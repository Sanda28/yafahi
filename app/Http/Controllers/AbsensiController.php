<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\QrToken;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\ActivityLog;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Jadwal;
use App\Models\Tahunajaran;
use App\Models\Libur;
use App\Models\Izin;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AbsensiController extends Controller
{
    // ✅ User: Tampilkan data absensi pribadi
    public function index(Request $request)
    {
        $user = Auth::user();

        // Admin / Superadmin: tampilkan semua absensi
        if (in_array($user->role, ['admin', 'superadmin'])) {
            $absensis = Absensi::with('user')->orderByDesc('tanggal')->get();
            return view('absensi.index', compact('absensis'));
        }

        // ==== UNTUK USER ====
        $bulan = $request->input('bulan') ?? now()->format('Y-m');
        $carbonBulan = Carbon::parse($bulan . '-01');
        $startOfMonth = $carbonBulan->copy()->startOfMonth();
        $endOfMonth = $carbonBulan->copy()->endOfMonth();

        // Buat daftar tanggal
        $tanggalList = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $tanggalList->push($date->copy());
        }

        // Ambil semua jadwal dari relasi
        $user->load('jadwals');
        $jadwalHari = [];
        foreach ($user->jadwals as $jadwal) {
            $hariArray = is_string($jadwal->hari) ? json_decode($jadwal->hari, true) : [];
            if (is_array($hariArray)) {
                $jadwalHari = array_merge($jadwalHari, $hariArray);
            }
        }
        $jadwalHari = array_unique($jadwalHari);

        // Absensi
        $absensis = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(fn($a) => Carbon::parse($a->tanggal)->toDateString());

        // Izin (fix: termasuk izin yg melewati rentang bulan)
        $izins = Izin::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('tanggal_mulai', '<=', $endOfMonth)
                ->where(function ($sub) use ($startOfMonth) {
                    $sub->whereNull('tanggal_selesai')
                        ->orWhere('tanggal_selesai', '>=', $startOfMonth);
                });
            })->get();

        $izinByDate = [];
        foreach ($izins as $izin) {
            $mulai = Carbon::parse($izin->tanggal_mulai);
            $selesai = Carbon::parse($izin->tanggal_selesai ?? $izin->tanggal_mulai);
            for ($tgl = $mulai->copy(); $tgl->lte($selesai); $tgl->addDay()) {
                $izinByDate[$tgl->toDateString()] = $izin;
            }
        }

        // Libur
        $liburs = Libur::where(function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereDate('tanggal_mulai', '<=', $endOfMonth)
            ->whereDate('tanggal_selesai', '>=', $startOfMonth);
        })->get();

        // Proses kalender
        $dataKalender = [];

        foreach ($tanggalList as $tgl) {
            $tanggal = $tgl->toDateString();
            $hari = $tgl->translatedFormat('l');

            $isLibur = $liburs->contains(function ($libur) use ($tgl) {
                $mulai = Carbon::parse($libur->tanggal_mulai);
                $selesai = $libur->tanggal_selesai ? Carbon::parse($libur->tanggal_selesai) : $mulai;
                return $mulai->lte($tgl) && $selesai->gte($tgl);
            });

            // Hari libur, Minggu, atau tidak ada jadwal kerja
            if ($hari === 'Minggu' || !in_array($hari, $jadwalHari) || $isLibur) {
                $dataKalender[$tanggal] = '-';
                continue;
            }

            // Ambil data absensi dan izin
            $absen = $absensis->get($tanggal);
            $izin = $izinByDate[$tanggal] ?? null;

            if ($absen && !empty($absen->waktu_absen) && $absen->waktu_absen !== '00:00:00') {
                $dataKalender[$tanggal] = 'H'; // Hadir
            } elseif ($izin) {
                $dataKalender[$tanggal] = match ($izin->kategori) {
                    'Sakit' => 'S',
                    'Izin/Cuti' => 'I',
                    default => 'X',
                };
            }  elseif ($tgl->lt(now())) {
                $dataKalender[$tanggal] = 'X'; // Alfa (tanggal sudah lewat, tidak absen dan tidak izin)
            } else {
                $dataKalender[$tanggal] = ''; // Tanggal masa depan, belum bisa dinilai
            }
        }

        return view('absensi.user', compact('dataKalender', 'carbonBulan', 'jadwalHari', 'bulan'));
    }
    public function getData()
    {
        $query = Absensi::with('user')->orderByDesc('tanggal');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal', fn($q) => \Carbon\Carbon::parse($q->tanggal)->format('d M Y'))
            ->editColumn('waktu_absen', fn($q) => $q->waktu_absen ? \Carbon\Carbon::parse($q->waktu_absen)->format('H:i:s') : '-')
            ->addColumn('nama', fn($q) => $q->user->name ?? '-')
            ->addColumn('aksi', function ($q) {
                $edit = '<a href="'.route('absensi.edit', $q->id).'" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil-square"></i> Edit</a>';
                $hapus = '<form method="POST" action="'.route('absensi.destroy', $q->id).'" style="display:inline;" onsubmit="return confirm(\'Yakin?\')">'.csrf_field().method_field('DELETE').'<button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button></form>';
                return $edit.$hapus;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function showScanKamera()
    {
        $user = auth()->user();
        $today = now()->startOfDay();
        $hariIni = ucwords(now()->locale('id')->dayName);

        $tahunAjaranAktif = TahunAjaran::where('status', 'aktif')->first();

        $jadwal = $jadwalHariIni = null;
        if ($tahunAjaranAktif) {
            $jadwal = Jadwal::where('user_id', $user->id)
                ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->first();

            if ($jadwal) {
                $hariArray = json_decode($jadwal->hari, true);
                if (is_array($hariArray) && in_array($hariIni, $hariArray)) {
                    $jadwalHariIni = $hariIni;
                }
            }
        }

        $hasJadwalHariIni = !is_null($jadwalHariIni);

        $libur = Libur::whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists();

        $izin = Izin::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists();

        $absensi = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $isBlocked = false;
        $reason = null;

        if ($libur) {
            $isBlocked = true;
            $reason = 'Hari ini adalah hari libur.';
        } elseif ($izin) {
            $isBlocked = true;
            $reason = 'Anda sedang izin/cuti.';
        } elseif (!$hasJadwalHariIni) {
            $isBlocked = true;
            $reason = 'Anda tidak dijadwalkan hari ini.';
        } elseif ($absensi && $absensi->waktu_absen) {
            $isBlocked = true;
            $reason = 'Anda sudah absen hari ini.';
        }

        return view('absensi.scan_kamera', compact('isBlocked', 'reason'));
    }


    // ✅ Proses absen masuk
    public function masuk()
    {
        $today = now();
        $hariIni = $today->locale('id')->translatedFormat('l'); // Pastikan hari dalam bahasa Indonesia
        $tanggal = $today->toDateString();
        $jamSekarang = $today->format('H:i');

        // Cek apakah hari ini hari libur (Hari Minggu)
        if ($hariIni == 'Minggu') {
            return back()->with('error', 'Hari ini libur, tidak bisa absen.');
        }

        // Cek apakah ada jadwal mengajar hari ini
        $hariMengajar = $this->getHariMengajar(Auth::user());

        if (!in_array($hariIni, $hariMengajar)) {
            return back()->with('error', 'Hari ini Anda tidak dijadwalkan mengajar.');
        }



        // Cek apakah hari ini ada dalam jadwal mengajar
        if (!in_array($hariIni, $hariMengajar)) {
            return back()->with('error', 'Hari ini Anda tidak dijadwalkan mengajar.');
        }

        // Cek apakah ada izin hari ini
        $izin = Izin::where('user_id', Auth::id())
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->first();

        if ($izin) {
            return back()->with('error', 'Anda sedang dalam izin hari ini.');
        }

        // Cek waktu absen (hanya jam 07:00 - 12:00)
        $mulai = Carbon::createFromTimeString('07:00');
        $akhir = Carbon::createFromTimeString('12:00');

        if ($today->lt($mulai) || $today->gt($akhir)) {
            return back()->with('error', 'Absen hanya diperbolehkan antara jam 07:00 - 12:00');
        }

        // Cek apakah sudah ada absensi untuk hari ini
        $absen = Absensi::firstOrCreate([
            'user_id' => Auth::id(),
            'tanggal' => $tanggal,
        ]);

        // Proses absen masuk
        if (!$absen->waktu_absen) {
            $absen->update(['waktu_absen' => $today->format('H:i:s')]);
            return back()->with('success', 'Absen masuk berhasil');
        }

        return back()->with('info', 'Anda sudah absen masuk hari ini');
    }
    // ✅ Scan QR untuk absensi
    public function scanQR($hash, Request $request)
    {
        // Cek token QR valid & belum expired
        $qrToken = QrToken::where('hash', $hash)
            ->where('expired_at', '>=', now())
            ->first();

        if (!$qrToken) {
            return redirect()->route('absensi.index')->with('error', 'QR tidak valid atau sudah kedaluwarsa');
        }

        $today = now();
        $hariIni = $today->locale('id')->translatedFormat('l');
        $tanggal = $today->toDateString();

        // Cek hari libur dan hari Minggu
        $libur = Libur::whereDate('tanggal_mulai', '<=', $tanggal)
                    ->whereDate('tanggal_selesai', '>=', $tanggal)
                    ->exists();

        if ($hariIni === 'Minggu' || $libur) {
            return back()->with('error', 'Hari ini libur, tidak bisa absen.');
        }

        // Cek jadwal mengajar hari ini
        $hariMengajar = $this->getHariMengajar(Auth::user());
        if (!in_array($hariIni, $hariMengajar)) {
            return back()->with('error', 'Hari ini Anda tidak dijadwalkan mengajar.');
        }

        // Cek izin disetujui hari ini
        $izin = Izin::where('user_id', Auth::id())
            ->where('status', 'Disetujui')
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->exists();

        if ($izin) {
            return back()->with('error', 'Anda sedang izin hari ini dan tidak bisa absen.');
        }

        // Validasi waktu absen (jam 07:00 - 12:00)
        $mulai = Carbon::createFromTimeString('07:00');
        $akhir = Carbon::createFromTimeString('23:00');

        if ($today->lt($mulai) || $today->gt($akhir)) {
            return back()->with('error', 'Absen hanya diperbolehkan antara jam 07:00 - 12:00');
        }

        // Validasi lokasi GPS
        $userLat = $request->input('latitude');
        $userLng = $request->input('longitude');

        if (!$userLat || !$userLng) {
            return redirect()->route('absensi.index')->with('error', 'Koordinat tidak tersedia. Aktifkan GPS Anda.');
        }

        // Koordinat sekolah (ganti sesuai lokasi sebenarnya)
        $schoolLat = -6.57825;
        $schoolLng = 106.7230961;

        $distance = $this->distanceInMeters($userLat, $userLng, $schoolLat, $schoolLng);

        if ($distance > 1000) {
            return redirect()->route('absensi.index')->with('error', 'Anda berada di luar area sekolah. Jarak: ' . round($distance) . ' meter');
        }

        // Simpan data absensi, jika belum ada hari ini
        $absen = Absensi::firstOrCreate(
            ['user_id' => Auth::id(), 'tanggal' => $tanggal],
            ['status' => null]
        );

        // Cek QR sudah dipakai di sesi ini atau belum
        if (session('qr_used') === $hash) {
            return redirect()->route('absensi.index')->with('info', 'QR sudah digunakan sebelumnya');
        }

        // Jika belum ada waktu_absen, simpan data absensi masuk
        if (!$absen->waktu_absen) {
            $absen->waktu_absen = $today->format('H:i:s');
            $absen->latitude = $userLat;
            $absen->longitude = $userLng;
            $absen->save();

            session(['qr_used' => $hash]);
            return redirect()->route('absensi.index')->with('success', 'Absen masuk berhasil via QR');
        }

        // Jika sudah absen hari ini
        return redirect()->route('absensi.index')->with('info', 'Absen Anda sudah lengkap hari ini');
    }

    // Fungsi hitung jarak antara dua koordinat (meter)
    private function distanceInMeters($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    private function getHariMengajar($user)
    {
        $jadwal = $user->jadwals()->get();

        return $jadwal->flatMap(function ($item) {
            return json_decode($item->hari, true);
        })->unique()->values()->toArray();
    }
    // ✅ Generate QR untuk absensi
    public function generateQr()
    {
        $today = now()->toDateString();

        $qrToken = QrToken::where('tanggal', $today)
            ->where('expired_at', '>', now())
            ->orderByDesc('expired_at')
            ->first();

        if (!$qrToken) {
            QrToken::where('tanggal', $today)->delete();

            $qrToken = QrToken::create([
                'hash' => Str::random(20),
                'tanggal' => $today,
                'expired_at' => now()->addSeconds(15),
            ]);
        }

        $qr = QrCode::size(200)->generate(route('absensi.scan', ['hash' => $qrToken->hash]));
        $absensiToday = Absensi::whereDate('tanggal', $today)->get();

        return view('absensi.generate-qr', compact('qr', 'absensiToday'));
    }

    public function refreshQr()
    {
        $today = now()->toDateString();

        $qrToken = QrToken::where('tanggal', $today)
            ->where('expired_at', '>', now())
            ->orderByDesc('expired_at')
            ->first();

        if (!$qrToken) {
            QrToken::where('tanggal', $today)->delete();

            $qrToken = QrToken::create([
                'hash' => Str::random(20),
                'tanggal' => $today,
                'expired_at' => now()->addSeconds(15),
            ]);
        }

        $qr = QrCode::size(200)->generate(route('absensi.scan', ['hash' => $qrToken->hash]));

        $absensiToday = Absensi::whereDate('tanggal', $today)->get();
        $absensiHtml = '';

        if ($absensiToday->isEmpty()) {
            $absensiHtml .= '<tr><td colspan="3" class="text-center">Belum ada absensi hari ini.</td></tr>';
        } else {
            foreach ($absensiToday as $index => $absen) {
                $absensiHtml .= '<tr>';
                $absensiHtml .= '<td>' . ($index + 1) . '</td>';
                $absensiHtml .= '<td>' . ($absen->user->name ?? '-') . '</td>';
                $absensiHtml .= '<td>' . $absen->waktu_absen->format('H:i:s') . '</td>';
                $absensiHtml .= '</tr>';
            }
        }

        return response()->json([
            'qr' => (string) $qr, // ← solusi penting
            'absensi' => $absensiHtml,
        ]);
    }

    public function create()
    {
        $users = User::where('role', 'user')->get();
        return view('absensi.create', compact('users'));
    }
    // Simpan absensi yang diinput manual oleh admin
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'waktu_absen' => 'nullable|date_format:H:i',
        ]);

        $user = User::findOrFail($request->user_id);

        // Cek apakah sudah absen
        $cek = Absensi::where('user_id', $user->id)
                    ->where('tanggal', $request->tanggal)
                    ->first();

        if ($cek) {
            return redirect()->back()->withInput()->with('error', 'Data absensi untuk user dan tanggal tersebut sudah ada.');
        }

        $hari = Carbon::parse($request->tanggal)->locale('id')->translatedFormat('l');

        // Cek apakah user sedang izin di tanggal ini
        $izin = Izin::where('user_id', $user->id)
                    ->whereDate('tanggal_mulai', '<=', $request->tanggal)
                    ->whereDate('tanggal_selesai', '>=', $request->tanggal)
                    ->exists();

        if ($izin) {
            return redirect()->back()->withInput()->with('error', 'Guru telah izin pada hari ini.');
        }

        // Cek apakah punya jadwal di hari itu
        $jadwals = $user->jadwals;
        $punyaJadwal = false;

        foreach ($jadwals as $jadwal) {
            $hariArray = json_decode($jadwal->hari, true);
            if (is_array($hariArray) && in_array($hari, $hariArray)) {
                $punyaJadwal = true;
                break;
            }
        }

        if (!$punyaJadwal) {
            return redirect()->back()->withInput()->with('error', "User tidak memiliki jadwal di hari $hari.");
        }

        // Simpan absensi
        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $request->tanggal,
            'waktu_absen' => $request->waktu_absen ?? '07:00',
            'latitude'    => -6.57825,
            'longitude'   => 106.7230961,
        ]);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil ditambahkan.');
    }

    // ✅ Admin edit absensi
    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        return view('absensi.edit', compact('absensi'));
    }

    // ✅ Admin update absensi
    public function update(Request $request, $id)
    {
        $absensi = Absensi::findOrFail($id);
        $validated = $request->validate([
            'waktu_absen' => 'nullable|date_format:H:i', // Mengganti jam_masuk dengan waktu_absen
        ]);

        // Periksa perubahan dan simpan log
        $perubahan = [];
        foreach ($validated as $key => $value) {
            if ($absensi->$key != $value) {
                $perubahan[] = ucfirst($key) . ": {$absensi->$key} → {$value}";
            }
        }

        $absensi->update($validated);

        // Log aktivitas
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'update',
            'model' => 'Absensi',
            'model_id' => $absensi->id,
            'description' => 'Memperbarui absensi: ' . implode(', ', $perubahan),
        ]);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    // ✅ Admin hapus absensi
    public function destroy($id)
    {
        $absen = Absensi::findOrFail($id);

        // Log aktivitas
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'delete',
            'model' => 'Absensi',
            'model_id' => $absen->id,
            'description' => 'Menghapus absensi pada tanggal ' . $absen->tanggal->format('d-m-Y'),
        ]);

        $absen->delete();
        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil dihapus.');
    }
}
