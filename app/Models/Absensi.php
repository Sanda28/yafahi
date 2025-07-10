<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'waktu_absen',
        'latitude',
        'longitude',
        'hash',
    ];
    protected $casts = [
        'tanggal' => 'date',
        'expired_at' => 'datetime',
        'waktu_absen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function izin()
{
    return $this->hasMany(Izin::class, 'user_id', 'user_id')->where('tanggal', $this->tanggal);
}
}
