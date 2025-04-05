<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {

        $check = Pelanggan::count();
        if ($check > 0) {
            return;
        }
        $pelanggans = [
            [
                'nama' => 'Lans The Prodigy',
                'kode' => '6285156031385',
                'jenis_kode' => 'Phone',
            ],
            [
                'nama' => 'Akane Kurokawa',
                'kode' => 'lanstheprodigy@gmail.com',
                'jenis_kode' => 'Email',
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }
    }
}
