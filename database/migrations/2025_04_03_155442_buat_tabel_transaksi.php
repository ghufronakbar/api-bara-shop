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
            $table->float('jumlah_pembayaran');
            $table->enum('metode_pembayaran', ['Cash', 'VirtualAccountOrBank']);
            $table->enum('status_pembayaran', ['Pending', 'Success']);
            $table->json('detail_transaksi')->nullable();

            $table->string('midtrans_snap_token')->nullable();
            $table->string('midtrans_url_redirect')->nullable();

            $table->uuid('pesanan_id')->unique();
            $table->foreign('pesanan_id')->references('pesanan_id')->on('pesanan')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
