<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Make the 'detail' field nullable and its type JSON
            $table->json('detail')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Revert the 'detail' column to be non-nullable
            $table->json('detail')->nullable(false)->change();
        });
    }
};
