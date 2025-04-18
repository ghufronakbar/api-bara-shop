<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $pesanans;

    public function __construct($pesanans)
    {
        $this->pesanans = $pesanans;
    }

    /**
     * Mengambil data koleksi yang akan diekspor
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->pesanans;
    }

    /**
     * Menambahkan judul untuk setiap kolom di Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'ID Pesanan',
            'Pelanggan',
            'Metode Pembayaran',
            'Produk',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Total',
            'Tanggal'
        ];
    }

    /**
     * Memetakan data untuk setiap baris dalam Excel
     *
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        // Memeriksa apakah itemPesanan tidak kosong atau null
        $produkData = $row->itemPesanan ? $row->itemPesanan->map(function ($item) {
            return $item->produk->nama . ' x ' . $item->jumlah . ' (@' . number_format($item->harga, 0, ',', '.') . ')';
        })->join(', ') : 'Tidak ada produk';

        // Memformat harga menjadi Rupiah (termasuk angka desimal)
        $subtotal = number_format($row->total_sementara, 2, ',', '.');
        $diskon = number_format($row->diskon, 2, ',', '.') . ' (' . $row->persentase_diskon . '%)';
        $pajak = number_format($row->pajak, 2, ',', '.') . ' (' . $row->persentase_pajak . '%)';
        $total = number_format($row->total_akhir, 2, ',', '.');
        $tanggal = date('d-m-Y H:i:s', strtotime($row->created_at));

        return [
            $row->nomor,
            $row->id,
            $row->pelanggan ? $row->pelanggan->nama . ' (' . $row->pelanggan->kode . ')' : '-',
            $row->transaksi ? $row->transaksi->metode : '-',
            $produkData,
            $subtotal,
            $diskon,
            $pajak,
            $total,
            $tanggal
        ];
    }

    /**
     * Menambahkan judul sheet Excel
     *
     * @return string
     */
    public function title(): string
    {
        return 'Laporan Penjualan ' . env("APP_NAME");
    }

    /**
     * Memberikan gaya pada header kolom dan menyesuaikan lebar kolom
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Apply bold font style to the headings
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        // Menyesuaikan lebar kolom berdasarkan panjang data
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }
}
