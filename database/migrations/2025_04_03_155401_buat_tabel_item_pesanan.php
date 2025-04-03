<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_pesanan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('jumlah');
            $table->float('harga');
            $table->float('total');

            $table->uuid('pesanan_id');
            $table->foreign('pesanan_id')->references('id')->on('pesanan')->onDelete('cascade');

            $table->uuid('produk_id');
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_pesanan');
    }
};
