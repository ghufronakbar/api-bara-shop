<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembelianProduk extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pembelian_produk';

    protected $casts = [
        'jumlah' => 'float',
        'harga' => 'float',
        'total' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'jumlah',
        'harga',
        'total',
        'deskripsi',
        'produk_id',
        'pemasok_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pembelianProduk) {
            if (empty($pembelianProduk->id)) {
                $pembelianProduk->id = (string) Str::uuid();
            }
        });
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class);
    }
}
