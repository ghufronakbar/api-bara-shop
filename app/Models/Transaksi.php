<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaksi extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'transaksi';

    protected $casts = [
        'jumlah' => 'float',
    ];

    protected $fillable = [
        'id',
        'jumlah',
        'metode',
        'status',
        'detail',
        'snap_token',
        'url_redirect',
        'pesanan_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pesanan) {
            if (empty($pesanan->id)) {
                $pesanan->id = (string) Str::uuid();
            }
        });
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
