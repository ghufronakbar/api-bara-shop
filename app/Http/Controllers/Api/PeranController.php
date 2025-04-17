<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peran;
use App\Models\User;
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
        $perans = Peran::whereHas('users', function ($query) {
            $query->where('is_deleted', false);
        })
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
            'ringkasan' => 'required|boolean',
            'laporan' => 'required|boolean',
            'informasi' => 'required|boolean',
            'kirim_pesan' => 'required|boolean',
            'pengguna' => 'required|boolean',
            'peran' => 'required|boolean',
            'pelanggan' => 'required|boolean',
            'produk' => 'required|boolean',
            'pemasok' => 'required|boolean',
            'riwayat_pesanan' => 'required|boolean',
            'pembelian' => 'required|boolean',
            'cacat_produk' => 'required|boolean',
            'kasir' => 'required|boolean',
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
            'ringkasan' => $validated['ringkasan'],
            'laporan' => $validated['laporan'],
            'informasi' => $validated['informasi'],
            'kirim_pesan' => $validated['kirim_pesan'],
            'pengguna' => $validated['pengguna'],
            'peran' => $validated['peran'],
            'pelanggan' => $validated['pelanggan'],
            'produk' => $validated['produk'],
            'pemasok' => $validated['pemasok'],
            'riwayat_pesanan' => $validated['riwayat_pesanan'],
            'pembelian' => $validated['pembelian'],
            'cacat_produk' => $validated['cacat_produk'],
            'kasir' => $validated['kasir'],
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
            'ringkasan' => 'required|boolean',
            'laporan' => 'required|boolean',
            'informasi' => 'required|boolean',
            'kirim_pesan' => 'required|boolean',
            'pengguna' => 'required|boolean',
            'peran' => 'required|boolean',
            'pelanggan' => 'required|boolean',
            'produk' => 'required|boolean',
            'pemasok' => 'required|boolean',
            'riwayat_pesanan' => 'required|boolean',
            'pembelian' => 'required|boolean',
            'cacat_produk' => 'required|boolean',
            'kasir' => 'required|boolean',
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
            'ringkasan' => $validated['ringkasan'],
            'laporan' => $validated['laporan'],
            'informasi' => $validated['informasi'],
            'kirim_pesan' => $validated['kirim_pesan'],
            'pengguna' => $validated['pengguna'],
            'peran' => $validated['peran'],
            'pelanggan' => $validated['pelanggan'],
            'produk' => $validated['produk'],
            'pemasok' => $validated['pemasok'],
            'riwayat_pesanan' => $validated['riwayat_pesanan'],
            'pembelian' => $validated['pembelian'],
            'cacat_produk' => $validated['cacat_produk'],
            'kasir' => $validated['kasir'],
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

        $users = User::where('peran_id', $id)->where('is_deleted', false)->get();

        if ($users->count() > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus peran yang memiliki pengguna'
            ], 400);
        }

        $peran->update(['is_deleted' => true]);

        $this->logService->saveToLog($request, 'Peran', $peran->toArray());

        return response()->json([
            'message' => 'Berhasil menghapus peran',
            'data' => $peran
        ]);
    }
}
