<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Izin;
use App\Models\Absensi;
use App\Models\TahunAjaran;
use App\Models\Jadwal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();
        $hariIni = $today->locale('id')->isoFormat('dddd');
        $tahunAjaranAktif = TahunAjaran::where('status', 'aktif')->first();

        if (in_array($user->role, ['admin', 'superadmin'])) {
            // Total Guru aktif (role user dengan jadwal di tahun ajaran aktif)
            $totalGuru = User::where('role', 'user')
                ->whereHas('jadwals', function ($query) use ($tahunAjaranAktif) {
                    $query->when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id));
                })->count();

            // Ambil semua guru dengan relasi jadwal, absensi hari ini, dan izin hari ini yang disetujui
            $allGuru = User::where('role', 'user')
                ->with([
                    'jadwals' => fn($q) => $q->when($tahunAjaranAktif, fn($qq) => $qq->where('tahun_ajaran_id', $tahunAjaranAktif->id)),
                    'absensis' => fn($q) => $q->whereDate('tanggal', $today),
                    'izins' => fn($q) => $q->where('status', 'Disetujui')
                        ->whereDate('tanggal_mulai', '<=', $today)
                        ->whereDate('tanggal_selesai', '>=', $today),
                ])
                ->get();

            // Filter guru dengan jadwal hari ini
            $guruDenganJadwal = $allGuru->filter(function ($guru) use ($hariIni) {
                $jadwal = $guru->jadwals->first();
                $hariArray = $jadwal ? json_decode($jadwal->hari, true) : [];
                return is_array($hariArray) && in_array($hariIni, $hariArray);
            });

            // Guru yang sudah absen dan yang belum absen dengan jadwal hari ini
            $guruSudahAbsen = $guruDenganJadwal->filter(fn($guru) => $guru->absensis->isNotEmpty());
            $guruBelumAbsen = $guruDenganJadwal->filter(fn($guru) => $guru->absensis->isEmpty());


            $jumlahHadir = $guruSudahAbsen->count();

            $jumlahIzin = $guruDenganJadwal->filter(function ($guru) {
                return $guru->izins->contains(fn($izin) => strtolower($izin->kategori) === 'izin/cuti');
            })->count();

            $jumlahSakit = $guruDenganJadwal->filter(function ($guru) {
                return $guru->izins->contains(fn($izin) => strtolower($izin->kategori) === 'sakit');
            })->count();

            // Ambil data bulan dan tahun dari request untuk filter grafik
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));

            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            // Buat koleksi tanggal dari awal sampai akhir bulan
            $rangeTanggal = collect();
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $rangeTanggal->push([
                    'tanggal' => $date->toDateString(),
                    'label' => $date->locale('id')->isoFormat('D MMM')
                ]);
            }

            // Ambil data absensi bulan tersebut dan group by tanggal
            $absensi = Absensi::whereBetween('tanggal', [$startDate, $endDate])
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });

            $absensiBulanan = $rangeTanggal->map(function ($item) use ($absensi) {
                $tanggal = $item['tanggal']; // sudah format Y-m-d
                $jumlahHadir = isset($absensi[$tanggal]) ? $absensi[$tanggal]->count() : 0;

                return [
                    'label' => $item['label'],
                    'hadir' => $jumlahHadir,
                ];
            });


            return view('dashboard.admin', compact(
                'totalGuru',
                'jumlahHadir',
                'jumlahIzin',
                'jumlahSakit',
                'absensiBulanan',
                'bulan',
                'tahun',
                'guruSudahAbsen',
                'guruBelumAbsen'
            ));
        }

        // User biasa (bukan admin)
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

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

        return view('dashboard.user', compact(
            'absensiHariIni',
            'jadwalHariIni',
            'tahunAjaranAktif',
            'jadwal'
        ));
    }
}
