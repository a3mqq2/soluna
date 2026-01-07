<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active');
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->latest();
                break;
        }

        $users = $query->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('users.create', compact('permissions'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'sometimes|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        // ربط الصلاحيات
        $user->syncPermissions($request->input('permissions', []));


        return redirect()->route('users.index')
                        ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'permissions']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $permissions = Permission::all();
        $user->load(['permissions']);
        return view('users.edit', compact('user', 'permissions'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'sometimes|boolean',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->has('is_active') ? $request->is_active : false,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // تحديث الصلاحيات
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('users.index')
                        ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                            ->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id() && $user->is_active) {
            return redirect()->route('users.index')
                            ->with('error', 'لا يمكنك إلغاء تفعيل حسابك الخاص');
        }

        $user->toggleStatus();

        $message = $user->is_active ? 'تم تفعيل المستخدم بنجاح' : 'تم إلغاء تفعيل المستخدم بنجاح';

        return redirect()->route('users.index')
                        ->with('success', $message);
    }

    /**
     * Bulk actions for users.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $userIds = $request->users;
        $currentUserId = auth()->id();

        switch ($request->action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = 'تم تفعيل المستخدمين المحددين بنجاح';
                break;

            case 'deactivate':
                $filteredUserIds = array_diff($userIds, [$currentUserId]);
                User::whereIn('id', $filteredUserIds)->update(['is_active' => false]);
                $message = 'تم إلغاء تفعيل المستخدمين المحددين بنجاح';
                break;

            case 'delete':
                $filteredUserIds = array_diff($userIds, [$currentUserId]);
                User::whereIn('id', $filteredUserIds)->delete();
                $message = 'تم حذف المستخدمين المحددين بنجاح';
                break;
        }

        return redirect()->route('users.index')
                        ->with('success', $message);
    }

}