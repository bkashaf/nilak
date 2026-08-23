<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::create([
            'name' => 'admin',
            'label' => 'مدیر کل',
        ]);

        $customer = Role::create([
            'name' => 'customer',
            'label' => 'مشتری',
        ]);

        $permissions = [
            'manage_users',
            'manage_products',
            'manage_orders',
            'manage_payments',
            'manage_content',
            'manage_settings',
        ];

        foreach ($permissions as $perm) {
            $p = Permission::create([
                'name' => $perm,
                'label' => $perm,
            ]);

            $admin->permissions()->attach($p->id);
        }
    }
}
