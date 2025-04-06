<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ItemPesanan extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'item_pesanan';

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
        'pesanan_id',
        'produk_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($item_pesanan) {
            if (empty($item_pesanan->id)) {
                $item_pesanan->id = (string) Str::uuid();
            }
        });
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
