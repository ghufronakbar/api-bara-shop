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
        $cacatProduk = CacatProduk::where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $produk = Produk::get();

        foreach ($cacatProduk as $cacat) {
            $filterProduk = $produk->where('produk_id', $cacat->produk_id)->first();
            $cacat->produk = $filterProduk ? $filterProduk->toArray() : null;
        }

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

        $checkProduk = Produk::where('produk_id', $request->produk_id)->first();

        if (!$checkProduk || $checkProduk->is_deleted) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }


        $validated = $validator->validated();

        $newAmount = $checkProduk->jumlah_stok - $validated['jumlah'];

        Produk::where('produk_id', $request->produk_id)->update([
            'jumlah_stok' => $newAmount
        ]);

        $cacatProduk = CacatProduk::create([
            'jumlah_produk' => $validated['jumlah'],
            'alasan_kerusakan' => $validated['alasan'],
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

        $cacatProduk = CacatProduk::where('cacat_produk_id', $id)->first();

        if (!$cacatProduk || $cacatProduk->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $produk = Produk::where('produk_id', $cacatProduk->produk_id)->first();

        $cacatProduk->produk = $produk;

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

        $checkCacatProduk = CacatProduk::where('cacat_produk.cacat_produk_id', $id)
            ->join('produk', 'cacat_produk.produk_id', '=', 'produk.produk_id')
            ->select('cacat_produk.jumlah_produk as cacat_jumlah', 'produk.jumlah_stok as produk_jumlah', 'produk.produk_id as produk_id')
            ->first();

        if (!$checkCacatProduk || $checkCacatProduk->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validated = $validator->validated();

        $gapAmount = $checkCacatProduk->cacat_jumlah - $validated['jumlah'];
        $newAmount = $checkCacatProduk->produk_jumlah + $gapAmount;

        Produk::where('produk_id', $checkCacatProduk->produk_id)->update([
            'jumlah_stok' => $newAmount
        ]);

        $cacatProduk = CacatProduk::where('cacat_produk_id', $id)->update([
            'jumlah_produk' => $validated['jumlah'],
            'alasan_kerusakan' => $validated['alasan'],
            'is_deleted' => false,
        ]);

        $cacatProduk = CacatProduk::where('cacat_produk_id', $id)->first();

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

        $checkCacatProduk = CacatProduk::where('cacat_produk.cacat_produk_id', $id)
            ->join('produk', 'cacat_produk.produk_id', '=', 'produk.produk_id')
            ->select('cacat_produk.jumlah_produk as cacat_jumlah', 'produk.jumlah_stok as produk_jumlah', 'produk.produk_id as produk_id')
            ->first();

        if (!$checkCacatProduk || $checkCacatProduk->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        CacatProduk::where('cacat_produk_id', $id)->update([
            'is_deleted' => true
        ]);

        $newAmount = $checkCacatProduk->produk_jumlah + $checkCacatProduk->cacat_jumlah;

        Produk::where('produk_id', $checkCacatProduk->produk_id)->update([
            'jumlah_stok' => $newAmount
        ]);

        $this->logService->saveToLog($request, 'CacatProduk', $checkCacatProduk->toArray());

        return response()->json([
            'message' => 'Berhasil menghapus data',
            'data' => $checkCacatProduk
        ]);
    }
}
