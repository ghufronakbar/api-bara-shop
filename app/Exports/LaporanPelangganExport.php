<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Pelanggan;

class LaporanPelangganExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $pelanggans;

    public function __construct($pelanggans)
    {
        $this->pelanggans = $pelanggans;
    }

    /**
     * Mengambil data koleksi yang akan diekspor
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->pelanggans;
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
            'ID Pelanggan',
            'Nama',
            'Tipe',
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
        $kontak = $row->jenis_kode == 'Email' ? $row->kode : '+' . $row->kode;
        return [
            $row->nomor, // Incremented nomor
            $row->id,
            $row->nama,
            $row->jenis_kode,
            $kontak,
            $row->pesanan_count,
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
        return 'Laporan Pelanggan ' . env("APP_NAME");
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
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Menyesuaikan lebar kolom berdasarkan panjang data
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }
}
