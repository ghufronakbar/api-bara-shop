<?php

namespace App\Services;

use App\Models\LogAksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LogService
{
    /**
     * Menyimpan data ke dalam log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $model  Model yang terlibat (User, Customer, etc.)
     * @param  array  $detail  Detail terkait log
     * @return void
     */
    public function saveToLog(Request $request, string $model, array $detail)
    {

        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7); // potong "Bearer "

        try {
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(env('JWT_SECRET'), 'HS256'));

            // Tentukan aksi berdasarkan method HTTP
            $action = 'Create'; // Default action adalah 'Create'
            $actionDesc = 'membuat';
            $modelDesc = '';

            switch ($request->method()) {
                case 'POST':
                    $action = 'Create';
                    $actionDesc = 'membuat';
                    break;
                case 'PUT':
                    $action = 'Update';
                    $actionDesc = 'mengedit';
                    break;
                case 'DELETE':
                    $action = 'Delete';
                    $actionDesc = 'menghapus';
                    break;
            }

            // Tentukan deskripsi model
            switch ($model) {
                case 'User':
                    $modelDesc = 'pengguna';
                    break;
                case 'Pelanggan':
                    $modelDesc = 'customer';
                    break;
                case 'Produk':
                    $modelDesc = 'produk';
                    break;
                case 'PembelianProduk':
                    $modelDesc = 'pembelian produk';
                    break;
                case 'Pesanan':
                    $modelDesc = 'pesanan';
                    break;
                case 'CacatProduk':
                    $modelDesc = 'kerusakan produk';
                    break;
                case 'Informasi':
                    $modelDesc = 'informasi';
                    break;
            }

            // Ambil data pengguna yang sedang melakukan aksi
            $id = $decoded->id;
            $user = User::find($id);
            if (!$user) {
                throw new \Exception('Unauthorized');
            }

            $description = sprintf(
                '%s (%s) %s %s dengan id %s',
                $user->nama,
                $user->peran,
                $model === 'Informasi' ? 'mengedit' : $actionDesc,
                $modelDesc,
                $detail['id']
            );

            // Simpan log
            LogAksi::create([
                'id' => (string) Str::uuid(),
                'deskripsi' => $description,
                'detail' => json_encode($detail),
                'referensi_id' => $detail['id'],
                'model_referensi' => $model,
                'aksi' => $action,
                'user_id' => $user->id,
                'is_deleted' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
