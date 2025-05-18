<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesan_terkirim', function (Blueprint $table) {
            $table->uuid('pesan_terkirim_id')->primary();
            $table->string('subjek_pesan');
            $table->text('isi_pesan');

            $table->uuid('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            $table->boolean('is_deleted')->default(false);
            $table->timestamps(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan_terkirim');
    }
};
