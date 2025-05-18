<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produk extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'produk';
    protected $primaryKey = 'produk_id';

    protected $casts = [
        'jumlah_stok' => 'float',
        'harga_produk' => 'float',
        'hpp' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'produk_id',
        'nama_produk',
        'harga_produk',
        'jumlah_stok',
        'hpp',
        'kategori_produk',
        'deskripsi_produk',
        'foto_produk',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($produk) {
            if (empty($produk->produk_id)) {
                $produk->produk_id = (string) Str::uuid();
            }
        });
    }

    public function item_pesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'produk_id', 'produk_id');
    }
}
