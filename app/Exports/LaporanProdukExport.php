<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Produk;

class LaporanProdukExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $produks;

    public function __construct($produks)
    {
        $this->produks = $produks;
    }

    /**
     * Mengambil data koleksi yang akan diekspor
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->produks;
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
            'ID Produk',
            'Nama',
            'Kategori',
            'Harga',
            'Harga Rata-Rata Pembelian',
            'Stok',
            'Terjual',
            'Pembelian',
            'Kerusakan',
            'Deskripsi',
            'Terdaftar Pada'
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
        // Memformat harga dan harga rata-rata pembelian menjadi Rupiah
        $harga = number_format($row->harga, 2, ',', '.');
        $hpp = number_format($row->hpp, 2, ',', '.');
        $jumlahStok = $row->jumlah ? $row->jumlah : 0;
        $deskripsi = $row->deskripsi ?: '-';
        $tanggal = date('d-m-Y H:i:s', strtotime($row->created_at));

        return [
            $row->nomor, // Incremented nomor
            $row->id,
            $row->nama,
            $row->kategori,
            $harga,
            $hpp,
            $jumlahStok,
            $row->total_terjual,
            $row->total_pembelian,
            $row->total_cacat,
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
        return 'Laporan Produk ' . env("APP_NAME");
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
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        // Menyesuaikan lebar kolom berdasarkan panjang data
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }
}
