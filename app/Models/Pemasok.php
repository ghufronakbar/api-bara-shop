<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pemasok extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pemasok';

    protected $fillable = [
        'id',
        'nama',
        'alamat',
        'telepon',
        'gambar',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pemasok) {
            if (empty($pemasok->id)) {
                $pemasok->id = (string) Str::uuid();
            }
        });
    }

    public function pembelian_produk()
    {
        return $this->hasMany(PembelianProduk::class);
    }
}
