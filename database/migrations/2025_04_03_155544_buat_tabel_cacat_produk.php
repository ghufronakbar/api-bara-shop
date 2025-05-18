<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cacat_produk', function (Blueprint $table) {
            $table->uuid('cacat_produk_id')->primary();
            $table->integer('jumlah_produk');
            $table->string('alasan_kerusakan');

            $table->uuid('produk_id');
            $table->foreign('produk_id')->references('produk_id')->on('produk')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cacat_produk');
    }
};
