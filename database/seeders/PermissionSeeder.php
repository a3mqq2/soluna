<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // مسح الكاش
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // حذف جميع الصلاحيات القديمة
        DB::table('model_has_permissions')->delete();
        DB::table('role_has_permissions')->delete();
        Permission::query()->delete();

        // الصلاحيات الجديدة
        $permissions = [
            ['name' => 'invoices', 'name_ar' => 'المناسبات'],
            ['name' => 'treasury', 'name_ar' => 'الخزينة'],
            ['name' => 'services', 'name_ar' => 'الخدمات'],
            ['name' => 'customers', 'name_ar' => 'الزبائن'],
            ['name' => 'coupons', 'name_ar' => 'الكوبونات'],
            ['name' => 'users', 'name_ar' => 'المستخدمين'],
            ['name' => 'reports', 'name_ar' => 'التقارير'],
        ];

        foreach ($permissions as $item) {
            Permission::create([
                'name' => $item['name'],
                'name_ar' => $item['name_ar'],
                'guard_name' => 'web'
            ]);
        }

        // منح المستخدم الأول جميع الصلاحيات
        $user = User::first();
        if ($user) {
            $user->syncPermissions(collect($permissions)->pluck('name')->toArray());
        }
    }
}
