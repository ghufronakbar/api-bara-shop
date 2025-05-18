<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CacatProduk extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'cacat_produk';

    protected $primaryKey = 'cacat_produk_id';

    protected $casts = [
        'jumlah_produk' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'cacat_produk_id',
        'jumlah_produk',
        'alasan_kerusakan',
        'produk_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($cacat_produk) {
            if (empty($cacat_produk->cacat_produk_id)) {
                $cacat_produk->cacat_produk_id = (string) Str::uuid();
            }
        });
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }
}
