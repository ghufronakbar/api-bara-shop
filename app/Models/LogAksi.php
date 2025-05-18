<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LogAksi extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'log_aksi';

    protected $primaryKey = 'log_aksi_id';

    protected $casts = [
        'detail_aksi' => 'array',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'log_aksi_id',
        'deskripsi_aksi',
        'detail_aksi',
        'referensi_id',
        'model_referensi',
        'jenis_aksi',
        'user_id',
        'is_deleted',
    ];

      protected static function booted()
    {
        static::creating(function ($log) {
            if (empty($log->log_aksi_id)) {
                $log->log_aksi_id = (string) Str::uuid();
            }
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
