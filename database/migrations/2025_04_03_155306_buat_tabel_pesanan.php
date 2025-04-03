<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('total_akhir');
            $table->float('total_sementara');
            $table->float('diskon');
            $table->float('pajak');
            $table->text('deskripsi')->nullable();

            $table->uuid('pelanggan_id')->nullable();
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
