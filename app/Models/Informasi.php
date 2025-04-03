<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Informasi extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'informasi';

    protected $fillable = [
        'id',
        'pajak',
        'diskon',
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
