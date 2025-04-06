<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pesanan';

    protected $casts = [
        'total_akhir' => 'float',
        'total_sementara' => 'float',
        'diskon' => 'float',
        'pajak' => 'float',
        'persentase_diskon' => 'float',
        'persentase_pajak' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'total_akhir',
        'total_sementara',
        'diskon',
        'pajak',
        'deskripsi',
        'pelanggan_id',
        'persentase_diskon',
        'persentase_pajak',
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

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function item_pesanan()
    {
        return $this->hasMany(ItemPesanan::class);
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class);
    }
}
