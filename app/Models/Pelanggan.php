<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pelanggan extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pelanggan';

    protected $fillable = [
        'id',
        'nama',
        'kode',
        'jenis_kode',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pelanggan) {
            if (empty($pelanggan->id)) {
                $pelanggan->id = (string) Str::uuid();
            }
        });
    }
}
