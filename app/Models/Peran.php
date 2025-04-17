<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Peran extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'peran';

    protected $casts = [
        'ringkasan' => 'boolean',
        'laporan' => 'boolean',
        'informasi' => 'boolean',
        'kirim_pesan' => 'boolean',
        'pengguna' => 'boolean',
        'peran' => 'boolean',
        'pelanggan' => 'boolean',
        'produk' => 'boolean',
        'pemasok' => 'boolean',
        'riwayat_pesanan' => 'boolean',
        'pembelian' => 'boolean',
        'cacat_produk' => 'boolean',
        'kasir' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'nama',
        'ringkasan',
        'laporan',
        'informasi',
        'kirim_pesan',
        'pengguna',
        'peran',
        'pelanggan',
        'produk',
        'pemasok',
        'riwayat_pesanan',
        'pembelian',
        'cacat_produk',
        'kasir',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($peran) {
            if (empty($peran->id)) {
                $peran->id = (string) Str::uuid();
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
