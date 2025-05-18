<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemasok;
use App\Models\PembelianProduk;
use App\Models\Produk;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembelianProdukController extends Controller
{

    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    public function index()
    {
        $pembelianProduk = PembelianProduk::where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $produk = Produk::get();
        $pemasok = Pemasok::get();

        foreach ($pembelianProduk as $pembelian) {
            $filterProduk = $produk->where('produk_id', $pembelian->produk_id)->first();
            $pembelian->produk = $filterProduk ? $filterProduk->toArray() : null;

            $filterPemasok = $pemasok->where('pemasok_id', $pembelian->pemasok_id)->first();
            $pembelian->pemasok = $filterPemasok ? $filterPemasok->toArray() : null;
        }

        return response()->json([
            'message' => 'OK',
            'data' => $pembelianProduk
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric',
            'total' => 'required|numeric',
            'deskripsi' => 'nullable|string',
            'produk_id' => 'required|uuid',
            'pemasok_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Cek produk
        $checkProduk = Produk::where('produk_id', $request->produk_id)->first();
        if (!$checkProduk || $checkProduk->is_deleted) {
            return response()->json([
                'message' => 'Produk tidak ditemukan atau telah dihapus'
            ], 400);
        }

        // Cek pemasok
        $checkPemasok = Pemasok::where('pemasok_id', $request->pemasok_id)->first();
        if (!$checkPemasok || $checkPemasok->is_deleted) {
            return response()->json([
                'message' => 'Pemasok tidak ditemukan atau telah dihapus'
            ], 400);
        }

        // Validasi dan ambil data yang telah tervalidasi
        $validated = $validator->validated();

        // Hitung total pembelian
        $harga = $validated['total'] / $validated['jumlah'];

        // Simpan data pembelian produk
        $pembelianProduk = PembelianProduk::create([
            'jumlah_pembelian' => $validated['jumlah'],
            'total_harga' => $validated['total'],
            'harga_per_barang' => $harga,
            'deskripsi_pembelian' => $validated['deskripsi'],
            'produk_id' => $validated['produk_id'],
            'pemasok_id' => $validated['pemasok_id'],
        ]);

        // Ambil data pembelian produk sebelumnya untuk menghitung HPP dan jumlah total
        $pembelianProduks = PembelianProduk::where('produk_id', $validated['produk_id'])
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung total pembelian dan jumlah pembelian
        $totalPurchase = 0;
        $amountPurchase = 0;

        foreach ($pembelianProduks as $pembelian) {
            $totalPurchase += $pembelian->total_harga;
            $amountPurchase += $pembelian->jumlah_pembelian;
        }

        // Hitung  HPP baru
        $hpp = $totalPurchase / $amountPurchase;

        // Update stok produk dan COGS
        $newAmount = $checkProduk->jumlah_stok + $validated['jumlah'];

        $checkProduk->update([
            'jumlah_stok' => $newAmount,
            'hpp' => $hpp,
        ]);

        // Simpan log pembelian produk
        $this->logService->saveToLog($request, 'PembelianProduk', $pembelianProduk->toArray());

        return response()->json([
            'message' => 'Berhasil menambahkan pembelian produk',
            'data' => $pembelianProduk
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

        $pembelianProduk = PembelianProduk::where('pembelian_produk_id', $id)->first();

        if (!$pembelianProduk || $pembelianProduk->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $produk = Produk::where('produk_id', $pembelianProduk->produk_id)->first();
        $pembelianProduk->produk = $produk;

        $pemasok = Pemasok::where('pemasok_id', $pembelianProduk->pemasok_id)->first();
        $pembelianProduk->pemasok = $pemasok;

        return response()->json([
            'message' => 'OK',
            'data' => $pembelianProduk
        ]);
    }

    public function update(Request $request, string $id)
    {
        // Validasi input
        $validator = Validator::make($request->all() + ['id' => $id], [
            'id' => 'required|uuid',
            'jumlah' => 'required|numeric',
            'total' => 'required|numeric',
            'deskripsi' => 'nullable|string',
            'pemasok_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $check = PembelianProduk::where('pembelian_produk_id', $id)->first();
        if (!$check || $check->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Cek produk
        $checkProduk = Produk::where('produk_id', $check->produk_id)->first();
        if (!$checkProduk || $checkProduk->is_deleted) {
            return response()->json([
                'message' => 'Produk tidak ditemukan atau telah dihapus'
            ], 400);
        }

        // Cek pemasok
        $checkPemasok = Pemasok::where('pemasok_id', $request->pemasok_id)->first();
        if (!$checkPemasok || $checkPemasok->is_deleted) {
            return response()->json([
                'message' => 'Pemasok tidak ditemukan atau telah dihapus'
            ], 400);
        }

        // Validasi dan ambil data yang telah tervalidasi
        $validated = $validator->validated();

        // Hitung total pembelian
        $harga = $validated['total'] / $validated['jumlah'];

        $totalBeforeUpdate = $check->jumlah_pembelian;

        // Simpan data pembelian produk
        $check->update([
            'jumlah_pembelian' => $validated['jumlah'],
            'total_harga' => $validated['total'],
            'harga' => $harga,
            'deskripsi_pembelian' => $validated['deskripsi'],
            'pemasok_id' => $validated['pemasok_id'],
        ]);

        // Ambil data pembelian produk sebelumnya untuk menghitung HPP dan jumlah total
        $pembelianProduks = PembelianProduk::where('produk_id', $check->produk_id)
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung total pembelian dan jumlah pembelian
        $totalPurchase = 0;
        $amountPurchase = 0;

        foreach ($pembelianProduks as $pembelian) {
            $totalPurchase += $pembelian->total_harga;
            $amountPurchase += $pembelian->jumlah_pembelian;
        }

        // Hitung  HPP baru
        $hpp = $totalPurchase / $amountPurchase;

        // Update stok produk dan COGS
        $gapAmount = $validated['jumlah'] - $totalBeforeUpdate;
        $newAmount = $checkProduk->jumlah_stok + $gapAmount;

        $checkProduk->update([
            'jumlah_stok' => $newAmount,
            'hpp' => $hpp,
        ]);

        // Simpan log pembelian produk
        $this->logService->saveToLog($request, 'PembelianProduk', $check->toArray());

        return response()->json([
            'message' => 'Berhasil mengedit pembelian produk',
            'debug' => [
                'gapAmount' => $gapAmount,
                'newAmount' => $newAmount
            ],
            'data' => $check
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

        $pembelianProduk = PembelianProduk::where('pembelian_produk_id', $id)->first();

        if (!$pembelianProduk || $pembelianProduk->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        PembelianProduk::where('pembelian_produk_id', $id)->update([
            'is_deleted' => true
        ]);

        // Ambil data pembelian produk sebelumnya untuk menghitung HPP dan jumlah total
        $pembelianProduks = PembelianProduk::where('produk_id', $pembelianProduk->produk_id)
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung total pembelian dan jumlah pembelian
        $totalPurchase = 0;
        $amountPurchase = 0;

        foreach ($pembelianProduks as $pembelian) {
            $totalPurchase += $pembelian->total_harga;
            $amountPurchase += $pembelian->jumlah_pembelian;
        }

        // Hitung  HPP baru
        $hpp = $totalPurchase / $amountPurchase;
        // Cek produk
        $checkProduk = Produk::where('produk_id', $pembelianProduk->produk_id)->first();

        // Update stok produk dan COGS
        $newAmount = $checkProduk->jumlah_stok - $pembelianProduk->jumlah_pembelian;

        $checkProduk->update([
            'jumlah_stok' => $newAmount,
            'hpp' => $hpp,
        ]);

        $this->logService->saveToLog($request, 'PembelianProduk', $pembelianProduk->toArray());

        return response()->json([
            'message' => 'Berhasil menghapus pembelian produk',
            'data' => $pembelianProduk
        ]);
    }
}
