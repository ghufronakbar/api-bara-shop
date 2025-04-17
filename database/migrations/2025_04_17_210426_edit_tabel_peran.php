<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up()
    {
        // Mengubah tabel perans
        Schema::table('peran', function (Blueprint $table) {
            // Menghapus kolom yang tidak diperlukan
            $table->dropColumn([
                'kelola_informasi',
                'kelola_produk',
                'kelola_pembelian_produk',
                'kelola_cacat_produk',
                'kelola_pelanggan',
                'kelola_supplier',
                'semua_log_aktivitas',
                'kirim_pesan',
                'laporan',
            ]);

            // Menambahkan kolom baru dengan default false
            $table->boolean('ringkasan')->default(false);
            $table->boolean('informasi')->default(false);
            $table->boolean('pengguna')->default(false);
            $table->boolean('peran')->default(false);
            $table->boolean('pelanggan')->default(false);
            $table->boolean('produk')->default(false);
            $table->boolean('pemasok')->default(false);
            $table->boolean('riwayat_pesanan')->default(false);
            $table->boolean('pembelian')->default(false);
            $table->boolean('cacat_produk')->default(false);
            $table->boolean('kasir')->default(false);
        });
    }

    /**
     * Balikkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        // Jika migrasi dibalik, kita akan menghapus kolom baru dan menambah kolom lama
        Schema::table('peran', function (Blueprint $table) {
            // Menghapus kolom baru yang telah ditambahkan
            $table->dropColumn([
                'ringkasan',
                'informasi',
                'pengguna',
                'peran',
                'pelanggan',
                'produk',
                'pemasok',
                'riwayat_pesanan',
                'pembelian',
                'cacat_produk',
                'kasir',
            ]);

            // Menambahkan kolom lama kembali
            $table->boolean('kelola_informasi')->default(false);
            $table->boolean('kelola_produk')->default(false);
            $table->boolean('kelola_pembelian_produk')->default(false);
            $table->boolean('kelola_cacat_produk')->default(false);
            $table->boolean('kelola_pelanggan')->default(false);
            $table->boolean('kelola_supplier')->default(false);
            $table->boolean('semua_log_aktivitas')->default(false);
            $table->boolean('kirim_pesan')->default(false);
            $table->boolean('laporan')->default(false);
        });
    }
};
