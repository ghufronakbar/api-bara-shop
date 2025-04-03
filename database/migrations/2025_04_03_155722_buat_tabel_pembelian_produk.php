<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('jumlah');
            $table->float('harga');
            $table->float('total');
            $table->text('deskripsi')->nullable();

            $table->uuid('produk_id');
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');

            $table->uuid('pemasok_id');
            $table->foreign('pemasok_id')->references('id')->on('pemasok')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_produk');
    }
};
