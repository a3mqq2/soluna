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
            ['name' => 'users',             'name_ar' => 'المستخدمين'],
        ];

        foreach ($permissions as $item) {
            Permission::firstOrCreate(
                ['name' => $item['name']],
                ['name_ar' => $item['name_ar'], 'guard_name' => 'web']
            );
        }

        $user = User::first();
        if ($user) {
            $user->givePermissionTo(collect($permissions)->pluck('name')->toArray());
        }
    }
}
