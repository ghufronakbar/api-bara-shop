<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\PembelianProduk;

class LaporanPembelianExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $pembelianProduks;

    public function __construct($pembelianProduks)
    {
        $this->pembelianProduks = $pembelianProduks;
    }

    /**
     * Mengambil data koleksi yang akan diekspor
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->pembelianProduks;
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
            'ID Pembelian',
            'Produk',
            'Pemasok',
            'Jumlah',
            'Harga',
            'Total',
            'Deskripsi',
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
        // Memformat harga dan total menjadi Rupiah
        $harga = number_format($row->harga, 2, ',', '.');
        $total = number_format($row->total, 2, ',', '.');
        $deskripsi = $row->deskripsi ?: '-';
        $tanggal = date('d-m-Y H:i:s', strtotime($row->created_at));

        return [
            $row->nomor, // Nomor urut
            $row->id,
            $row->produk->nama,
            $row->pemasok->nama,
            $row->jumlah,
            $harga,
            $total,
            $deskripsi,
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
        return 'Laporan Pembelian ' . env("APP_NAME");
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
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        // Menyesuaikan lebar kolom berdasarkan panjang data
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }
}
