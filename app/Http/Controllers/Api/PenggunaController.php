<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailPembuatan;
use App\Mail\EmailPenghapusan;
use App\Mail\EmailPeranBaru;
use App\Models\Peran;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PenggunaController extends Controller
{

    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    public function index()
    {
        $user = User::with(["peran"])
            ->where('is_deleted', false)
            ->orderBy('nama_pengguna', 'asc')
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $user
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'nama' => 'required|string',
            'peran_id' => 'required|string|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        $checkEmail = User::where('email', $validated['email'])->first();

        if ($checkEmail) {
            return response()->json([
                'message' => 'Email sudah terdaftar'
            ], 400);
        }


        $checkPeran = Peran::where('peran_id', $validated['peran_id'])->first();

        if (!$checkPeran || $checkPeran->is_deleted) {
            return response()->json([
                'message' => 'Peran tidak ditemukan'
            ], 400);
        }

        $password = rand(10000000, 99999999);
        $hashedPassword = bcrypt($password);



        // Membuat user baru
        $user = User::create([
            'nama_pengguna' => $validated['nama'],
            'email' => $validated['email'],
            'peran_id' => $validated['peran_id'],
            'password' => $hashedPassword,
            'is_deleted' => false,
        ]);

        // Mengirim email
        Mail::to($validated['email'])->send(new EmailPembuatan($user->nama_pengguna, $password));

        $this->logService->saveToLog($request, 'User', $user->toArray());

        return response()->json([
            'message' => 'Berhasil menambahkan pengguna',
            'data' => $user
        ], 201);
    }

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

        $user = User::with('peran')->where('user_id', $id)->first();

        if (!$user || $user->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $user
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make(['id' => $id] + $request->all(), [
            'id' => 'required|uuid',
            'peran_id' => 'required|string|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        $checkPeran = Peran::where('peran_id', $validated['peran_id'])->first();

        if (!$checkPeran || $checkPeran->is_deleted) {
            return response()->json([
                'message' => 'Peran tidak ditemukan'
            ], 400);
        }

        $user = User::where('user_id', $id)->first();

        if (!$user || $user->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validated = $validator->validated();

        $user->update([
            'peran_id' => $validated['peran_id'],
        ]);

        Mail::to($user->email)->send(new EmailPeranBaru($user->nama_pengguna, $checkPeran->nama_peran));

        $this->logService->saveToLog($request, 'User', $user->toArray());

        return response()->json([
            'message' => 'Berhasil mengedit peran pengguna',
            'data' => $user
        ], 201);
    }

    public function destroy(string $id, Request $request)
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

        $user = User::where('user_id', $id)->first();

        if (!$user || $user->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        Mail::to($user->email)->send(new EmailPenghapusan($user->nama_pengguna));

        $user->update(['is_deleted' => true]);

        $this->logService->saveToLog($request, 'User', $user->toArray());

        return response()->json([
            'message' => 'Berhasil menghapus pengguna',
            'data' => $user
        ]);
    }
}
