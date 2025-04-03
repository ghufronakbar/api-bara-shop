<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('jumlah');
            $table->enum('metode', ['Cash', 'VirtualAccountOrBank']);
            $table->enum('status', ['Pending', 'Success']);
            $table->json('detail');

            $table->string('snap_token')->nullable();
            $table->string('url_redirect')->nullable();

            $table->uuid('pesanan_id')->unique();
            $table->foreign('pesanan_id')->references('id')->on('pesanan')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
