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
                'nama_produk' => 'Bunga Anggrek',
                'harga_produk' => 14000,
                'jumlah_stok' => 0,
                'hpp' => 0,
                'kategori_produk' => "Bunga",
                'deskripsi_produk' => "Hana Kirei",
            ],
            [
                'nama_produk' => 'Bunga Mawar',
                'harga_produk' => 15000,
                'jumlah_stok' => 0,
                'hpp' => 0,
                'kategori_produk' => "Bunga",
                'deskripsi_produk' => "Hana Kirei",
            ],
            [
                'nama_produk' => 'Bunga Tulip',
                'harga_produk' => 16000,
                'jumlah_stok' => 0,
                'hpp' => 0,
                'kategori_produk' => "Bunga",
                'deskripsi_produk' => "Hana Kirei",
            ],
            [
                'nama_produk' => 'Paket Natal',
                'harga_produk' => 85000,
                'jumlah_stok' => 0,
                'hpp' => 0,
                'kategori_produk' => "Paket",
                'deskripsi_produk' => "Ekspresi Natal",
            ],
            [
                'nama_produk' => 'Paket Tahun Baru',
                'harga_produk' => 90000,
                'jumlah_stok' => 0,
                'hpp' => 0,
                'kategori_produk' => "Paket",
                'deskripsi_produk' => "Ekspresi Tahun Baru",
            ],
            [
                'nama_produk' => 'Paket Ulang Tahun',
                'harga_produk' => 80000,
                'jumlah_stok' => 0,
                'hpp' => 0,
                'kategori_produk' => "Paket",
                'deskripsi_produk' => "Ekspresi Ulang Tahun",
            ],
        ];

        foreach ($produks as $produk) {
            Produk::create($produk);
        }
    }
}

