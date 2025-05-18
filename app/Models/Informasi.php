<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Informasi extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'informasi';
    protected $primaryKey = 'informasi_id';

    protected $casts = [
        'persentase_pajak' => 'float',
        'persentase_diskon' => 'float',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'informasi_id',
        'persentase_pajak',
        'persentase_diskon',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($informasi) {
            if (empty($informasi->informasi_id)) {
                $informasi->informasi_id = (string) Str::uuid();
            }
        });
    }
}
