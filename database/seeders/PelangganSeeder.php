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
                'nama_pelanggan' => 'Lans The Prodigy',
                'kode_pelanggan' => '6285156031385',
                'jenis_kode' => 'Phone',
            ],
            [
                'nama_pelanggan' => 'Akane Kurokawa',
                'kode_pelanggan' => 'lanstheprodigy@gmail.com',
                'jenis_kode' => 'Email',
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }
    }
}
