<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrToken;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HomeController extends Controller
{
    public function index()
    {
        // Generate QR jika belum ada yang aktif
        $today = now()->toDateString();
        $qrToken = QrToken::where('tanggal', $today)
            ->where('expired_at', '>', now())
            ->orderByDesc('expired_at')
            ->first();

        if (!$qrToken || Carbon::parse($qrToken->expired_at)->lt(now())) {
            $hash = Str::random(20);
            $qrToken = QrToken::create([
                'hash' => $hash,
                'tanggal' => $today,
                'expired_at' => now()->addMinute(),
            ]);
        }

        // Generate QR code
        $qr = QrCode::size(200)->generate(route('absensi.scan', ['hash' => $qrToken->hash]));

        return view('home', compact('qr', 'qrToken'));
    }
    public function landing()
{
    $today = now()->toDateString();

    // QR terakhir hari ini yang belum expired
    $qrToken = QrToken::where('tanggal', $today)
        ->where('expired_at', '>', now())
        ->orderByDesc('expired_at')
        ->first();

    // Kalau tidak ada, buat baru
    if (!$qrToken || Carbon::parse($qrToken->expired_at)->lt(now())) {
        $hash = Str::random(20);
        $qrToken = QrToken::create([
            'hash' => $hash,
            'tanggal' => $today,
            'expired_at' => now()->addSeconds(15),
        ]);
    }

    $qr = QrCode::size(250)->generate(route('absensi.scan', ['hash' => $qrToken->hash]));

    return view('landing', compact('qr', 'qrToken'));
}
}
