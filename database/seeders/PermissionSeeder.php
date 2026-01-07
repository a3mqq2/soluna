<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // المستخدمين
            ['name' => 'users.view',    'name_ar' => 'عرض المستخدمين'],
            ['name' => 'users.create',  'name_ar' => 'إضافة مستخدم'],
            ['name' => 'users.edit',    'name_ar' => 'تعديل مستخدم'],
            ['name' => 'users.delete',  'name_ar' => 'حذف مستخدم'],

            // المناسبات (الفواتير)
            ['name' => 'invoices.view',   'name_ar' => 'عرض المناسبات'],
            ['name' => 'invoices.create', 'name_ar' => 'إنشاء مناسبة'],
            ['name' => 'invoices.edit',   'name_ar' => 'تعديل مناسبة'],
            ['name' => 'invoices.delete', 'name_ar' => 'حذف مناسبة'],

            // الخزينة والمعاملات
            ['name' => 'treasury.view',   'name_ar' => 'عرض الخزينة'],
            ['name' => 'treasury.create', 'name_ar' => 'إضافة معاملة'],
            ['name' => 'treasury.edit',   'name_ar' => 'تعديل معاملة'],
            ['name' => 'treasury.delete', 'name_ar' => 'حذف معاملة'],

            // التقارير
            ['name' => 'reports.view', 'name_ar' => 'عرض التقارير'],

            // الخدمات
            ['name' => 'services.view',   'name_ar' => 'عرض الخدمات'],
            ['name' => 'services.create', 'name_ar' => 'إضافة خدمة'],
            ['name' => 'services.edit',   'name_ar' => 'تعديل خدمة'],
            ['name' => 'services.delete', 'name_ar' => 'حذف خدمة'],

            // الزبائن
            ['name' => 'customers.view',   'name_ar' => 'عرض الزبائن'],
            ['name' => 'customers.create', 'name_ar' => 'إضافة زبون'],
            ['name' => 'customers.edit',   'name_ar' => 'تعديل زبون'],
            ['name' => 'customers.delete', 'name_ar' => 'حذف زبون'],

            // الكوبونات
            ['name' => 'coupons.view',   'name_ar' => 'عرض الكوبونات'],
            ['name' => 'coupons.create', 'name_ar' => 'إضافة كوبون'],
            ['name' => 'coupons.edit',   'name_ar' => 'تعديل كوبون'],
            ['name' => 'coupons.delete', 'name_ar' => 'حذف كوبون'],
        ];

        foreach ($permissions as $item) {
            Permission::firstOrCreate(
                ['name' => $item['name']],
                ['name_ar' => $item['name_ar'], 'guard_name' => 'web']
            );
        }

        // منح المستخدم الأول جميع الصلاحيات
        $user = User::first();
        if ($user) {
            $user->syncPermissions(collect($permissions)->pluck('name')->toArray());
        }
    }
}
