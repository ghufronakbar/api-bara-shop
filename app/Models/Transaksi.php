<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaksi extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';


    protected $casts = [
        'jumlah_pembayaran' => 'float',
        'detail_transaksi' => 'array',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'transaksi_id',
        'jumlah_pembayaran',
        'metode_pembayaran',
        'status_pembayaran',
        'detail_transaksi',
        'midtrans_snap_token',
        'midtrans_url_redirect',
        'pesanan_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($transaksi) {
            if (empty($transaksi->transaksi_id)) {
                $transaksi->transaksi_id = (string) Str::uuid();
            }
        });
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
