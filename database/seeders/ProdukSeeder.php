<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {

        $check = Produk::count();
        if ($check > 0) {
            return;
        }
        $produks = [
            [
                'nama' => 'Bunga Anggrek',
                'harga' => 14000,
                'jumlah' => 0,
                'hpp' => 0,
                'kategori' => "Bunga",
                'deskripsi' => "Hana Kirei",
            ],
            [
                'nama' => 'Bunga Mawar',
                'harga' => 15000,
                'jumlah' => 0,
                'hpp' => 0,
                'kategori' => "Bunga",
                'deskripsi' => "Hana Kirei",
            ],
            [
                'nama' => 'Bunga Tulip',
                'harga' => 16000,
                'jumlah' => 0,
                'hpp' => 0,
                'kategori' => "Bunga",
                'deskripsi' => "Hana Kirei",
            ],
            [
                'nama' => 'Paket Natal',
                'harga' => 85000,
                'jumlah' => 0,
                'hpp' => 0,
                'kategori' => "Paket",
                'deskripsi' => "Ekspresi Natal",
            ],
            [
                'nama' => 'Paket Tahun Baru',
                'harga' => 90000,
                'jumlah' => 0,
                'hpp' => 0,
                'kategori' => "Paket",
                'deskripsi' => "Ekspresi Tahun Baru",
            ],
            [
                'nama' => 'Paket Ulang Tahun',
                'harga' => 80000,
                'jumlah' => 0,
                'hpp' => 0,
                'kategori' => "Paket",
                'deskripsi' => "Ekspresi Ulang Tahun",
            ],
        ];

        foreach ($produks as $produk) {
            Produk::create($produk);
        }
    }
}
