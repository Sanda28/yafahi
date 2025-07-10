<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'mulai',
        'status',
        'selesai',
    ];

    protected $casts = [
        'mulai' => 'date',
        'selesai' => 'date',
    ];

    // Relasi ke jadwal
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'tahun_ajaran_id');
    }
}
