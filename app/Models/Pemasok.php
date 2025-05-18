<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pemasok extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pemasok';

    protected $primaryKey = 'pemasok_id';

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'pemasok_id',
        'nama_pemasok',
        'alamat_pemasok',
        'telepon_pemasok',
        'logo_pemasok',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pemasok) {
            if (empty($pemasok->pemasok_id)) {
                $pemasok->pemasok_id = (string) Str::uuid();
            }
        });
    }

    public function pembelian_produk()
    {
        return $this->hasMany(PembelianProduk::class, 'pemasok_id', 'pemasok_id');
    }
}
