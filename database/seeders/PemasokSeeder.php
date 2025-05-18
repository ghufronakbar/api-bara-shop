<?php

namespace Database\Seeders;

use App\Models\Pemasok;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PemasokSeeder extends Seeder
{
    public function run(): void
    {

        $check = Pemasok::count();
        if ($check > 0) {
            return;
        }
        $pemasoks = [
            [
                'nama_pemasok' => 'SRC Jayakarta',
                'alamat_pemasok' => 'Jakarta Selatan',
                'telepon_pemasok' => '6285156031385',
            ],
            [
                'nama_pemasok' => 'SRC Bandung',
                'alamat_pemasok' => 'Bandung',
                'telepon_pemasok' => '6285156031386',
            ],
            [
                'nama_pemasok' => 'SRC Yogya',
                'alamat_pemasok' => 'Yogyakarta',
                'telepon_pemasok' => '6285156031387',
            ],
            [
                'nama_pemasok' => 'SRC Surabaya',
                'alamat_pemasok' => 'Surabaya',
                'telepon_pemasok' => '6285156031388',
            ],
        ];

        foreach ($pemasoks as $pemasok) {
            Pemasok::create($pemasok);
        }
    }
}
