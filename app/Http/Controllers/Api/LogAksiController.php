<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogAksi;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogAksiController extends Controller
{

    public function index(Request $request)
    {

        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7); // potong "Bearer "

        try {

            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(env('JWT_SECRET'), 'HS256'));

            if (!$decoded->id || !$decoded->peran) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $logAksi = LogAksi::withCount([])
                ->where('is_deleted', false)
                ->orderBy('created_at', 'desc')
                ->get();

            $user = User::get();

            foreach ($logAksi as $log) {
                $log->user = $user->where('id', $log->user_id)->first();
                try {
                    $log->detail = json_decode($log->detail);
                } catch (\Throwable $th) {
                    $log->detail = $log->detail;
                }
            };

            // Log::info('Log Aksi', ['querySemua' => $request->query('semua')]);
            // Log::info('Log Aksi', ['decoded role' => $decoded->peran]);

            if ($decoded->peran === 'ManagerOperational' || $decoded->peran === 'Cashier') {
                $logAksi = $logAksi->where('user_id', $decoded->id);
            }

            return response()->json([
                'message' => 'OK',
                'data' => $logAksi,
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorized', 'error' => $e], 401);
        }
    }

    public function store(Request $request) {}


    public function show(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Id tidak valid',
                'errors' => $validator->errors(),
            ], 400);
        }

        $logAksi = LogAksi::where('id', $id)->first();

        if (!$logAksi || $logAksi->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $user = User::get();

        $logAksi->user = $user->where('id', $logAksi->user_id)->first();
        try {
            $logAksi->detail = json_decode($logAksi->detail);
        } catch (\Throwable $th) {
            $logAksi->detail = $logAksi->detail;
        }

        return response()->json([
            'message' => 'OK',
            'data' => $logAksi
        ]);
    }

    public function update(Request $request, string $id) {}

    public function destroy(string $id, Request $request) {}
}
