<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'hash',
        'tanggal',
        'expired_at',
    ];

    protected $dates = ['expired_at'];
}
