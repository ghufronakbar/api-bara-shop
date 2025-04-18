<?php

namespace App\Http\Controllers\Api;

use App\Models\Pesanan;
use App\Models\ItemPesanan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CacatProduk;
use App\Models\Pemasok;
use App\Models\PembelianProduk;
use App\Models\Produk;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{

    public function laporanPenjualan(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

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

        $row = 2;

        foreach ($pesanans as $pesanan) {
            $sheet->setCellValue('A' . $row, $pesanan->nomor);
            $sheet->setCellValue('B' . $row, $pesanan->id);
            $sheet->setCellValue('C' . $row, $pesanan->pelanggan ? $pesanan->pelanggan->nama . ' (' . $pesanan->pelanggan->kode . ')' : '-');
            $sheet->setCellValue('D' . $row, $pesanan->transaksi ? $pesanan->transaksi->metode : '-');

            $produkData = $pesanan->item_pesanan->map(function ($item) {
                return $item->produk->nama . ' x ' . $item->jumlah . ' (@' . number_format($item->harga, 0, ',', '.') . ')';
            })->join(', ');
            $sheet->setCellValue('E' . $row, $produkData);

            $sheet->setCellValue('F' . $row, $pesanan->total_sementara);
            $sheet->setCellValue('G' . $row, $pesanan->diskon . ' (' . $pesanan->persentase_diskon . '%)');
            $sheet->setCellValue('H' . $row, $pesanan->pajak . ' (' . $pesanan->persentase_pajak . '%)');
            $sheet->setCellValue('I' . $row, $pesanan->total_akhir);
            $sheet->setCellValue('J' . $row, date('d-m-Y H:i:s', strtotime($pesanan->created_at)));

            $row++;
        }

        $styleArrayFirstRow = [
            'font' => [
                'bold' => true,
            ]
        ];

        $highestColumn = $sheet->getHighestColumn();

        foreach (range('A', $highestColumn) as $column) {
            $sheet->getStyle($column . '1')->applyFromArray($styleArrayFirstRow);
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Penjualan ' . env('APP_NAME') . '.xlsx"');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    public function laporanPembelian(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

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

        $nomor = 1;
        foreach ($pembelianProduks as $pembelian) {
            $pembelian->nomor = $nomor;
            $nomor++;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Pembelian');
        $sheet->setCellValue('C1', 'Produk');
        $sheet->setCellValue('D1', 'Pemasok');
        $sheet->setCellValue('E1', 'Jumlah');
        $sheet->setCellValue('F1', 'Harga');
        $sheet->setCellValue('G1', 'Total');
        $sheet->setCellValue('H1', 'Deskripsi');
        $sheet->setCellValue('I1', 'Tanggal');

        $row = 2;

        foreach ($pembelianProduks as $item) {
            $sheet->setCellValue('A' . $row, $item->nomor);
            $sheet->setCellValue('B' . $row, $item->id);
            $sheet->setCellValue('C' . $row, $item->produk->nama);
            $sheet->setCellValue('D' . $row, $item->pemasok->nama);
            $sheet->setCellValue('E' . $row, $item->jumlah);
            $sheet->setCellValue('F' . $row, $item->harga);
            $sheet->setCellValue('G' . $row, $item->total);
            $sheet->setCellValue('H' . $row, $item->deskripsi);
            $sheet->setCellValue('I' . $row, date('d-m-Y H:i:s', strtotime($item->created_at)));

            $row++;
        }

        $styleArrayFirstRow = [
            'font' => [
                'bold' => true,
            ]
        ];

        $highestColumn = $sheet->getHighestColumn();

        foreach (range('A', $highestColumn) as $column) {
            $sheet->getStyle($column . '1')->applyFromArray($styleArrayFirstRow);
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Pembelian ' . env('APP_NAME') . '.xlsx"');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    public function laporanKerusakan(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $cacatProdukQuery = CacatProduk::query()
            ->where('is_deleted', false)
            ->with(['produk'])
            ->select('cacat_produk.*');

        if ($start) {
            $cacatProdukQuery->whereDate('created_at', '>=', $start);
        }

        if ($end) {
            $cacatProdukQuery->whereDate('created_at', '<=', $end);
        }

        $cacatProduks = $cacatProdukQuery->get();

        $nomor = 1;
        foreach ($cacatProduks as $cacatProduk) {
            $cacatProduk->nomor = $nomor;
            $nomor++;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Kerusakan');
        $sheet->setCellValue('C1', 'Produk');
        $sheet->setCellValue('D1', 'Jumlah');
        $sheet->setCellValue('E1', 'Alasan');
        $sheet->setCellValue('F1', 'Tanggal');

        $row = 2;

        foreach ($cacatProduks as $item) {
            $sheet->setCellValue('A' . $row, $item->nomor);
            $sheet->setCellValue('B' . $row, $item->id);
            $sheet->setCellValue('C' . $row, $item->produk->nama);
            $sheet->setCellValue('D' . $row, $item->jumlah);
            $sheet->setCellValue('E' . $row, $item->alasan);
            $sheet->setCellValue('F' . $row, date('d-m-Y H:i:s', strtotime($item->created_at)));

            $row++;
        }

        $styleArrayFirstRow = [
            'font' => [
                'bold' => true,
            ]
        ];

        $highestColumn = $sheet->getHighestColumn();

        foreach (range('A', $highestColumn) as $column) {
            $sheet->getStyle($column . '1')->applyFromArray($styleArrayFirstRow);
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output'); // Stream directly to the browser
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Pembelian ' . env('APP_NAME') . '.xlsx"');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    public function laporanProduk(Request $request)
    {
        $produks = Produk::where('is_deleted', false)->get();
        $items_pesanan = ItemPesanan::where('is_deleted', false)->get();
        $cacats = CacatProduk::where('is_deleted', false)->get();
        $pembelians = PembelianProduk::where('is_deleted', false)->get();

        $nomor = 1;
        foreach ($produks as $produk) {
            $produk->total_terjual = $items_pesanan->where('produk_id', $produk->id)->sum('jumlah');
            $produk->total_cacat = $cacats->where('produk_id', $produk->id)->sum('jumlah');
            $produk->total_pembelian = $pembelians->where('produk_id', $produk->id)->sum('jumlah');
            $produk->nomor = $nomor;
            $nomor++;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Produk');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Kategori');
        $sheet->setCellValue('E1', 'Harga');
        $sheet->setCellValue('F1', 'Harga Rata-Rata Pembelian');
        $sheet->setCellValue('G1', 'Stok');
        $sheet->setCellValue('H1', 'Terjual');
        $sheet->setCellValue('I1', 'Pembelian');
        $sheet->setCellValue('J1', 'Kerusakan');
        $sheet->setCellValue('K1', 'Deskripsi');
        $sheet->setCellValue('L1', 'Terdaftar Pada');

        $row = 2;

        foreach ($produks as $item) {
            $sheet->setCellValue('A' . $row, $item->nomor);
            $sheet->setCellValue('B' . $row, $item->id);
            $sheet->setCellValue('C' . $row, $item->nama);
            $sheet->setCellValue('D' . $row, $item->kategori);
            $sheet->setCellValue('E' . $row, $item->harga);
            $sheet->setCellValue('F' . $row, $item->hpp);
            $sheet->setCellValue('G' . $row, $item->jumlah);
            $sheet->setCellValue('H' . $row, $item->total_terjual);
            $sheet->setCellValue('I' . $row, $item->total_pembelian);
            $sheet->setCellValue('J' . $row, $item->total_cacat);
            $sheet->setCellValue('K' . $row, $item->deskripsi ?: '-');
            $sheet->setCellValue('L' . $row, date('d-m-Y H:i:s', strtotime($item->created_at)));

            $row++;
        }

        $styleArrayFirstRow = [
            'font' => [
                'bold' => true,
            ]
        ];

        $highestColumn = $sheet->getHighestColumn();

        foreach (range('A', $highestColumn) as $column) {
            $sheet->getStyle($column . '1')->applyFromArray($styleArrayFirstRow);
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Produk ' . env('APP_NAME') . '.xlsx"');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    public function laporanPelanggan(Request $request)
    {
        $pelanggans = Pelanggan::withCount(["pesanan"])->where('is_deleted', false)->get();

        $nomor = 1;
        foreach ($pelanggans as $pelanggan) {
            $pelanggan->nomor = $nomor;
            $nomor++;
        }

        // Initialize the spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headings
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Pelanggan');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Tipe');
        $sheet->setCellValue('E1', 'Kontak');
        $sheet->setCellValue('F1', 'Total Pesanan');
        $sheet->setCellValue('G1', 'Terdaftar Pada');

        // Fill data
        $row = 2;
        foreach ($pelanggans as $item) {
            $sheet->setCellValue('A' . $row, $item->nomor);
            $sheet->setCellValue('B' . $row, $item->id);
            $sheet->setCellValue('C' . $row, $item->nama);
            $sheet->setCellValue('D' . $row, $item->jenis_kode);
            $sheet->setCellValue('E' . $row, $item->kode);
            $sheet->setCellValue('F' . $row, $item->pesanan_count);
            $sheet->setCellValue('G' . $row, date('d-m-Y H:i:s', strtotime($item->created_at)));

            $row++;
        }

        // Apply style to the first row
        $styleArrayFirstRow = [
            'font' => [
                'bold' => true,
            ]
        ];

        $highestColumn = $sheet->getHighestColumn();

        foreach (range('A', $highestColumn) as $column) {
            $sheet->getStyle($column . '1')->applyFromArray($styleArrayFirstRow);
        }

        // Create the writer and response
        $writer = new Xlsx($spreadsheet);

        // Stream the response without writing to disk
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');  // Send directly to the browser
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Pelanggan ' . env('APP_NAME') . '.xlsx"');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    public function laporanPemasok(Request $request)
    {
        $pemasoks = Pemasok::withCount(["pembelian_produk"])->where('is_deleted', false)->get();

        $nomor = 1;
        foreach ($pemasoks as $pemasok) {
            $pemasok->nomor = $nomor;
            $nomor++;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Pemasok');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Kontak');
        $sheet->setCellValue('E1', 'Total Pesanan');
        $sheet->setCellValue('F1', 'Terdaftar Pada');

        $row = 2;

        foreach ($pemasoks as $item) {
            $sheet->setCellValue('A' . $row, $item->nomor);
            $sheet->setCellValue('B' . $row, $item->id);
            $sheet->setCellValue('C' . $row, $item->nama);
            $sheet->setCellValue('D' . $row, $item->telepon);
            $sheet->setCellValue('E' . $row, $item->pembelian_produk_count);
            $sheet->setCellValue('F' . $row, date('d-m-Y H:i:s', strtotime($item->created_at)));

            $row++;
        }

        $styleArrayFirstRow = [
            'font' => [
                'bold' => true,
            ]
        ];

        $highestColumn = $sheet->getHighestColumn();

        foreach (range('A', $highestColumn) as $column) {
            $sheet->getStyle($column . '1')->applyFromArray($styleArrayFirstRow);
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Pemasok ' . env('APP_NAME') . '.xlsx"');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
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
}
