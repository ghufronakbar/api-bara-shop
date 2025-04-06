<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->float('persentase_diskon', 8, 2)->default(0);
            $table->float('persentase_pajak', 8, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['persentase_diskon', 'persentase_pajak']);
        });
    }
};
