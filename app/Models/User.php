<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Jadwal;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'nik', 'jadwal', 'jabatan',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'jadwal' => 'array',
    ];

    public function absensis() { return $this->hasMany(Absensi::class); }
    public function izins() { return $this->hasMany(Izin::class); }
    public function jadwals() { return $this->hasMany(Jadwal::class); }
}
