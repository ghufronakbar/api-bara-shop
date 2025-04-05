<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Semua kolom wajib diisi',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Cek email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Email tidak ditemukan'
            ], 400);
        }

        // Cek password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password salah'
            ], 400);
        }

        // Payload JWT
        $payload = [
            'id' => $user->id,
            'nama' => $user->nama,
            'gambar' => $user->gambar,
            'email' => $user->email,
            'peran' => $user->peran,
            'exp' => now()->addDays(1)->timestamp,
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'message' => 'Login berhasil!',
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'gambar' => $user->gambar,
                'peran' => $user->peran,
                'token' => $token,
            ]
        ]);
    }

    public function check(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7); // potong "Bearer "

        try {
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(env('JWT_SECRET'), 'HS256'));

            return response()->json([
                'message' => 'OK',
                'data' => (array) $decoded,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
