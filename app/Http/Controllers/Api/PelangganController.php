<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{

    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    public function index()
    {
        $pelanggan = Pelanggan::withCount([])
            ->where('is_deleted', false)
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $pelanggan
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'kode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $checkCode = Pelanggan::where('kode', $request->kode)->first();

        if ($checkCode) {
            return response()->json([
                'message' => 'Pelanggan sudah terdaftar'
            ], 400);
        }

        $jenis_kode = "Email";

        if (filter_var($request->kode, FILTER_VALIDATE_EMAIL)) {
            $jenis_kode = "Email";
        } else if (filter_var($request->kode, FILTER_VALIDATE_INT) && substr($request->kode, 0, 2) == '62') {
            $jenis_kode = "Phone";
        } else {
            return response()->json([
                'message' => 'Kode tidak valid'
            ], 400);
        }

        $validated = $validator->validated();

        $pelanggan = Pelanggan::create([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'jenis_kode' => $jenis_kode,
            'is_deleted' => false,
        ]);

        $this->logService->saveToLog($request, 'Pelanggan', $pelanggan->toArray());

        return response()->json([
            'message' => 'Berhasil menambahkan pelanggan',
            'data' => $pelanggan
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

        $pelanggan = Pelanggan::where('id', $id)->first();

        if (!$pelanggan || $pelanggan->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $pelanggan
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make(['id' => $id] + $request->all(), [
            'id' => 'required|uuid',
            'nama' => 'required|string',
            'kode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $pelanggan = Pelanggan::where('id', $id)->select('id', 'is_deleted')->first();

        if (!$pelanggan || $pelanggan->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validated = $validator->validated();

        $checkCode = Pelanggan::where('kode', $request->kode)->first();

        if ($checkCode && $checkCode->id != $id) {
            return response()->json([
                'message' => 'Pelanggan sudah terdaftar'
            ], 400);
        }

        $jenis_kode = "Email";

        if (filter_var($request->kode, FILTER_VALIDATE_EMAIL)) {
            $jenis_kode = "Email";
        } else if (filter_var($request->kode, FILTER_VALIDATE_INT) && substr($request->kode, 0, 2) == '62') {
            $jenis_kode = "Phone";
        } else {
            return response()->json([
                'message' => 'Kode tidak valid'
            ]);
        }

        $pelanggan->update([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'jenis_kode' => $jenis_kode,
        ]);

        $this->logService->saveToLog($request, 'Pelanggan', $pelanggan->toArray());

        return response()->json([
            'message' => 'Berhasil mengedit pelanggan',
            'data' => $pelanggan
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

        $pelanggan = Pelanggan::where('id', $id)->first();

        if (!$pelanggan || $pelanggan->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $pelanggan->update(['is_deleted' => true]);

        $this->logService->saveToLog($request, 'Pelanggan', $pelanggan->toArray());

        return response()->json([
            'message' => 'Berhasil menghapus pelanggan',
            'data' => $pelanggan
        ]);
    }
}
