<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{

    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    public function index()
    {
        $produk = Produk::withCount([])
            ->where('is_deleted', false)
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $produk
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string',
            'nama' => 'required|string',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        // Membuat produk baru
        $produk = Produk::create([
            'kategori' => $validated['kategori'],
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
            'deskripsi' => $validated['deskripsi'],
            'hpp' => 0,
            'jumlah' => 0,
            'gambar' => $validated['gambar'] ?? null,
            'is_deleted' => false,
        ]);

        $this->logService->saveToLog($request, 'Produk', $produk->toArray());

        // Menyimpan dan mengembalikan response
        return response()->json([
            'message' => 'Berhasil menambahkan produk',
            'data' => $produk
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

        $produk = Produk::where('id', $id)->first();

        if (!$produk) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $produk
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make(['id' => $id] + $request->all(), [
            'id' => 'required|uuid',
            'kategori' => 'required|string',
            'nama' => 'required|string',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $produk = Produk::where('id', $id)->select('id')->first();

        if (!$produk) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validated = $validator->validated();

        // Membuat produk baru
        $produk = Produk::create([
            'kategori' => $validated['kategori'],
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
            'deskripsi' => $validated['deskripsi'],
            'hpp' => 0,
            'jumlah' => 0,
            'gambar' => $validated['gambar'] ?? null,
            'is_deleted' => false,
        ]);

        $this->logService->saveToLog($request, 'Produk', $produk->toArray());

        // Menyimpan dan mengembalikan response
        return response()->json([
            'message' => 'Berhasil mengedit produk',
            'data' => $produk
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
