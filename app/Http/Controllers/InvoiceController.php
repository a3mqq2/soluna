<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\Treasury;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Invoice::query()->with('customer');

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

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->get('date_to'));
        }

        if ($request->get('view') === 'calendar') {
            $query->orderBy('invoice_date', 'asc');
            $allInvoices = $query->get();

            $invoicesGrouped = $allInvoices->groupBy(function ($invoice) {
                return \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d');
            })->map(function ($group) {
                return $group->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'customer_name' => $invoice->customer->name,
                        'total' => $invoice->total,
                        'status' => $invoice->status
                    ];
                })->toArray();
            })->toArray();

            $invoices = $allInvoices;
            $calendarData = $invoicesGrouped;
        } else {
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
                'delivery_date' => $validated['delivery_date'] ?? null,
                'discount' => $validated['discount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
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

            if (!empty($validated['paid_amount']) && $validated['paid_amount'] > 0) {
                $invoice->addPayment([
                    'amount' => $validated['paid_amount'],
                    'method' => 'cash',
                    'payment_date' => $validated['invoice_date'],
                    'status' => 'completed',
                ]);

                // 📌 إضافة معاملة في الخزنة
                $treasury = Treasury::first();
                if ($treasury) {
                    Transaction::create([
                        'treasury_id' => $treasury->id,
                        'type' => 'deposit',
                        'amount' => $validated['paid_amount'],
                        'description' => "دفعة من فاتورة #{$invoice->invoice_number}",
                        'invoice_id' => $invoice->id,
                    ]);
                    $treasury->increment('balance', $validated['paid_amount']);
                }
            }

            if (!empty($validated['coupon_id'])) {
                $coupon = Coupon::find($validated['coupon_id']);
                if ($coupon) {
                    $coupon->use();
                }
            }

            $invoice->calculateTotals();
        });

        return response()->json(['success' => true, 'message' => 'تم إنشاء الفاتورة بنجاح']);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.service']);
        if (request('print')) {
            return view('invoices.print', compact('invoice'));
        }
        return view('invoices.show', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
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

        DB::transaction(function () use ($invoice, $validated) {
            $oldPaid = $invoice->paid_amount;

            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'discount' => $validated['discount'] ?? 0,
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            $invoice->items()->delete();

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // 📌 تعديل المعاملات بالخزنة لو تغير المبلغ المدفوع
            if (isset($validated['paid_amount'])) {
                $diff = $validated['paid_amount'] - $oldPaid;
                if ($diff != 0) {
                    $treasury = Treasury::first();
                    if ($treasury) {
                        Transaction::create([
                            'treasury_id' => $treasury->id,
                            'type' => $diff > 0 ? 'deposit' : 'withdrawal',
                            'amount' => abs($diff),
                            'description' => "تعديل دفعة فاتورة #{$invoice->invoice_number}",
                            'invoice_id' => $invoice->id,
                        ]);

                        $diff > 0
                            ? $treasury->increment('balance', $diff)
                            : $treasury->decrement('balance', abs($diff));
                    }
                }
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
}
