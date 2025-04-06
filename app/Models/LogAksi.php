<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAksi extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'log_aksi';

    protected $casts = [
        'detail' => 'array',
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'deskripsi',
        'detail',
        'referensi_id',
        'model_referensi',
        'aksi',
        'user_id',
        'is_deleted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
