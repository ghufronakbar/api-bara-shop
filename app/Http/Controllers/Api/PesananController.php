<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use App\Models\ItemPesanan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class PesananController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function index()
    {
        $pesanan = Pesanan::with(["pesanan", "item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->get();

        return response()->json([
            'message' => 'OK',
            'data' => $pesanan
        ]);
    }


    public function store(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $validator = Validator::make($request->all(), [
                'deskripsi' => 'nullable|string',
                'kode' => 'nullable|string',
                'metode' => 'required|string',
                'item_pesanan' => 'required|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Harap lengkapi data',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $validated = $validator->validated();

            if ($validated['metode'] !== 'Cash' && $validated['metode'] !== 'VirtualAccountOrBank') {
                return response()->json([
                    'message' => 'Metode pembayaran tidak valid',
                ], 400);
            }

            if (!array_is_list($validated['item_pesanan'])) {
                return response()->json([
                    'message' => 'Item order tidak valid',
                ], 400);
            }

            if (count($validated['item_pesanan']) < 1) {
                return response()->json([
                    'message' => 'Item order tidak boleh kosong',
                ], 400);
            }

            foreach ($validated['item_pesanan'] as $item) {
                if (!$item['produk_id'] || !$item['jumlah'] || $item['jumlah'] < 1) {
                    return response()->json([
                        'message' => 'Item order tidak valid',
                    ], 400);
                }
            }

            $pelanggan_id = null;

            if ($request->kode) {
                $pelanggan = Pelanggan::where('kode', $validated['kode'])->first();
                if ($pelanggan) {
                    $pelanggan_id = $pelanggan->id;
                }
            }

            $informasi = Informasi::first();

            if (!$informasi) {
                $informasi = Informasi::create([
                    'pajak' => 12,
                    'diskon' => 10,
                ]);
            }

            $produk_ids = [];

            foreach ($validated['item_pesanan'] as $item) {
                $produk_ids[] = $item['produk_id'];
            }

            $check_products = Produk::whereIn('id', $produk_ids)->where('is_deleted', false)->get();

            if (count($check_products) !== count($produk_ids)) {
                return response()->json([
                    'message' => 'Produk tidak ditemukan',
                ], 400);
            }

            $filtered_item_pesanan = [];
            $data_update_prouducts = [];

            $total_sementara = 0;

            foreach ($validated['item_pesanan'] as $item) {
                $filtered_product = $check_products->where('id', $item['produk_id'])->first();
                if (!$filtered_product) {
                    return response()->json([
                        'message' => 'Produk tidak ditemukan',
                    ], 400);
                }

                if ($filtered_product->jumlah < $item['jumlah']) {
                    return response()->json([
                        'message' => 'Stok produk tidak mencukupi',
                    ], 400);
                }

                $jumlah_baru = $filtered_product->jumlah - $item['jumlah'];

                $data_update_prouducts[] = [
                    'id' => $filtered_product->id,
                    'jumlah' => $jumlah_baru
                ];

                $filtered_item_pesanan[] = [
                    'produk_id' => $filtered_product->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $filtered_product->harga,
                    'total' => $filtered_product->harga * $item['jumlah'],
                ];

                $total_sementara += $filtered_product->harga * $item['jumlah'];
            }

            $persentase_diskon = $request->kode ? $informasi->diskon / 100 : 0;
            $diskon = $persentase_diskon * $total_sementara;
            $harga_sebelum_pajak = $total_sementara - $diskon;
            $pajak = ($informasi->pajak / 100) * $harga_sebelum_pajak;
            $total_akhir = $harga_sebelum_pajak + $pajak;

            $pesanan = Pesanan::create([
                'deskripsi' => $request->deskripsi,
                'total_sementara' => $total_sementara,
                'total_akhir' => $total_akhir,
                'diskon' => $diskon,
                'pajak' => $pajak,
                'pelanggan_id' => $pelanggan_id
            ]);

            $pesanan->item_pesanan()->createMany($filtered_item_pesanan);

            foreach ($data_update_prouducts as $product) {
                Produk::where('id', $product['id'])->update([
                    'jumlah' => $product['jumlah']
                ]);
            }

            if ($validated['metode'] === "Cash") {
                $pesanan->transaksi()->create([
                    'metode' => 'Cash',
                    'status' => "Success",
                    'jumlah' => $total_akhir,
                    'detail' => null,
                ]);
            } else {
                $transactionDetails = [
                    'order_id' => $pesanan->id,
                    'gross_amount' => $total_akhir,
                ];

                $transaction = [
                    'transaction_details' => $transactionDetails,
                ];

                $snap_token = Snap::getSnapToken($transaction);
                $url_redirect = Snap::createTransaction($transaction)->redirect_url;

                $pesanan->transaksi()->create([
                    'metode' => 'VirtualAccountOrBank',
                    'status' => "Pending",
                    'jumlah' => $total_akhir,
                    'detail' => null,
                    'snap_token' => $snap_token,
                    'url_redirect' => $url_redirect
                ]);
            }
            $pesanan = Pesanan::with(["item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->where('id', $pesanan->id)->first();
            $this->logService->saveToLog($request, 'Pesanan', $pesanan->toArray());

            return response()->json([
                'message' => 'Berhasil membuat pesanan',
                'data' => $pesanan
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan sistem',
                'errors' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
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

        $pesanan = Pesanan::with(["pesanan", "item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->where('id', $id)->first();

        if (!$pesanan) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $pesanan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
