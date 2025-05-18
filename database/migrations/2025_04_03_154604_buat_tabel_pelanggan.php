<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->uuid('pelanggan_id')->primary();
            $table->string('nama_pelanggan');
            $table->string('kode_pelanggan')->unique();
            $table->enum('jenis_kode', ['Email', 'Phone']);

            $table->boolean('is_deleted')->default(false);

            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
