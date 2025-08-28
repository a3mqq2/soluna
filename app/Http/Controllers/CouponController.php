<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Coupon::query();

        $filters = [
            'q' => $request->string('q')->toString(),
            'status' => $request->input('status', 'all'), // all | active | inactive | expired
            'type' => $request->input('type', 'all'), // all | fixed | percentage
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_dir' => $request->input('sort_dir', 'desc'),
            'per_page' => (int) $request->input('per_page', 15),
        ];

        // Search filter
        if ($filters['q']) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['q'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['q'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['q'] . '%');
            });
        }

        // Status filter
        if ($filters['status'] === 'active') {
            $query->active();
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        } elseif ($filters['status'] === 'expired') {
            $query->where('end_date', '<', now());
        }

        // Type filter
        if ($filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        // Sorting
        $allowedSorts = ['name', 'code', 'type', 'value', 'used_count', 'created_at', 'end_date'];
        if (!in_array($filters['sort_by'], $allowedSorts, true)) {
            $filters['sort_by'] = 'created_at';
        }
        $filters['sort_dir'] = strtolower($filters['sort_dir']) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($filters['sort_by'], $filters['sort_dir']);

        $allowedPerPage = [10, 15, 20, 25, 50];
        $perPage = in_array($filters['per_page'], $allowedPerPage, true) ? $filters['per_page'] : 15;

        $coupons = $query->paginate($perPage)->withQueryString();

        return view('coupons.index', compact('coupons', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coupons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:20', 'unique:coupons,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = Coupon::generateCode();
        } else {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        // Additional validation for percentage
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'النسبة المئوية يجب أن تكون أقل من أو تساوي 100%'])->withInput();
        }

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        $coupon = Coupon::create($validated);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'تم إنشاء الكوبون بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        if(request('print'))
        {
            return view('coupons.print', compact('coupon'));
        }
        return view('coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        return view('coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('coupons')->ignore($coupon)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));

        // Additional validation for percentage
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'النسبة المئوية يجب أن تكون أقل من أو تساوي 100%'])->withInput();
        }

        $validated['is_active'] = (bool) ($validated['is_active'] ?? $coupon->is_active);

        $coupon->update($validated);

        return redirect()
            ->route('coupons.show', $coupon)
            ->with('success', 'تم تحديث الكوبون بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        // Check if coupon has been used
        if ($coupon->used_count > 0) {
            return back()->with('error', 'لا يمكن حذف كوبون تم استخدامه من قبل');
        }

        $coupon->delete();

        return redirect()
            ->route('coupons.index')
            ->with('success', 'تم حذف الكوبون بنجاح');
    }

    /**
     * Toggle coupon status
     */
    public function toggle(Coupon $coupon)
    {
        $coupon->update([
            'is_active' => !$coupon->is_active
        ]);

        $status = $coupon->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        
        return back()->with('success', "تم {$status} الكوبون بنجاح");
    }

    /**
     * Validate coupon code via API
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'nullable|numeric|min:0'
        ]);

        $coupon = Coupon::findByCode($request->code);

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'كود الكوبون غير صحيح'
            ], 404);
        }

        $validation = $coupon->isValid($request->amount);

        if ($validation['valid']) {
            $discount = $coupon->calculateDiscount($request->amount ?? 0);
            
            return response()->json([
                'valid' => true,
                'message' => $validation['message'],
                'coupon' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'discount_amount' => $discount,
                    'formatted_discount' => $coupon->formatted_discount
                ]
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => $validation['message']
        ], 400);
    }

    /**
     * Generate a new coupon code
     */
    public function generateCode()
    {
        return response()->json([
            'code' => Coupon::generateCode()
        ]);
    }
}