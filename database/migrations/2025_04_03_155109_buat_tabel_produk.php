<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->uuid('produk_id')->primary();
            $table->string('nama_produk');
            $table->float('harga_produk');
            $table->float('jumlah_stok');
            $table->float('hpp')->default(0);
            $table->string('kategori_produk');
            $table->text('deskripsi_produk')->nullable();
            $table->string('foto_produk')->nullable();

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
