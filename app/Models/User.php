<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'users';

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'nama',
        'email',
        'password',
        'peran_id',
        'is_deleted',
    ];

    protected $hidden = [
        'password',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    public function log_aksi()
    {
        return $this->hasMany(LogAksi::class);
    }

    public function peran()
{
    return $this->belongsTo(Peran::class);
}

}
