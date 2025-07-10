<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Libur extends Model
{
    // Tentukan tabel yang digunakan jika tabel tidak sesuai dengan nama model
    protected $table = 'liburs';

    // Tentukan kolom yang dapat diisi (mass assignment)
    protected $fillable = ['tanggal_mulai', 'tanggal_selesai', 'keterangan'];

    // Tentukan format tanggal agar 'tanggal' di-cast ke Carbon instance
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

}
