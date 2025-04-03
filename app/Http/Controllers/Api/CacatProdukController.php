<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CacatProduk;
use App\Models\Produk;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CacatProdukController extends Controller
{

    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    public function index()
    {
        $cacatProduk = CacatProduk::withCount([])
            ->where('is_deleted', false)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $cacatProduk
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric',
            'alasan' => 'required|string',
            'produk_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $checkProduk = Produk::where('id', $request->produk_id)->first();

        if (!$checkProduk || $checkProduk->is_deleted) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }


        $validated = $validator->validated();

        $newAmount = $checkProduk->jumlah - $validated['jumlah'];

        Produk::where('id', $request->produk_id)->update([
            'jumlah' => $newAmount
        ]);

        $cacatProduk = CacatProduk::create([
            'jumlah' => $validated['jumlah'],
            'alasan' => $validated['alasan'],
            'produk_id' => $validated['produk_id'],
            'is_deleted' => false,
        ]);

        $this->logService->saveToLog($request, 'CacatProduk', $cacatProduk->toArray());

        return response()->json([
            'message' => 'Berhasil menambahkan data',
            'data' => $cacatProduk
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

        $cacatProduk = CacatProduk::where('id', $id)->first();

        if (!$cacatProduk || $cacatProduk->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $cacatProduk
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all() + ['id' => $id], [
            'id' => 'required|uuid',
            'jumlah' => 'required|numeric',
            'alasan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $checkCacatProduk = CacatProduk::where('cacat_produk.id', $id)
            ->join('produk', 'cacat_produk.produk_id', '=', 'produk.id')
            ->select('cacat_produk.jumlah as cacat_jumlah', 'produk.jumlah as produk_jumlah', 'produk.id as produk_id')
            ->first();

        if (!$checkCacatProduk || $checkCacatProduk->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validated = $validator->validated();

        $gapAmount = $checkCacatProduk->cacat_jumlah - $validated['jumlah'];
        $newAmount = $checkCacatProduk->produk_jumlah + $gapAmount;

        Produk::where('id', $checkCacatProduk->produk_id)->update([
            'jumlah' => $newAmount
        ]);

        $cacatProduk = CacatProduk::where('id', $id)->update([
            'jumlah' => $validated['jumlah'],
            'alasan' => $validated['alasan'],
            'is_deleted' => false,
        ]);

        $cacatProduk = CacatProduk::where('id', $id)->first();

        $this->logService->saveToLog($request, 'CacatProduk', $cacatProduk->toArray());

        return response()->json([
            'message' => 'Berhasil mengedit data',
            'data' => $cacatProduk
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

        $checkCacatProduk = CacatProduk::where('cacat_produk.id', $id)
            ->join('produk', 'cacat_produk.produk_id', '=', 'produk.id')
            ->select('cacat_produk.jumlah as cacat_jumlah', 'produk.jumlah as produk_jumlah', 'produk.id as produk_id')
            ->first();

        if (!$checkCacatProduk || $checkCacatProduk->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $checkCacatProduk->update(['is_deleted' => true]);

        $newAmount = $checkCacatProduk->produk_jumlah + $checkCacatProduk->cacat_jumlah;

        Produk::where('id', $checkCacatProduk->produk_id)->update([
            'jumlah' => $newAmount
        ]);

        $this->logService->saveToLog($request, 'CacatProduk', $checkCacatProduk->toArray());

        return response()->json([
            'message' => 'Berhasil menghapus data',
            'data' => $checkCacatProduk
        ]);
    }
}
