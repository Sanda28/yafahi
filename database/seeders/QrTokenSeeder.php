<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QrToken;
use Illuminate\Support\Str;

class QrTokenSeeder extends Seeder
{
    public function run(): void
    {
        QrToken::create([
            'hash' => Str::random(20),
            'tanggal' => now()->toDateString(),
            'expired_at' => now()->addSeconds(15),
        ]);
    }
}
