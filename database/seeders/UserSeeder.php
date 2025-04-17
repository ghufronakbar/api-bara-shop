<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        $check = User::count();
        if ($check > 0) {
            return;
        }
        $users = [
            [
                'nama' => 'Owner Satu',
                'email' => 'owner@example.com',
                'password' => Hash::make('12345678'),
                'peran' => 'Owner',
            ],
            [
                'nama' => 'Admin Dua',
                'email' => 'admin@example.com',
                'password' => Hash::make('12345678'),
                'peran' => 'Admin',
            ],
            [
                'nama' => 'Manager Operasional',
                'email' => 'manager@example.com',
                'password' => Hash::make('12345678'),
                'peran' => 'ManagerOperational',
            ],
            [
                'nama' => 'Kasir Empat',
                'email' => 'cashier@example.com',
                'password' => Hash::make('12345678'),
                'peran' => 'Cashier',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
