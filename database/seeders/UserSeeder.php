<?php

namespace Database\Seeders;

use App\Models\Peran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        $check = User::count();
        $perans = Peran::all();
        if ($check > 0) {
            return;
        }
        $admin = Peran::where('nama_peran', 'Admin')->first();
        $owner = Peran::where('nama_peran', 'Owner')->first();
        $managerOperational = Peran::where('nama_peran', 'Manager Operational')->first();
        $cashier = Peran::where('nama_peran', 'Kasir')->first();
        $users = [
            [
                'nama_pengguna' => 'Owner Satu',
                'email' => 'owner@example.com',
                'password' => Hash::make('12345678'),
                'peran_id' => $owner->peran_id,
            ],
            [
                'nama_pengguna' => 'Admin Dua',
                'email' => 'admin@example.com',
                'password' => Hash::make('12345678'),
                'peran_id' => $admin->peran_id,
            ],
            [
                'nama_pengguna' => 'Manager Operasional',
                'email' => 'manager@example.com',
                'password' => Hash::make('12345678'),
                'peran_id' => $managerOperational->peran_id,
            ],
            [
                'nama_pengguna' => 'Kasir Empat',
                'email' => 'cashier@example.com',
                'password' => Hash::make('12345678'),
                'peran_id' => $cashier->peran_id,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
