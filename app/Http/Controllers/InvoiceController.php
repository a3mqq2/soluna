<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Customer;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['customer', 'items.service'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::select('id', 'name', 'phone')->get();
        $services = Service::where('is_active', true)->select('id', 'name', 'price')->get();
        
        return view('invoices.create', compact('customers', 'services'));
    }


    public function edit(Invoice $invoice)
    {
        $invoice->load(['items', 'customer']);
        $customers = Customer::select('id', 'name', 'phone')->get();
        $services = Service::where('is_active', true)->select('id', 'name', 'price')->get();
        
        return view('invoices.edit', compact('invoice', 'customers', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'coupon_id' => 'nullable|exists:coupons,id',
            'coupon_code' => 'nullable|string',
            'coupon_discount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'discount' => $validated['discount'] ?? 0,
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'notes' => $validated['notes'],
                'user_id' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }


            if ($validated['coupon_id']) {
                $coupon = Coupon::find($validated['coupon_id']);
                $coupon->use(); 
            }
            
            $invoice->calculateTotals();
        });

        return response()->json(['success' => true, 'message' => 'تم إنشاء الفاتورة بنجاح']);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.service']);
        if(request('print'))
        {
            return view('invoices.print', compact('invoice'));
        }
        return view('invoices.show', compact('invoice'));
    }

    // API endpoints for Vue component
    public function getCustomers()
    {
        return Customer::select('id', 'name', 'phone')->get();
    }

    public function getServices()
    {
        return Service::where('is_active', true)
            ->select('id', 'name', 'price')
            ->get();
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);
        
        return response()->json([
            'success' => true,
            'customer' => $customer->only(['id', 'name', 'phone'])
        ]);
    }

    public function getNextInvoiceNumber()
    {
        $lastInvoice = Invoice::latest('id')->first();
        $nextNumber = $lastInvoice ? ($lastInvoice->id + 1) : 1;
        
        return response()->json(['next_number' => $nextNumber]);
    }


    public function searchCustomers(Request $request)
    {
        $searchTerm = $request->get('q', '');
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        
        if (strlen($searchTerm) < 3) {
            return response()->json([
                'data' => [],
                'has_more_pages' => false
            ]);
        }
        
        $query = Customer::where(function($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
        })
        ->select('id', 'name', 'phone')
        ->orderBy('name');
        
        $customers = $query->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'data' => $customers->items(),
            'has_more_pages' => $customers->hasMorePages(),
            'total' => $customers->total()
        ]);
    }

    public function showApi(Invoice $invoice)
    {
        $invoice->load(['items', 'customer']);

        $subtotal = (float) $invoice->items->sum('total_price');

        return response()->json([
            'id'             => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'customer_id'    => $invoice->customer_id,
            'customer'       => $invoice->customer ? [
                'id'    => $invoice->customer->id,
                'name'  => $invoice->customer->name,
                'phone' => $invoice->customer->phone,
            ] : null,
            'invoice_date'   => optional($invoice->invoice_date)->toDateString(),
            'discount'       => (float) $invoice->discount,
            'paid_amount'    => (float) $invoice->paid_amount,
            'notes'          => $invoice->notes,
            'items'          => $invoice->items->map(fn ($it) => [
                'service_id' => $it->service_id,
                'quantity'   => (int) $it->quantity,
                'unit_price' => (float) $it->unit_price,
                'total_price'=> (float) $it->total_price,
            ])->values(),
            'coupon'         => null,
            'subtotal'       => $subtotal,
            'total'          => (float) $invoice->total,
            'remaining'      => max(0, (float) $invoice->total - (float) $invoice->paid_amount),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'invoice_date'         => 'required|date',
            'discount'             => 'nullable|numeric|min:0',
            'paid_amount'          => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.service_id'   => 'required|exists:services,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'coupon_id'            => 'nullable|exists:coupons,id',
            'coupon_code'          => 'nullable|string',
            'coupon_discount'      => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $invoice->update([
                'customer_id'   => $validated['customer_id'],
                'invoice_date'  => $validated['invoice_date'],
                'discount'      => $validated['discount'] ?? 0,
                'paid_amount'   => $validated['paid_amount'] ?? 0,
                'notes'         => $validated['notes'] ?? null,
            ]);

            $invoice->items()->delete();

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            if (!empty($validated['coupon_id'])) {
                $coupon = Coupon::find($validated['coupon_id']);
                if ($coupon) {
                    $coupon->use();
                }
            }

            $invoice->calculateTotals();
        });

        return response()->json(['success' => true, 'message' => 'تم تحديث الفاتورة بنجاح']);
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            // احذف المدفوعات أولاً ثم العناصر ثم الفاتورة
            $invoice->payments()->delete();
            $invoice->items()->delete();
            $invoice->delete();
        });
    
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تم حذف الفاتورة بنجاح']);
        }
    
        return redirect()->route('invoices.index')->with('success', 'تم حذف الفاتورة بنجاح.');
    }    
}