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
        // Mengubah tabel users
        Schema::table('users', function (Blueprint $table) {

            $table->uuid('peran_id')->after('user_id');

            $table->foreign('peran_id')->references('peran_id')->on('peran')->onDelete('cascade');
        });
    }

    /**
     * Balikkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        // Jika migrasi dibalik, kita akan menghapus peran_id dan mengembalikan peran ke field lama
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['peran_id']);
            $table->dropColumn('peran_id');
        });
    }
};
