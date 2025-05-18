<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pesanan';
    protected $primaryKey = 'pesanan_id';


    protected $casts = [
        'total_akhir' => 'float',
        'total_harga_barang' => 'float',
        'diskon_dikenakan' => 'float',
        'pajak_dikenakan' => 'float',
        'persentase_diskon' => 'float',
        'persentase_pajak' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'pesanan_id',
        'total_akhir',
        'total_harga_barang',
        'diskon_dikenakan',
        'pajak_dikenakan',
        'deskripsi_pesanan',
        'pelanggan_id',
        'persentase_diskon',
        'persentase_pajak',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pesanan) {
            if (empty($pesanan->pesanan_id)) {
                $pesanan->pesanan_id = (string) Str::uuid();
            }
        });
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function item_pesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'pesanan_id', 'pesanan_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'pesanan_id', 'pesanan_id');
    }
}
