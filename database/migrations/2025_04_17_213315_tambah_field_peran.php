<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up()
    {
        // Menambahkan kolom laporan dan kirim_pesan
        Schema::table('peran', function (Blueprint $table) {
            $table->boolean('laporan')->default(false);
            $table->boolean('kirim_pesan')->default(false);
        });
    }

    /**
     * Balikkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        // Menghapus kolom laporan dan kirim_pesan
        Schema::table('peran', function (Blueprint $table) {
            $table->dropColumn('laporan');
            $table->dropColumn('kirim_pesan');
        });
    }
};
