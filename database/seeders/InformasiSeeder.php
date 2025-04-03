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
                'pajak' => 12,
                'diskon' => 10,
            ],
        ];

        foreach ($users as $user) {
            Informasi::create($user);
        }
    }
}
