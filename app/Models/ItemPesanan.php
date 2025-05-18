<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ItemPesanan extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'item_pesanan';

    protected $primaryKey = 'item_pesanan_id';

    protected $casts = [
        'jumlah_barang' => 'float',
        'harga_per_barang' => 'float',
        'total_harga' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'item_pesanan_id',
        'jumlah_barang',
        'harga_per_barang',
        'total_harga',
        'pesanan_id',
        'produk_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($item_pesanan) {
            if (empty($item_pesanan->item_pesanan_id)) {
                $item_pesanan->item_pesanan_id = (string) Str::uuid();
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
