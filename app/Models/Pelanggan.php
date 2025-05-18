<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pelanggan extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pelanggan';

    protected $primaryKey = 'pelanggan_id';

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'pelanggan_id',
        'nama_pelanggan',
        'kode_pelanggan',
        'jenis_kode',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pelanggan) {
            if (empty($pelanggan->pelanggan_id)) {
                $pelanggan->pelanggan_id = (string) Str::uuid();
            }
        });
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
