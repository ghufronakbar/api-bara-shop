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
            // Mengubah kolom peran_id agar tidak nullable
            $table->uuid('peran_id')->nullable(false)->change();

            // Menghapus kolom is_confirmed
            $table->dropColumn('is_confirmed');
        });
    }

    /**
     * Balikkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        // Jika migrasi dibalik, kita akan mengembalikan peran_id menjadi nullable dan menambah kembali is_confirmed
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('peran_id')->nullable()->change();
            $table->boolean('is_confirmed')->default(false)->after('peran_id');
        });
    }
};
