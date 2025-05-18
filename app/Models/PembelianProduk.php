<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembelianProduk extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pembelian_produk';

    protected $primaryKey = 'pembelian_produk_id';

    protected $casts = [
        'jumlah_pembelian' => 'float',
        'harga_per_barang' => 'float',
        'total_harga' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'pembelian_produk_id',
        'jumlah_pembelian',
        'harga_per_barang',
        'total_harga',
        'deskripsi_pembelian',
        'produk_id',
        'pemasok_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pembelianProduk) {
            if (empty($pembelianProduk->pembelian_produk_id)) {
                $pembelianProduk->pembelian_produk_id = (string) Str::uuid();
            }
        });
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'pemasok_id', 'pemasok_id');
    }
}
