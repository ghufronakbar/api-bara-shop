<?php

namespace Database\Seeders;

use App\Models\Informasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InformasiSeeder extends Seeder
{
    public function run(): void
    {

        $check = Informasi::count();
        if ($check > 0) {
            return;
        }
        $users = [
            [
                'persentase_pajak' => 12,
                'persentase_diskon' => 10,
            ],
        ];

        foreach ($users as $user) {
            Informasi::create($user);
        }
    }
}
