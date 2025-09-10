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
    public function index(Request $request)
    {
        $query = \App\Models\Invoice::query()->with('customer');
    
        // 🔍 البحث
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }
    
        // ✅ الفلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
    
        // 📅 فلترة التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->get('date_to'));
        }
    
        // Handle different views
        if ($request->get('view') === 'calendar') {
            // For calendar view, get all invoices and group by date
            $query->orderBy('invoice_date', 'asc');
            $allInvoices = $query->get();
            
            // Group invoices by date for calendar
            $invoicesGrouped = $allInvoices->groupBy(function($invoice) {
                return \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d');
            })->map(function($group) {
                return $group->map(function($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'customer_name' => $invoice->customer->name,
                        'total' => $invoice->total,
                        'status' => $invoice->status
                    ];
                })->toArray();
            })->toArray();
            
            $invoices = $allInvoices; // For compatibility
            $calendarData = $invoicesGrouped;
        } else {
            // For table view, use pagination
            $query->orderBy('invoice_date', 'desc');
            $invoices = $query->paginate(10);
            $calendarData = [];
        }
    
        return view('invoices.index', compact('invoices', 'calendarData'));
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
            'delivery_date' => "nullable|date",
        ]);

        DB::transaction(function () use ($validated) {
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'delivery_date' => $validated['delivery_date'],
                'discount' => $validated['discount'] ?? 0,
                'notes' => $validated['notes'],
                'user_id' => auth()->id() ?? 1,
            ]);
        
            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }
        
            if ($validated['paid_amount'] > 0) {
                $invoice->addPayment([
                    'amount' => $validated['paid_amount'],
                    'method' => 'cash',
                    'payment_date' => $validated['invoice_date'],
                    'status' => 'completed',
                    'notes' => 'دفعة بعد التحديث',
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