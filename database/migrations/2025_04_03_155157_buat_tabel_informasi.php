<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informasi', function (Blueprint $table) {
            $table->uuid('informasi_id')->primary();
            $table->float('persentase_pajak');
            $table->float('persentase_diskon');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasi');
    }
};
