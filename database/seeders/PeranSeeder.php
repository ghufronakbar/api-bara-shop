<?php

namespace Database\Seeders;

use App\Models\Peran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PeranSeeder extends Seeder
{
    public function run(): void
    {
        // Cek jika data sudah ada, jika sudah ada tidak perlu melakukan seeding lagi
        $check = Peran::count();
        if ($check > 0) {
            return;
        }

        // Data peran yang akan di-seed
        $perans = [
            [
                'nama_peran' => 'Admin',
                'akses_ringkasan' => true,
                'akses_laporan' => true,
                'akses_informasi' => true,
                'akses_kirim_pesan' => true,
                'akses_pengguna' => true,
                'akses_peran' => true,
                'akses_pelanggan' => true,
                'akses_produk' => true,
                'akses_pemasok' => true,
                'akses_riwayat_pesanan' => true,
                'akses_pembelian' => true,
                'akses_cacat_produk' => true,
                'akses_kasir' => true,
                'is_deleted' => false,
            ],
            [
                'nama_peran' => 'Owner',
                'akses_ringkasan' => true,
                'akses_laporan' => true,
                'akses_informasi' => true,
                'akses_kirim_pesan' => true,
                'akses_pengguna' => true,
                'akses_peran' => true,
                'akses_pelanggan' => true,
                'akses_produk' => true,
                'akses_pemasok' => true,
                'akses_riwayat_pesanan' => true,
                'akses_pembelian' => true,
                'akses_cacat_produk' => true,
                'akses_kasir' => true,
                'is_deleted' => false,
            ],
            [
                'nama_peran' => 'Manager Operational',
                'akses_ringkasan' => false,
                'akses_laporan' => false,
                'akses_informasi' => true,
                'akses_kirim_pesan' => true,
                'akses_pengguna' => false,
                'akses_peran' => false,
                'akses_pelanggan' => true,
                'akses_produk' => true,
                'akses_pemasok' => true,
                'akses_riwayat_pesanan' => false,
                'akses_pembelian' => true,
                'akses_cacat_produk' => false,
                'akses_kasir' => true,
                'is_deleted' => false,
            ],
            [
                'nama_peran' => 'Kasir',
                'akses_ringkasan' => false,
                'akses_laporan' => false,
                'akses_informasi' => false,
                'akses_kirim_pesan' => true,
                'akses_pengguna' => false,
                'akses_peran' => false,
                'akses_pelanggan' => false,
                'akses_produk' => false,
                'akses_pemasok' => false,
                'akses_riwayat_pesanan' => false,
                'akses_pembelian' => false,
                'akses_cacat_produk' => false,
                'akses_kasir' => true,
                'is_deleted' => false,
            ],
        ];

        // Melakukan seed pada tabel peran
        foreach ($perans as $peran) {
            Peran::create($peran);
        }
    }
}

