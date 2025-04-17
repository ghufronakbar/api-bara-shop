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
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->boolean('kelola_informasi')->default(false);
            $table->boolean('kelola_produk')->default(false);
            $table->boolean('kelola_pembelian_produk')->default(false);
            $table->boolean('kelola_cacat_produk')->default(false);
            $table->boolean('kelola_pelanggan')->default(false);
            $table->boolean('kelola_supplier')->default(false);
            $table->boolean('semua_log_aktivitas')->default(false);
            $table->boolean('kirim_pesan')->default(false);
            $table->boolean('laporan')->default(false);
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
