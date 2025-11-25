<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::whereIn('name', ['admin', 'operator'])
            ->pluck('id', 'name');

        $users = [
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role_id' => $roles['admin'] ?? null,
                'status' => true,
            ],
            [
                'name' => 'Winandi',
                'username' => 'winandi',
                'password' => Hash::make('winandi'),
                'role_id' => $roles['operator'] ?? null,
                'status' => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                $user
            );
        }
    }
}
