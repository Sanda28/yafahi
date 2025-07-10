<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Izin extends Model
{
    protected $fillable = ['user_id', 'tanggal_mulai','tanggal_selesai', 'kategori', 'keterangan', 'status'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
