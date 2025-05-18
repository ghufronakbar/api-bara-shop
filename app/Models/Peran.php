<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Peran extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'peran';
    protected $primaryKey = 'peran_id';

    protected $casts = [
        'akses_ringkasan' => 'boolean',
        'akses_laporan' => 'boolean',
        'akses_informasi' => 'boolean',
        'akses_kirim_pesan' => 'boolean',
        'akses_pengguna' => 'boolean',
        'akses_peran' => 'boolean',
        'akses_pelanggan' => 'boolean',
        'akses_produk' => 'boolean',
        'akses_pemasok' => 'boolean',
        'akses_riwayat_pesanan' => 'boolean',
        'akses_pembelian' => 'boolean',
        'akses_cacat_produk' => 'boolean',
        'akses_kasir' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'peran_id',
        'nama_peran',
        'akses_ringkasan',
        'akses_laporan',
        'akses_informasi',
        'akses_kirim_pesan',
        'akses_pengguna',
        'akses_peran',
        'akses_pelanggan',
        'akses_produk',
        'akses_pemasok',
        'akses_riwayat_pesanan',
        'akses_pembelian',
        'akses_cacat_produk',
        'akses_kasir',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($peran) {
            if (empty($peran->peran_id)) {
                $peran->peran_id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi dengan model User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

