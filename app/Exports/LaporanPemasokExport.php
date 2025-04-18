<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\pemasok;

class LaporanPemasokExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $pemasoks;

    public function __construct($pemasoks)
    {
        $this->pemasoks = $pemasoks;
    }

    /**
     * Mengambil data koleksi yang akan diekspor
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->pemasoks;
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
            'ID Pemasok',
            'Nama',
            'Kontak',
            'Total Pesanan',
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
        return [
            $row->nomor, // Incremented nomor
            $row->id,
            $row->nama,
            $row->telepon,
            $row->pembelian_produk_count,
            date('d-m-Y H:i:s', strtotime($row->created_at))
        ];
    }

    /**
     * Menambahkan judul sheet Excel
     *
     * @return string
     */
    public function title(): string
    {
        return 'Laporan pemasok ' . env("APP_NAME");
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
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Menyesuaikan lebar kolom berdasarkan panjang data
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }
}
