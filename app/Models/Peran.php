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
        'kelola_informasi' => 'boolean',
        'kelola_produk' => 'boolean',
        'kelola_pembelian_produk' => 'boolean',
        'kelola_cacat_produk' => 'boolean',
        'kelola_pelanggan' => 'boolean',
        'kelola_supplier' => 'boolean',
        'semua_log_aktivitas' => 'boolean',
        'kirim_pesan' => 'boolean',
        'laporan' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'nama',
        'kelola_informasi',
        'kelola_produk',
        'kelola_pembelian_produk',
        'kelola_cacat_produk',
        'kelola_pelanggan',
        'kelola_supplier',
        'semua_log_aktivitas',
        'kirim_pesan',
        'laporan',
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
