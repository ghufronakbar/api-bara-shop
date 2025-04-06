<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CacatProduk extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'cacat_produk';

    protected $casts = [
        'jumlah' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'jumlah',
        'alasan',
        'produk_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($cacat_produk) {
            if (empty($cacat_produk->id)) {
                $cacat_produk->id = (string) Str::uuid();
            }
        });
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
