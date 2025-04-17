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
                'nama' => 'Admin',
                'kelola_informasi' => true,
                'kelola_produk' => true,
                'kelola_pembelian_produk' => true,
                'kelola_cacat_produk' => true,
                'kelola_pelanggan' => true,
                'kelola_supplier' => true,
                'semua_log_aktivitas' => true,
                'kirim_pesan' => true,
                'laporan' => true,
                'is_deleted' => false,
            ],
            [
                'nama' => 'Owner',
                'kelola_informasi' => true,
                'kelola_produk' => true,
                'kelola_pembelian_produk' => true,
                'kelola_cacat_produk' => true,
                'kelola_pelanggan' => true,
                'kelola_supplier' => true,
                'semua_log_aktivitas' => true,
                'kirim_pesan' => true,
                'laporan' => true,
                'is_deleted' => false,
            ],
            [
                'nama' => 'Manager Operational',
                'kelola_informasi' => true,
                'kelola_produk' => true,
                'kelola_pembelian_produk' => true,
                'kelola_cacat_produk' => false,
                'kelola_pelanggan' => true,
                'kelola_supplier' => true,
                'semua_log_aktivitas' => false,
                'kirim_pesan' => true,
                'laporan' => true,
                'is_deleted' => false,
            ],
            [
                'nama' => 'Kasir',
                'kelola_informasi' => false,
                'kelola_produk' => false,
                'kelola_pembelian_produk' => false,
                'kelola_cacat_produk' => false,
                'kelola_pelanggan' => false,
                'kelola_supplier' => false,
                'semua_log_aktivitas' => false,
                'kirim_pesan' => true,
                'laporan' => false,
                'is_deleted' => false,
            ],
        ];

        // Melakukan seed pada tabel peran
        foreach ($perans as $peran) {
            Peran::create($peran);
        }
    }
}
