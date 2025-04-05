<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemasok;
use App\Models\PembelianProduk;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemasokController extends Controller
{

    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    public function index()
    {
        $pemasok = Pemasok::withCount([])
            ->where('is_deleted', false)
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $pemasok
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
            'gambar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        $pemasok = Pemasok::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'telepon' => $validated['telepon'],
            'gambar' => $validated['gambar'] ?? null,
            'is_deleted' => false,
        ]);

        $this->logService->saveToLog($request, 'Pemasok', $pemasok->toArray());

        return response()->json([
            'message' => 'Berhasil menambahkan pemasok',
            'data' => $pemasok
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

        $pemasok = Pemasok::where('id', $id)->first();

        if (!$pemasok || $pemasok->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $pembelian = PembelianProduk::where('pemasok_id', $pemasok->id)->get();
        $pemasok->pembelian_produk = $pembelian;

        return response()->json([
            'message' => 'OK',
            'data' => $pemasok
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make(['id' => $id] + $request->all(), [
            'id' => 'required|uuid',
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'nullable|string',
            'gambar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $pemasok = Pemasok::where('id', $id)->select('id', 'is_deleted')->first();

        if (!$pemasok || $pemasok->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validated = $validator->validated();

        $pemasok->update([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'telepon' => $validated['telepon'],
            'gambar' => $validated['gambar'] ?? null,
        ]);

        $this->logService->saveToLog($request, 'Pemasok', $pemasok->toArray());

        return response()->json([
            'message' => 'Berhasil mengedit pemasok',
            'data' => $pemasok
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

        $pemasok = Pemasok::where('id', $id)->first();

        if (!$pemasok || $pemasok->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $pemasok->update(['is_deleted' => true]);

        $this->logService->saveToLog($request, 'Pemasok', $pemasok->toArray());

        return response()->json([
            'message' => 'Berhasil menghapus pemasok',
            'data' => $pemasok
        ]);
    }
}
