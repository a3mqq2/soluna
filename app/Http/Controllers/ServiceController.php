<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource with filters and pagination.
     */
    public function index(Request $request)
    {
        $query = Service::query();

        $filters = [
            'q'         => $request->string('q')->toString(),
            'status'    => $request->input('status', 'all'), // all | active | inactive
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'sort_by'   => $request->input('sort_by', 'created_at'),
            'sort_dir'  => $request->input('sort_dir', 'desc'),
            'per_page'  => (int) $request->input('per_page', 15),
        ];

        if ($filters['q']) {
            $query->where('name', 'like', '%' . $filters['q'] . '%');
        }

        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        if ($filters['min_price'] !== null && $filters['min_price'] !== '') {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if ($filters['max_price'] !== null && $filters['max_price'] !== '') {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        $allowedSorts = ['name', 'price', 'created_at', 'updated_at'];
        if (! in_array($filters['sort_by'], $allowedSorts, true)) {
            $filters['sort_by'] = 'created_at';
        }
        $filters['sort_dir'] = strtolower($filters['sort_dir']) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($filters['sort_by'], $filters['sort_dir']);

        $allowedPerPage = [10, 15, 20, 25, 50, 100];
        $perPage = in_array($filters['per_page'], $allowedPerPage, true) ? $filters['per_page'] : 15;

        $services = $query->paginate($perPage)->withQueryString();

        return view('services.index', [
            'services' => $services,
            'filters'  => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'price'     => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $service = Service::create($data);

        return redirect()
            ->route('services.index')
            ->with('success', 'تم إنشاء الخدمة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'price'     => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? $service->is_active);

        $service->update($data);

        return redirect()
            ->route('services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', 'تم حذف الخدمة بنجاح.');
    }

    /**
     * Toggle is_active status.
     */
    public function toggle(Service $service)
    {
        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        return back()->with('success', 'تم تغيير حالة الخدمة.');
    }

    /**
     * Bulk actions: activate, deactivate, delete.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['integer', 'exists:services,id'],
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];

        DB::transaction(function () use ($ids, $action) {
            if ($action === 'activate') {
                Service::whereIn('id', $ids)->update(['is_active' => true]);
            } elseif ($action === 'deactivate') {
                Service::whereIn('id', $ids)->update(['is_active' => false]);
            } elseif ($action === 'delete') {
                Service::whereIn('id', $ids)->delete();
            }
        });

        return back()->with('success', 'تم تنفيذ العملية بنجاح.');
    }
}
