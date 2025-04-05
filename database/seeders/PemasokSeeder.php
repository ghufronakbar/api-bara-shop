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
                'nama' => 'SRC Jayakarta',
                'alamat' => 'Jakarta Selatan',
                'telepon' => '6285156031385',
            ],
            [
                'nama' => 'SRC Bandung',
                'alamat' => 'Bandung',
                'telepon' => '6285156031386',
            ],
            [
                'nama' => 'SRC Yogya',
                'alamat' => 'Yogyakarta',
                'telepon' => '6285156031387',
            ],
            [
                'nama' => 'SRC Surabaya',
                'alamat' => 'Surabaya',
                'telepon' => '6285156031388',
            ],
        ];

        foreach ($pemasoks as $pemasok) {
            Pemasok::create($pemasok);
        }
    }
}
