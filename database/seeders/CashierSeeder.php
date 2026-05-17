<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'cashier')->first();

        if (!$role) {
            throw new \Exception('Role cashier tidak ditemukan');
        }

        $totalActive = 216;
        $totalInactive = 58;
        $total = $totalActive + $totalInactive;

        $now = now();
        $hashedPassword = bcrypt('password');

        $data = [];

        for ($i = 1; $i <= $total; $i++) {
            $data[] = [
                'name' => 'Cashier ' . $i,
                'username' => 'cashier' . $i,
                'password' => $hashedPassword,
                'role_id' => $role->id,
                'status' => $i <= $totalActive,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($data, 500) as $chunk) {
            User::insert($chunk);
        }
    }
}
