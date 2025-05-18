<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_produk', function (Blueprint $table) {
            $table->uuid('pembelian_produk_id')->primary();
            $table->float('jumlah_pembelian');
            $table->float('harga_per_barang');
            $table->float('total_harga');
            $table->text('deskripsi_pembelian')->nullable();

            $table->uuid('produk_id');
            $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');

            $table->uuid('pemasok_id');
            $table->foreign('pemasok_id')->references('pemasok_id')->on('pemasok')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_produk');
    }
};
