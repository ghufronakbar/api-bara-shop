<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PesanTerkirim extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'pesan_terkirim';
    protected $primaryKey = 'pesan_terkirim_id';


    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'pesan_terkirim_id',
        'subjek_pesan',
        'isi_pesan',
        'user_id',
        'is_deleted',
    ];

    protected static function booted()
    {
        static::creating(function ($pesanan_terkirim) {
            if (empty($pesanan_terkirim->pesan_terkirim_id)) {
                $pesanan_terkirim->pesan_terkirim_id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pelanggan()
    {
        return $this->belongsToMany(Pelanggan::class, 'pesan_terkirim_pelanggan', 'pesan_terkirim_id', 'pelanggan_id');
    }
}
