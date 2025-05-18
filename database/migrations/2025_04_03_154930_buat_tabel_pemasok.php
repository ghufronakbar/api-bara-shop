<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasok', function (Blueprint $table) {
            $table->uuid('pemasok_id')->primary();
            $table->string('nama_pemasok');
            $table->text('alamat_pemasok');
            $table->string('telepon_pemasok');
            $table->string('logo_pemasok')->nullable();

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasok');
    }
};
