<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AkunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7); // potong "Bearer "

        try {
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(env('JWT_SECRET'), 'HS256'));

            if (!$decoded->user_id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $user = User::with('peran')->find($decoded->user_id);

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $payload = [
                'user_id' => $user->user_id,
                'nama_pengguna' => $user->nama_pengguna,
                'email' => $user->email,
                'foto_profil' => $user->foto_profil,
                'peran' => $user->peran,
                'exp' => now()->addDays(1)->timestamp,
            ];

            $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            $user->token = $token;

            return response()->json([
                'message' => 'OK',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorized', 'error' => $e], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function editProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'nama' => 'required',
            'gambar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7); // potong "Bearer "

        try {
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(env('JWT_SECRET'), 'HS256'));

            if (!$decoded->user_id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $user = User::find($decoded->user_id);
            $checkEmail = User::where('email', $request->email)->first();

            if ($checkEmail && $checkEmail->user_id != $user->user_id) {
                return response()->json([
                    'message' => 'Email sudah terdaftar'
                ], 400);
            }

            $user->email = $request->email;
            $user->nama_pengguna = $request->nama;
            $user->foto_profil = $request->gambar;
            $user->save();

            $payload = [
                'user_id' => $user->user_id,
                'nama_pengguna' => $user->nama_pengguna,
                'email' => $user->email,
                'foto_profil' => $user->foto_profil,
                'peran' => $user->peran,
                'exp' => now()->addDays(1)->timestamp,
            ];

            $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'user_id' => $user->user_id,
                    'nama_pengguna' => $user->nama_pengguna,
                    'email' => $user->email,
                    'foto_profil' => $user->foto_profil,
                    'peran' => $user->peran,
                    'token' => $token,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorized', 'error' => $e], 401);
        }
    }

    public function editPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_lama' => 'required',
            'password_baru' => 'required',
            'password_konfirmasi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7); // potong "Bearer "

        try {
            $decoded = JWT::decode($token, new \Firebase\JWT\Key(env('JWT_SECRET'), 'HS256'));

            if (!$decoded->user_id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $user = User::find($decoded->user_id);

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if ($request->password_baru != $request->password_konfirmasi) {
                return response()->json([
                    'message' => 'Password baru dan konfirmasi tidak sama'
                ], 400);
            }

            if (!Hash::check($request->password_lama, $user->password)) {
                return response()->json([
                    'message' => 'Password lama salah'
                ], 400);
            }

            $user->password = Hash::make($request->password_baru);
            $user->save();

            return response()->json([
                'message' => 'Berhasil mengubah password',
                'data' => [
                    'user_id' => $user->user_id,
                    'nama_pengguna' => $user->nama_pengguna,
                    'email' => $user->email,
                    'foto_profil' => $user->foto_profil,
                    'peran' => $user->peran,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorized', 'error' => $e], 401);
        }
    }
}
