<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('deskripsi');
            $table->json('detail');
            $table->uuid('referensi_id');
            $table->string('model_referensi');
            $table->enum('aksi', ['Create', 'Update', 'Delete']);

            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aksi');
    }
};
