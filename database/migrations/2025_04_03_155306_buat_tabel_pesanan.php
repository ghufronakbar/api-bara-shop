<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->uuid('pesanan_id')->primary();
            $table->float('total_akhir');
            $table->float('total_harga_barang');
            $table->float('diskon_dikenakan');
            $table->float('pajak_dikenakan');
            $table->float('persentase_diskon', 8, 2)->default(0);
            $table->float('persentase_pajak', 8, 2)->default(0);
            $table->text('deskripsi_pesanan')->nullable();

            $table->uuid('pelanggan_id')->nullable();
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
