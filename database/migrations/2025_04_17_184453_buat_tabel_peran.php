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
        Schema::create('peran', function (Blueprint $table) {
            $table->uuid('peran_id')->primary();
            $table->string('nama_peran');

            $table->boolean('akses_ringkasan')->default(false);
            $table->boolean('akses_laporan')->default(false);
            $table->boolean('akses_informasi')->default(false);
            $table->boolean('akses_kirim_pesan')->default(false);
            $table->boolean('akses_pengguna')->default(false);
            $table->boolean('akses_peran')->default(false);
            $table->boolean('akses_pelanggan')->default(false);
            $table->boolean('akses_produk')->default(false);
            $table->boolean('akses_pemasok')->default(false);
            $table->boolean('akses_riwayat_pesanan')->default(false);
            $table->boolean('akses_pembelian')->default(false);
            $table->boolean('akses_cacat_produk')->default(false);
            $table->boolean('akses_kasir')->default(false);

            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peran');
    }
};
