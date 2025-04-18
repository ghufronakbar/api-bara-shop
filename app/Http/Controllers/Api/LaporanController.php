<?php

namespace App\Http\Controllers\Api;

use App\Exports\LaporanKerusakanExport;
use App\Exports\LaporanPelangganExport;
use App\Exports\LaporanPemasokExport;
use App\Exports\LaporanPembelianExport;
use App\Models\Pesanan;
use App\Models\ItemPesanan;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPenjualanExport;
use App\Exports\LaporanProdukExport;
use App\Http\Controllers\Controller;
use App\Models\CacatProduk;
use App\Models\Pemasok;
use App\Models\PembelianProduk;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Fungsi untuk mengekspor laporan penjualan ke Excel
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function laporanPenjualan(Request $request)
    {
        // Menangani query params untuk filter berdasarkan 'start' dan 'end'
        $start = $request->query('start');
        $end = $request->query('end');

        // Membuat query untuk mengambil data pesanan
        $pesananQuery = Pesanan::query()
            ->where('is_deleted', false)
            ->with(['transaksi', 'item_pesanan.produk', 'pelanggan'])
            ->select('pesanan.*');


        if ($start) {
            $pesananQuery->whereDate('created_at', '>=', $start);
        }

        if ($end) {
            $pesananQuery->whereDate('created_at', '<=', $end);
        }

        $pesanans = $pesananQuery->get();

        $nomor = 1;
        foreach ($pesanans as $pesanan) {
            $pesanan->nomor = $nomor;
            $nomor++;
        }

        // Mengirim data ke export untuk diekspor ke Excel
        return Excel::download(new LaporanPenjualanExport($pesanans), 'Laporan Penjualan ' . env('APP_NAME') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }


    /**
     * Fungsi untuk mengekspor laporan pembelian ke Excel
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function laporanPembelian(Request $request)
    {
        // Menangani query params untuk filter berdasarkan 'start' dan 'end'
        $start = $request->query('start');
        $end = $request->query('end');

        // Membuat query untuk mengambil data pembelian
        $pembelianQuery = PembelianProduk::query()
            ->where('is_deleted', false)
            ->with(['produk', 'pemasok'])
            ->select('pembelian_produk.*');

        if ($start) {
            $pembelianQuery->whereDate('created_at', '>=', $start);
        }

        if ($end) {
            $pembelianQuery->whereDate('created_at', '<=', $end);
        }

        $pembelianProduks = $pembelianQuery->get();

        // Menambahkan nomor urut
        $nomor = 1;
        foreach ($pembelianProduks as $pembelian) {
            $pembelian->nomor = $nomor;
            $nomor++;
        }

        // Mengirim data ke export untuk diekspor ke Excel
        return Excel::download(new LaporanPembelianExport($pembelianProduks), 'Laporan Pembelian ' . env('APP_NAME') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Fungsi untuk mengekspor laporan kerusakan ke Excel
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function laporanKerusakan(Request $request)
    {
        // Menangani query params untuk filter berdasarkan 'start' dan 'end'
        $start = $request->query('start');
        $end = $request->query('end');

        // Membuat query untuk mengambil data kerusakan
        $cacatProdukQuery = CacatProduk::query()
            ->where('is_deleted', false)
            ->with(['produk']) // Memuat relasi produk
            ->select('cacat_produk.*');

        if ($start) {
            $cacatProdukQuery->whereDate('created_at', '>=', $start);
        }

        if ($end) {
            $cacatProdukQuery->whereDate('created_at', '<=', $end);
        }

        $cacatProduks = $cacatProdukQuery->get();

        // Menambahkan nomor urut
        $nomor = 1;
        foreach ($cacatProduks as $cacatProduk) {
            $cacatProduk->nomor = $nomor;
            $nomor++;
        }

        // Mengirim data ke export untuk diekspor ke Excel
        return Excel::download(new LaporanKerusakanExport($cacatProduks), 'Laporan Kerusakan ' . env('APP_NAME') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }


    /**
     * Fungsi untuk mengekspor laporan produk ke Excel
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function laporanProduk(Request $request)
    {
        // Membuat query untuk mengambil data produk
        $produks = Produk::where('is_deleted', false)->get();
        $items_pesanan = ItemPesanan::where('is_deleted', false)->get();
        $cacats = CacatProduk::where('is_deleted', false)->get();
        $pembelians = PembelianProduk::where('is_deleted', false)->get();

        // Menambahkan nomor urut
        $nomor = 1;
        foreach ($produks as $produk) {
            $produk->total_terjual = $items_pesanan->where('produk_id', $produk->id)->sum('jumlah');
            $produk->total_cacat = $cacats->where('produk_id', $produk->id)->sum('jumlah');
            $produk->total_pembelian = $pembelians->where('produk_id', $produk->id)->sum('jumlah');
            $produk->nomor = $nomor;
            $nomor++;
        }

         // Create the Excel file as a stream (without writing to disk)
         $excelStream = Excel::blob(new LaporanProdukExport($produks), \Maatwebsite\Excel\Excel::XLSX);

         // Return the file as a streamed response to the browser without saving it to a temporary file
         $response = new StreamedResponse(function () use ($excelStream) {
             echo $excelStream;
         });

           // Set the response headers for file download
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Penjualan ' . env('APP_NAME') . '.xlsx"');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }


    /**
     * Fungsi untuk mengekspor laporan pelanggan ke Excel
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function laporanPelanggan(Request $request)
    {

        // Membuat query untuk mengambil data pelanggan
        $pelanggans = Pelanggan::withCount(["pesanan"])->where('is_deleted', false)->get();

        // Menambahkan nomor urut
        $nomor = 1;
        foreach ($pelanggans as $pelanggan) {
            $pelanggan->nomor = $nomor;
            $nomor++;
        }

        // Mengirim data ke export untuk diekspor ke Excel
        return Excel::download(new LaporanPelangganExport($pelanggans), 'Laporan Pelanggan ' . env('APP_NAME') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function laporanPemasok(Request $request)
    {

        // Membuat query untuk mengambil data pelanggan
        $pemasoks = Pemasok::withCount(["pembelian_produk"])->where('is_deleted', false)->get();

        // Menambahkan nomor urut
        $nomor = 1;
        foreach ($pemasoks as $pemasok) {
            $nomor++;
        }

        // Mengirim data ke export untuk diekspor ke Excel
        return Excel::download(new LaporanPemasokExport($pemasoks), 'Laporan Pemasok ' . env('APP_NAME') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function index(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $penjualanQuery = Pesanan::query()
            ->where('is_deleted', false);

        $pembelianQuery = PembelianProduk::query()
            ->where('is_deleted', false);

        $cacatProdukQuery = CacatProduk::query()
            ->where('is_deleted', false);

        if ($start) {
            $penjualanQuery->whereDate('created_at', '>=', $start);
            $pembelianQuery->whereDate('created_at', '>=', $start);
            $cacatProdukQuery->whereDate('created_at', '>=', $start);
        }

        if ($end) {
            $penjualanQuery->whereDate('created_at', '<=', $end);
            $pembelianQuery->whereDate('created_at', '<=', $end);
            $cacatProdukQuery->whereDate('created_at', '<=', $end);
        }

        $penjualan = $penjualanQuery->count();
        $pembelian = $pembelianQuery->count();
        $kerusakan = $cacatProdukQuery->count();

        $produk = Produk::where('is_deleted', false)->count();
        $pemasok = Pemasok::where('is_deleted', false)->count();
        $pelanggan = Pelanggan::where('is_deleted', false)->count();


        $data = [
            'penjualan' => $penjualan,
            'pembelian' => $pembelian,
            'kerusakan' => $kerusakan,
            'produk' => $produk,
            'pemasok' => $pemasok,
            'pelanggan' => $pelanggan
        ];

        return response()->json([
            'message' => 'OK',
            'data' => $data
        ]);
    }

    public function test(Request $request)
    {
        // Menangani query params untuk filter berdasarkan 'start' dan 'end'
        $start = $request->query('start');
        $end = $request->query('end');

        // Membuat query untuk mengambil data pesanan
        $pesananQuery = Pesanan::query()
            ->where('is_deleted', false)
            ->with(['transaksi', 'item_pesanan.produk', 'pelanggan'])
            ->select('pesanan.*');

        if ($start) {
            $pesananQuery->whereDate('created_at', '>=', $start);
        }

        if ($end) {
            $pesananQuery->whereDate('created_at', '<=', $end);
        }

        $pesanans = $pesananQuery->get();

        $nomor = 1;
        foreach ($pesanans as $pesanan) {
            $pesanan->nomor = $nomor;
            $nomor++;
        }

        // Initialize Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Pesanan');
        $sheet->setCellValue('C1', 'Pelanggan');
        $sheet->setCellValue('D1', 'Metode Pembayaran');
        $sheet->setCellValue('E1', 'Produk');
        $sheet->setCellValue('F1', 'Subtotal');
        $sheet->setCellValue('G1', 'Diskon');
        $sheet->setCellValue('H1', 'Pajak');
        $sheet->setCellValue('I1', 'Total');
        $sheet->setCellValue('J1', 'Tanggal');

        $row = 2; // Start from second row

        // Loop through the data and populate the sheet
        foreach ($pesanans as $pesanan) {
            $sheet->setCellValue('A' . $row, $pesanan->nomor);
            $sheet->setCellValue('B' . $row, $pesanan->id);
            $sheet->setCellValue('C' . $row, $pesanan->pelanggan ? $pesanan->pelanggan->nama . ' (' . $pesanan->pelanggan->kode . ')' : '-');
            $sheet->setCellValue('D' . $row, $pesanan->transaksi ? $pesanan->transaksi->metode : '-');

            // Format the product data (mapping ItemPesanan)
            $produkData = $pesanan->item_pesanan->map(function ($item) {
                return $item->produk->nama . ' x ' . $item->jumlah . ' (@' . number_format($item->harga, 0, ',', '.') . ')';
            })->join(', ');
            $sheet->setCellValue('E' . $row, $produkData);

            $sheet->setCellValue('F' . $row, number_format($pesanan->total_sementara, 0, ',', '.'));
            $sheet->setCellValue('G' . $row, number_format($pesanan->diskon, 0, ',', '.') . ' (' . $pesanan->persentase_diskon . '%)');
            $sheet->setCellValue('H' . $row, number_format($pesanan->pajak, 0, ',', '.') . ' (' . $pesanan->persentase_pajak . '%)');
            $sheet->setCellValue('I' . $row, number_format($pesanan->total_akhir, 0, ',', '.'));
            $sheet->setCellValue('J' . $row, date('d-m-Y H:i:s', strtotime($pesanan->created_at)));

            $row++;
        }

        // Create the response and stream the file directly
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output'); // Stream directly to the browser
        });

        // Set the headers for the response to indicate it's an Excel file
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Penjualan ' . env('APP_NAME') . '.xlsx"');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }
}
