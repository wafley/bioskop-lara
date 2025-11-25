<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'label' => 'Admin',
                'redirect' => 'dashboard.index'
            ],
            [
                'name' => 'operator',
                'label' => 'Operator',
                'redirect' => 'dashboard.index'
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
