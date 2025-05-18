<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WhatsAppController;
use App\Mail\EmailNotaPesanan;
use App\Models\Informasi;
use App\Models\ItemPesanan;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
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
        $pesanan = Pesanan::with(["item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->orderBy('created_at', 'desc')->get();

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
                $pelanggan = Pelanggan::where('kode_pelanggan', $validated['kode'])->first();
                if ($pelanggan) {
                    $pelanggan_id = $pelanggan->pelanggan_id;
                }
            }

            $informasi = Informasi::first();

            if (!$informasi) {
                $informasi = Informasi::create([
                    'persentase_pajak' => 12,
                    'persentase_diskon' => 10,
                ]);
            }

            $produk_ids = [];

            foreach ($validated['item_pesanan'] as $item) {
                $produk_ids[] = $item['produk_id'];
            }

            $check_products = Produk::whereIn('produk_id', $produk_ids)->where('is_deleted', false)->get();

            if (count($check_products) !== count($produk_ids)) {
                return response()->json([
                    'message' => 'Produk tidak ditemukan',
                ], 400);
            }

            $filtered_item_pesanan = [];
            $data_update_prouducts = [];

            $total_sementara = 0;

            foreach ($validated['item_pesanan'] as $item) {
                $filtered_product = $check_products->where('produk_id', $item['produk_id'])->first();
                if (!$filtered_product) {
                    return response()->json([
                        'message' => 'Produk tidak ditemukan',
                    ], 400);
                }

                if ($filtered_product->jumlah_stok < $item['jumlah']) {
                    return response()->json([
                        'message' => 'Stok produk tidak mencukupi',
                    ], 400);
                }

                $jumlah_baru = $filtered_product->jumlah_stok - $item['jumlah'];

                $data_update_prouducts[] = [
                    'produk_id' => $filtered_product->produk_id,
                    'jumlah_stok' => $jumlah_baru
                ];

                $filtered_item_pesanan[] = [
                    'produk_id' => $filtered_product->produk_id,
                    'jumlah_barang' => $item['jumlah'],
                    'harga_per_barang' => $filtered_product->harga_produk,
                    'total_harga' => $filtered_product->harga_produk * $item['jumlah'],
                ];

                $total_sementara += $filtered_product->harga_produk * $item['jumlah'];
                // DEBUG
                // return response()->json([
                //     'message' => 'OK',
                //     'filtered_item_pesanan' => $filtered_item_pesanan,
                //     'total_sementara' => $total_sementara,
                // ]);
            }

            $persentase_diskon = $request->kode ? $informasi->persentase_diskon / 100 : 0;

            $diskon = $persentase_diskon * $total_sementara;
            $harga_sebelum_pajak = $total_sementara - $diskon;
            $pajak = ($informasi->persentase_pajak / 100) * $harga_sebelum_pajak;
            $total_akhir = $harga_sebelum_pajak + $pajak;

            // // DEBUG
            // return response()->json([
            //     'message' => 'OK',
            //     'persentase_diskon' => $persentase_diskon,
            //     'data' => $filtered_item_pesanan,
            //     'total_sementara' => $total_sementara,
            //     'diskon' => $diskon,
            //     'pajak' => $pajak,
            //     'total_akhir' => $total_akhir,
            //     'data_update_prouducts' => $data_update_prouducts
            // ]);

            $pesanan = Pesanan::create([
                'deskripsi_pesanan' => $request->deskripsi,
                'total_harga_barang' => $total_sementara,
                'total_akhir' => $total_akhir,
                'diskon_dikenakan' => $diskon,
                'pajak_dikenakan' => $pajak,
                'persentase_diskon' => $informasi->persentase_diskon,
                'persentase_pajak' => $informasi->persentase_pajak,
                'pelanggan_id' => $pelanggan_id
            ]);

            $pesanan->item_pesanan()->createMany($filtered_item_pesanan);

            foreach ($data_update_prouducts as $product) {
                Produk::where('produk_id', $product['produk_id'])->update([
                    'jumlah_stok' => $product['jumlah_stok']
                ]);
            }

            if ($validated['metode'] === "Cash") {
                $pesanan->transaksi()->create([
                    'metode_pembayaran' => 'Cash',
                    'status_pembayaran' => "Success",
                    'jumlah_pembayaran' => $total_akhir,
                    'detail_transaksi' => null,
                ]);
            } else {
                $transactionDetails = [
                    'order_id' => $pesanan->pesanan_id,
                    'gross_amount' => $total_akhir,
                ];

                $transaction = [
                    'transaction_details' => $transactionDetails,
                ];

                $snap_token = Snap::getSnapToken($transaction);
                $url_redirect = Snap::createTransaction($transaction)->redirect_url;

                $status = null;
                try {
                    $status = MidtransTransaction::status($pesanan->pesanan_id);
                } catch (Exception $e) {
                }

                $pesanan->transaksi()->create([
                    'metode_pembayaran' => 'VirtualAccountOrBank',
                    'status_pembayaran' => "Pending",
                    'jumlah_pembayaran' => $total_akhir,
                    'detail_transaksi' => $status ? json_encode($status) : null,
                    'midtrans_snap_token' => $snap_token,
                    'midtrans_url_redirect' => $url_redirect
                ]);
            }
            $pesanan = Pesanan::with(["item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->where('pesanan_id', $pesanan->pesanan_id)->first();
            $this->logService->saveToLog($request, 'Pesanan', $pesanan->toArray());

            $nota = [
                'pesanan_id' => $pesanan->pesanan_id,
                'tanggal' => $pesanan->created_at,
                'persentase_diskon' => $informasi->persentase_diskon,
                'diskon' => $pesanan->diskon_dikenakan,
                'persentase_pajak' => $informasi->persentase_pajak,
                'pajak' => $pesanan->pajak_dikenakan,
                'total_akhir' => $pesanan->total_akhir
            ];

            $item_nota = [];

            foreach ($pesanan->item_pesanan as $item) {
                $item_nota[] = [
                    'produk' => $item->produk->nama_produk,
                    'jumlah' => $item->jumlah_barang,
                    'harga' => $item->harga_per_barang,
                    'total' => $item->total_harga,
                ];
            }

            if ($pelanggan && !$pelanggan->is_deleted && $request->metode === "Cash") {
                if ($pelanggan->jenis_kode == "Email") {
                    Mail::to($pelanggan->kode_pelanggan)->send(new EmailNotaPesanan($nota, $item_nota));
                }

                if ($pelanggan->jenis_kode == "Phone") {
                    $whatsapp = new WhatsAppController();
                    $whatsapp->sendNotaPesanan($pelanggan->kode_pelanggan, $nota, $item_nota);
                }
            }

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


    public function show(string $id)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Id tidak valid',
                'errors' => $validator->errors(),
            ], 400);
        }

        $pesanan = Pesanan::with(["item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->where('pesanan_id', $id)->first();

        if (!$pesanan) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        $status = null;
        try {
            $status = MidtransTransaction::status($pesanan->pesanan_id);
            // DEBUG
            // return response()->json([
            //     'data_local'=>[
            //         'pesanan->transaksi->status' => $pesanan->transaksi->status_pembayaran,
            //         'pesanan->transaksi->detail' => $pesanan->transaksi->detail_transaksi
            //     ],
            //     'status' => (array) $status,
            // ]);
            $arrStatus = (array) $status;
            if ($pesanan->transaksi->status_pembayaran == "Pending" && isset($status->transaction_status) && $arrStatus["transaction_status"] == "settlement") {
                // DEBUG
                $pesanan->transaksi()->update([
                    'status_pembayaran' => "Success",
                    'detail_transaksi' => json_encode($status)
                ]);
                $informasi = Informasi::first();
                $pelanggan = $pesanan->pelanggan;
                $nota = [
                    'pesanan_id' => $pesanan->pesanan_id,
                    'tanggal' => $pesanan->created_at,
                    'persentase_diskon' => $pesanan->persentase_diskon,
                    'diskon' => $pesanan->diskon_dikenakan,
                    'persentase_pajak' => $informasi->persentase_pajak,
                    'pajak' => $pesanan->pajak_dikenakan,
                    'total_akhir' => $pesanan->total_akhir
                ];

                $item_nota = [];

                foreach ($pesanan->item_pesanan as $item) {
                    $item_nota[] = [
                        'produk' => $item->produk->nama_produk,
                        'jumlah' => $item->jumlah_barang,
                        'harga' => $item->harga_per_barang,
                        'total' => $item->total_harga,
                    ];
                }
                if ($pelanggan && !$pelanggan->is_deleted) {
                    if ($pelanggan->jenis_kode == "Email") {
                        Mail::to($pelanggan->kode_pelanggan)->send(new EmailNotaPesanan($nota, $item_nota));
                    }

                    if ($pelanggan->jenis_kode == "Phone") {
                        $whatsapp = new WhatsAppController();
                        $whatsapp->sendNotaPesanan($pelanggan->kode_pelanggan, $nota, $item_nota);
                    }
                }
            }
        } catch (Exception $e) {
        }
        $pesanan = Pesanan::with(["item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->where('pesanan_id', $id)->first();
        return response()->json([
            'message' => 'OK',
            'data' => $pesanan
        ]);
    }

    /**
     * Send Nota Pesanan
     */
    public function kirimNota(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Id tidak valid',
                'errors' => $validator->errors(),
            ], 400);
        }

        $pesanan = Pesanan::with(["item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->where('pesanan_id', $request->id)->first();
        if (!$pesanan) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        if ($pesanan->status == "Pending") {
            return response()->json([
                'message' => 'Pesanan belum dibayar',
            ], 400);
        }

        if (!$pesanan->pelanggan) {
            return response()->json([
                'message' => 'Tidak bisa mengirim nota, pelanggan belum terdaftar',
            ], 400);
        }

        $pelanggan = $pesanan->pelanggan;
        $nota = [
            'pesanan_id' => $pesanan->pesanan_id,
            'tanggal' => $pesanan->created_at,
            'persentase_diskon' => $pesanan->persentase_diskon,
            'diskon' => $pesanan->diskon_dikenakan,
            'persentase_pajak' => $pesanan->persentase_pajak,
            'pajak' => $pesanan->pajak_dikenakan,
            'total_akhir' => $pesanan->total_akhir
        ];

        $item_nota = [];

        foreach ($pesanan->item_pesanan as $item) {
            $item_nota[] = [
                'produk' => $item->produk->nama_produk,
                'jumlah' => $item->jumlah_barang,
                'harga' => $item->harga_per_barang,
                'total' => $item->total_harga,
            ];
        }

        if ($pelanggan && !$pelanggan->is_deleted) {
            if ($pelanggan->jenis_kode == "Email") {
                Mail::to($pelanggan->kode_pelanggan)->send(new EmailNotaPesanan($nota, $item_nota));
            }

            if ($pelanggan->jenis_kode == "Phone") {
                $whatsapp = new WhatsAppController();
                $whatsapp->sendNotaPesanan($pelanggan->kode_pelanggan, $nota, $item_nota);
            }
        }

        $to = $pelanggan->jenis_kode == "Email" ? "email" : "nomor";

        return response()->json([
            'message' => 'Berhasil mengirim nota ke ' . $to . ' pelanggan',
            'data' => $pesanan
        ]);
    }

    public function webhook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_status' => 'required|string',
            'order_id' => 'required|string|uuid',
            'fraud_status' => 'required|string',
        ]);

        $pesanan = Pesanan::with(["item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->where('pesanan_id', $request->order_id)->first();

        if (!$pesanan) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        if (
            ($request->transaction_status == "capture" && $request->fraud_status == "accept") ||
            $request->transaction_status == "settlement"
        ) {
            $pesanan->transaksi()->update([
                'status_pembayaran' => "Success",
                'detail_transaksi' => json_encode($request->all())
            ]);

            $pelanggan = $pesanan->pelanggan;
            $nota = [
                'pesanan_id' => $pesanan->pesanan_id,
                'tanggal' => $pesanan->created_at,
                'persentase_diskon' => $pesanan->persentase_diskon,
                'diskon' => $pesanan->diskon_dikenakan,
                'persentase_pajak' => $pesanan->persentase_pajak,
                'pajak' => $pesanan->pajak_dikenakan,
                'total_akhir' => $pesanan->total_akhir
            ];

            $item_nota = [];

            foreach ($pesanan->item_pesanan as $item) {
                $item_nota[] = [
                    'produk' => $item->produk->nama_produk,
                    'jumlah' => $item->jumlah_barang,
                    'harga' => $item->harga_per_barang,
                    'total' => $item->total_harga,
                ];
            }

            if ($pelanggan && !$pelanggan->is_deleted) {
                if ($pelanggan->jenis_kode == "Email") {
                    Mail::to($pelanggan->kode_pelanggan)->send(new EmailNotaPesanan($nota, $item_nota));
                }

                if ($pelanggan->jenis_kode == "Phone") {
                    $whatsapp = new WhatsAppController();
                    $whatsapp->sendNotaPesanan($pelanggan->kode_pelanggan, $nota, $item_nota);
                }
            }
        }

        $pesanan = Pesanan::with(["item_pesanan", "item_pesanan.produk", "transaksi", 'pelanggan'])->where('pesanan_id', $request->order_id)->first();

        return response()->json([
            'message' => 'OK',
            'data' => $pesanan
        ]);
    }
}
