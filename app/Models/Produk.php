<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produk extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'produk';

    protected $casts = [
        'jumlah' => 'float',
        'harga' => 'float',
        'hpp' => 'float',
    ];

    protected $fillable = [
        'id',
        'nama',
        'harga',
        'jumlah',
        'hpp',
        'kategori',
        'deskripsi',
        'gambar',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($produk) {
            if (empty($produk->id)) {
                $produk->id = (string) Str::uuid();
            }
        });
    }

    // Relasi jika dibutuhkan bisa ditambahkan di sini
    // public function pembelian_produk() { ... }
}
