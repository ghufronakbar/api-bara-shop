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
            // Menghapus field peran yang ada
            $table->dropColumn('peran');

            // Menambahkan field peran_id yang menghubungkan ke tabel peran
            $table->uuid('peran_id')->nullable()->after('id');

            // Menambahkan foreign key constraint
            $table->foreign('peran_id')->references('id')->on('peran')->onDelete('set null');
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
            $table->string('peran')->nullable()->after('id');
        });
    }
};
