<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->float('harga');
            $table->float('jumlah');
            $table->float('hpp')->default(0);
            $table->string('kategori');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
