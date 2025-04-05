<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PesanTerkirim extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pesan_terkirim';

    protected $fillable = [
        'id',
        'subjek',
        'pesan',
        'user_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pesanan_terkirim) {
            if (empty($pesanan_terkirim->id)) {
                $pesanan_terkirim->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelanggan()
    {
        return $this->belongsToMany(Pelanggan::class, 'pesan_pelanggan', 'pesan_terkirim_id', 'pelanggan_id');
    }
}
