<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\PembelianProduk;
use App\Models\ItemPesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RingkasanController extends Controller
{
    // Fungsi untuk mengambil chart keuangan per hari
    public function getChartData(): array
    {
        // Mengambil data omzet per hari (total_akhir dari pesanan)
        $omzetData = Pesanan::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_akhir) as omzet')
        )
            ->where('is_deleted', false)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // Mengambil data pengeluaran per hari (total dari pembelian_produk)
        $pengeluaranData = PembelianProduk::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as pengeluaran')
        )
            ->where('is_deleted', false)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // Mengambil total produk terjual per hari (jumlah dari item_pesanan.jumlah)
        $totalProdukTerjualData = ItemPesanan::select(
            DB::raw('DATE(item_pesanan.created_at) as date'),
            DB::raw('SUM(item_pesanan.jumlah) as total_terjual')
        )
            ->join('pesanan', 'pesanan.id', '=', 'item_pesanan.pesanan_id')
            ->where('item_pesanan.is_deleted', false)
            ->groupBy(DB::raw('DATE(item_pesanan.created_at)'))
            ->get();

        // Mengambil total pembelian produk per hari (jumlah dari pembelian_produk.jumlah)
        $totalPembelianData = PembelianProduk::select(
            DB::raw('DATE(pembelian_produk.created_at) as date'),
            DB::raw('SUM(pembelian_produk.jumlah) as total_pembelian')
        )
            ->where('pembelian_produk.is_deleted', false)
            ->groupBy(DB::raw('DATE(pembelian_produk.created_at)'))
            ->get();

        // Gabungkan omzet, pengeluaran, total produk terjual, dan total pembelian menjadi satu array
        $chartData = [];

        // Inisialisasi data default untuk setiap tanggal
        foreach ($omzetData as $omzet) {
            $date = $omzet->date;
            $chartData[$date] = [
                'omzet' => $omzet->omzet,
                'pengeluaran' => 0,  // Default 0
                'total_terjual' => 0, // Default 0
                'total_pembelian' => 0, // Default 0
            ];
        }

        // Gabungkan data pengeluaran ke dalam chartData
        foreach ($pengeluaranData as $pengeluaran) {
            $date = $pengeluaran->date;
            if (!isset($chartData[$date])) {
                $chartData[$date] = [
                    'omzet' => 0,
                    'pengeluaran' => 0,
                    'total_terjual' => 0,
                    'total_pembelian' => 0,
                ];
            }
            $chartData[$date]['pengeluaran'] = $pengeluaran->pengeluaran;
        }

        // Gabungkan data produk terjual ke dalam chartData
        foreach ($totalProdukTerjualData as $produkTerjual) {
            $date = $produkTerjual->date;
            if (!isset($chartData[$date])) {
                $chartData[$date] = [
                    'omzet' => 0,
                    'pengeluaran' => 0,
                    'total_terjual' => 0,
                    'total_pembelian' => 0,
                ];
            }
            $chartData[$date]['total_terjual'] = $produkTerjual->total_terjual;
        }

        // Gabungkan data pembelian produk ke dalam chartData
        foreach ($totalPembelianData as $pembelian) {
            $date = $pembelian->date;
            if (!isset($chartData[$date])) {
                $chartData[$date] = [
                    'omzet' => 0,
                    'pengeluaran' => 0,
                    'total_terjual' => 0,
                    'total_pembelian' => 0,
                ];
            }
            $chartData[$date]['total_pembelian'] = $pembelian->total_pembelian;
        }

        // Hitung profit (omzet - pengeluaran)
        foreach ($chartData as $date => $data) {
            $chartData[$date]['profit'] = $data['omzet'] - $data['pengeluaran'];
        }

        return $chartData;
    }

    // Fungsi untuk mengambil data master (omzet, pengeluaran, profit)
    public function getMasterData(array $chart): array
    {
        $omzet = 0;
        $pengeluaran = 0;
        $profit = 0;
        $totalProdukTerjual = 0;
        $totalPembelianProduk = 0;

        foreach ($chart as $data) {
            $omzet += $data['omzet'];
            $pengeluaran += $data['pengeluaran'];
            $profit += $data['profit'];
            $totalProdukTerjual += $data['total_terjual'];
            $totalPembelianProduk += $data['total_pembelian'];
        }

        return [
            'omzet' => number_format($omzet, 2, ',', '.'),
            'pengeluaran' => number_format($pengeluaran, 2, ',', '.'),
            'profit' => number_format($profit, 2, ',', '.'),
            'total_produk_terjual' => number_format($totalProdukTerjual, 0, ',', '.'),
            'total_pembelian_produk' => number_format($totalPembelianProduk, 0, ',', '.')
        ];
    }

    // Fungsi untuk mengambil produk yang terjual berdasarkan item pesanan dan jumlah
    public function getSoldProductOrderBySold(): array
    {
        $soldProducts = ItemPesanan::select(
            'produk_id',
            DB::raw('MAX(produk.nama) as nama'),
            DB::raw('MAX(produk.gambar) as gambar'),
            DB::raw('SUM(item_pesanan.jumlah) as total_terjual')
        )
            ->join('produk', 'produk.id', '=', 'item_pesanan.produk_id')
            ->where('item_pesanan.is_deleted', false)
            ->groupBy('produk_id')
            ->orderByDesc(DB::raw('SUM(item_pesanan.jumlah)'))
            ->get();

        return $soldProducts->toArray();
    }

    // Fungsi untuk menghitung rasio dan total penjualan barang dari Item Pesanan * Jumlah
    public function getRatioSoldProduct(array $soldProducts): array
    {
        $totalSold = 0;
        $ratioData = [];

        foreach ($soldProducts as $product) {
            $totalSold += $product['total_terjual'];
        }

        foreach ($soldProducts as $product) {
            $ratioData[] = [
                'produk_id' => $product['produk_id'],
                'total_terjual' => $product['total_terjual'],
                'rasio' => number_format(($product['total_terjual'] / $totalSold) * 100, 2, ',', '.')
            ];
        }

        return $ratioData;
    }

    // MAIN
    public function index()
    {
        $chart = $this->getChartData();
        $master = $this->getMasterData($chart);
        $sold = $this->getSoldProductOrderBySold();
        $ratio = $this->getRatioSoldProduct($sold);

        $data = [
            'chart' => $chart,
            'master' => $master,
            'sold' => $sold,
            'ratio' => $ratio
        ];

        return response()->json([
            'message' => 'OK',
            'data' => $data
        ]);
    }
}
