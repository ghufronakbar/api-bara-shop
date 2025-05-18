<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pesan_terkirim_pelanggan', function (Blueprint $table) {
            $table->uuid('pesan_terkirim_pelanggan_id')->primary();
            $table->uuid('pesan_terkirim_id');
            $table->uuid('pelanggan_id');
            $table->foreign('pesan_terkirim_id')->references('pesan_terkirim_id')->on('pesan_terkirim')->onDelete('cascade');
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pesan_pelanggan');
    }
};
