<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peran;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeranController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Menampilkan daftar peran.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $perans = Peran::where('is_deleted', false)
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $perans
        ]);
    }

    /**
     * Menyimpan data peran baru.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'kelola_informasi' => 'required|boolean',
            'kelola_produk' => 'required|boolean',
            'kelola_pembelian_produk' => 'required|boolean',
            'kelola_cacat_produk' => 'required|boolean',
            'kelola_pelanggan' => 'required|boolean',
            'kelola_supplier' => 'required|boolean',
            'semua_log_aktivitas' => 'required|boolean',
            'kirim_pesan' => 'required|boolean',
            'laporan' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        // Membuat peran baru
        $peran = Peran::create([
            'nama' => $validated['nama'],
            'kelola_informasi' => $validated['kelola_informasi'],
            'kelola_produk' => $validated['kelola_produk'],
            'kelola_pembelian_produk' => $validated['kelola_pembelian_produk'],
            'kelola_cacat_produk' => $validated['kelola_cacat_produk'],
            'kelola_pelanggan' => $validated['kelola_pelanggan'],
            'kelola_supplier' => $validated['kelola_supplier'],
            'semua_log_aktivitas' => $validated['semua_log_aktivitas'],
            'kirim_pesan' => $validated['kirim_pesan'],
            'laporan' => $validated['laporan'],
            'is_deleted' => false,
        ]);

        $this->logService->saveToLog($request, 'Peran', $peran->toArray());

        return response()->json([
            'message' => 'Berhasil menambahkan peran',
            'data' => $peran
        ], 201);
    }

    /**
     * Menampilkan data peran berdasarkan id.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
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

        $peran = Peran::where('id', $id)
            ->where('is_deleted', false)
            ->first();

        if (!$peran) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $peran
        ]);
    }

    /**
     * Mengupdate data peran.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make(['id' => $id] + $request->all(), [
            'id' => 'required|uuid',
            'nama' => 'required|string',
            'kelola_informasi' => 'required|boolean',
            'kelola_produk' => 'required|boolean',
            'kelola_pembelian_produk' => 'required|boolean',
            'kelola_cacat_produk' => 'required|boolean',
            'kelola_pelanggan' => 'required|boolean',
            'kelola_supplier' => 'required|boolean',
            'semua_log_aktivitas' => 'required|boolean',
            'kirim_pesan' => 'required|boolean',
            'laporan' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $peran = Peran::where('id', $id)->where('is_deleted', false)->first();

        if (!$peran) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validated = $validator->validated();

        $peran->update([
            'nama' => $validated['nama'],
            'kelola_informasi' => $validated['kelola_informasi'],
            'kelola_produk' => $validated['kelola_produk'],
            'kelola_pembelian_produk' => $validated['kelola_pembelian_produk'],
            'kelola_cacat_produk' => $validated['kelola_cacat_produk'],
            'kelola_pelanggan' => $validated['kelola_pelanggan'],
            'kelola_supplier' => $validated['kelola_supplier'],
            'semua_log_aktivitas' => $validated['semua_log_aktivitas'],
            'kirim_pesan' => $validated['kirim_pesan'],
            'laporan' => $validated['laporan'],
        ]);

        $this->logService->saveToLog($request, 'Peran', $peran->toArray());

        return response()->json([
            'message' => 'Berhasil mengedit peran',
            'data' => $peran
        ], 201);
    }

    /**
     * Menghapus data peran.
     *
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

        $peran = Peran::where('id', $id)->where('is_deleted', false)->first();

        if (!$peran) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $peran->update(['is_deleted' => true]);

        $this->logService->saveToLog($request, 'Peran', $peran->toArray());

        return response()->json([
            'message' => 'Berhasil menghapus peran',
            'data' => $peran
        ]);
    }
}
