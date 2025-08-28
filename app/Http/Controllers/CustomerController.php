<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource with filters and pagination.
     */
    public function index(Request $request)
    {
        $query = Customer::query()
            ->select('customers.*')
            ->selectSub(function ($q) {
                $q->from('invoices as i')
                    ->selectRaw("
                        COALESCE(
                            SUM(
                                CASE
                                    WHEN (COALESCE(i.total, 0) - COALESCE(i.paid_amount, 0)) > 0
                                        THEN (COALESCE(i.total, 0) - COALESCE(i.paid_amount, 0))
                                    ELSE 0
                                END
                            ), 0
                        )
                    ")
                    ->whereColumn('i.customer_id', 'customers.id');
            }, 'receivable');

        $filters = [
            'q'        => $request->string('q')->toString(),
            'sort_by'  => $request->input('sort_by', 'created_at'),
            'sort_dir' => $request->input('sort_dir', 'desc'),
            'per_page' => (int) $request->input('per_page', 15),
        ];

        if ($filters['q']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['q'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['q'] . '%');
            });
        }

        $allowedSorts = ['name', 'phone', 'created_at', 'updated_at', 'receivable'];
        if (! in_array($filters['sort_by'], $allowedSorts, true)) {
            $filters['sort_by'] = 'created_at';
        }
        $filters['sort_dir'] = strtolower($filters['sort_dir']) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($filters['sort_by'], $filters['sort_dir']);

        $allowedPerPage = [10, 15, 20, 25, 50, 100];
        $perPage = in_array($filters['per_page'], $allowedPerPage, true) ? $filters['per_page'] : 15;

        $customers = $query->paginate($perPage)->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'filters'   => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer = Customer::create($data);

        return redirect()
            ->route('customers.index')
            ->with('success', 'تم إنشاء الزبون بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer->update($data);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'تم تحديث الزبون بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'تم حذف الزبون بنجاح.');
    }

    /**
     * Bulk actions: delete.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:delete'],
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['integer', 'exists:customers,id'],
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];

        DB::transaction(function () use ($ids, $action) {
            if ($action === 'delete') {
                Customer::whereIn('id', $ids)->delete();
            }
        });

        return back()->with('success', 'تم تنفيذ العملية بنجاح.');
    }
}
