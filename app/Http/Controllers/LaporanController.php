<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Libur;
use Carbon\Carbon;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PDF;

class LaporanController extends Controller
{
    // Tampilkan laporan bulanan di browser
    public function laporanBulanan(Request $request)
    {
        Carbon::setLocale('id');

        $bulan = $request->query('bulan', now()->format('Y-m'));
        $tanggalBulan = Carbon::parse($bulan . '-01');

        // Cek apakah bulan sudah selesai
        $isBulanSelesai = $tanggalBulan->lt(now()->startOfMonth());

        // Cari tahun ajaran aktif pada bulan tersebut
        $tahunAjaran = TahunAjaran::whereDate('mulai', '<=', $tanggalBulan)
            ->whereDate('selesai', '>=', $tanggalBulan)
            ->first();

        if (!$tahunAjaran) {
            return back()->with('error', 'Tidak ada tahun ajaran untuk bulan tersebut.');
        }

        // Ambil jadwal semua guru untuk tahun ajaran
        $jadwalGuru = Jadwal::where('tahun_ajaran_id', $tahunAjaran->id)
            ->get()
            ->groupBy('user_id');

        $userIds = $jadwalGuru->keys();
        $users = User::whereIn('id', $userIds)->get();

        // Ambil tanggal libur dalam bentuk string (Y-m-d)
        $liburDates = Libur::all()->flatMap(function ($libur) {
            $start = Carbon::parse($libur->tanggal_mulai);
            $end = Carbon::parse($libur->tanggal_selesai ?? $libur->tanggal_mulai);

            return collect(Carbon::parse($start)->daysUntil($end->copy()->addDay()))
                ->map(fn($date) => $date->toDateString());
        })->values();

        // Ambil absensi (hanya yg memiliki waktu_absen yang valid)
        $absensiAll = Absensi::whereYear('tanggal', $tanggalBulan->year)
            ->whereMonth('tanggal', $tanggalBulan->month)
            ->whereNotNull('waktu_absen')
            ->where('waktu_absen', '!=', '00:00:00')
            ->get()
            ->groupBy('user_id');

        // Ambil izin yang disetujui
        $izinAll = Izin::whereYear('tanggal_mulai', $tanggalBulan->year)
            ->whereMonth('tanggal_mulai', $tanggalBulan->month)
            ->where('status', 'Disetujui')
            ->get()
            ->groupBy('user_id');

        // Daftar tanggal dalam bulan tersebut
        $tanggalList = collect();
        $startDate = $tanggalBulan->copy()->startOfMonth();
        $endDate = $tanggalBulan->isSameMonth(now())
            ? now()->copy()->startOfDay()
            : $tanggalBulan->copy()->endOfMonth();

        for ($d = $startDate; $d->lte($endDate); $d->addDay()) {
            $tanggalList->push($d->copy());
        }

        $rekap = [];

        foreach ($users as $user) {
            $jadwalUser = $jadwalGuru->get($user->id)
                ?->pluck('hari')
                ->flatMap(fn($hariJson) => json_decode($hariJson, true))
                ->toArray() ?? [];

            $data = [
                'nama' => $user->name,
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'tanpa_keterangan' => 0,
                'persentase' => 0,
            ];

            $totalHariAktif = 0;

            foreach ($tanggalList as $tgl) {
                $tanggal = $tgl->toDateString();
                $hari = $tgl->translatedFormat('l');

                // Skip hari Minggu, hari tanpa jadwal, atau tanggal libur
                if ($hari === 'Minggu' || !in_array($hari, $jadwalUser) || $liburDates->contains($tanggal)) {
                    continue;
                }

                $totalHariAktif++;

                // Cek absensi
                $absen = optional($absensiAll->get($user->id))
                    ?->first(fn($a) => Carbon::parse($a->tanggal)->toDateString() === $tanggal);

                // Cek izin
                $izin = optional($izinAll->get($user->id))
                    ?->first(function ($i) use ($tanggal) {
                        $mulai = Carbon::parse($i->tanggal_mulai);
                        $selesai = Carbon::parse($i->tanggal_selesai ?? $i->tanggal_mulai);
                        return $mulai->lte($tanggal) && $selesai->gte($tanggal);
                    });

                if ($absen && $absen->waktu_absen && $absen->waktu_absen !== '00:00:00') {
                    $data['hadir']++;
                } elseif ($izin) {
                    match ($izin->kategori) {
                        'Izin/Cuti' => $data['izin']++,
                        'Sakit' => $data['sakit']++,
                        default => null,
                    };
                } else {
                    if ($tgl->isPast()) {
                        $data['tanpa_keterangan']++;
                    }
                }
            }

            $totalHadirSah = $data['hadir'] + $data['izin'] + $data['sakit'];
            $data['persentase'] = $totalHariAktif > 0
                ? round(($totalHadirSah / $totalHariAktif) * 100, 2)
                : 0;

            $rekap[] = $data;
        }

        return view('laporan.laporan_admin', compact('rekap', 'bulan', 'tahunAjaran', 'isBulanSelesai'));
    }

    // Cetak PDF laporan bulanan
    public function rekapPdf(Request $request)
    {
        Carbon::setLocale('id');
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $userId = $request->input('user_id', null);

        // Ambil user dengan role user
        $users = User::where('role', 'user')
            ->when($userId, fn($q) => $q->where('id', $userId))
            ->with('jadwals') // load relasi jadwal
            ->get();

        $startDate = Carbon::parse($bulan . '-01');
        $endDate = $startDate->copy()->endOfMonth();

        // Ambil absensi dan izin
        $absensiAll = Absensi::whereBetween('tanggal', [$startDate, $endDate])->get()->groupBy('user_id');
        $izinAll = Izin::where('status', 'Disetujui')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
            })
            ->get()
            ->groupBy('user_id');

        // Ambil data libur
        $liburs = Libur::where(function ($q) use ($startDate, $endDate) {
            $q->whereDate('tanggal_mulai', '<=', $endDate)
                ->whereDate('tanggal_selesai', '>=', $startDate);
        })->get();

        // List tanggal dalam bulan
        $tanggalList = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $tanggalList->push($date->copy());
        }

        $rekap = [];

        foreach ($users as $user) {
            // Ambil daftar hari dari semua jadwal (relasi)
            $jadwalHari = [];
            foreach ($user->jadwals as $jadwal) {
                $hariArray = is_string($jadwal->hari) ? json_decode($jadwal->hari, true) : [];
                if (is_array($hariArray)) {
                    $jadwalHari = array_merge($jadwalHari, $hariArray);
                }
            }
            $jadwalHari = array_unique($jadwalHari);

            $data = [
                'nama' => $user->name,
                'jadwal' => $jadwalHari,
                'tanggal' => [],
            ];

            foreach ($tanggalList as $tgl) {
                $tanggal = $tgl->toDateString();
                $hari = $tgl->translatedFormat('l');

                // Cek hari libur
                $isLibur = $liburs->contains(function ($libur) use ($tgl) {
                    $mulai = Carbon::parse($libur->tanggal_mulai);
                    $selesai = $libur->tanggal_selesai ? Carbon::parse($libur->tanggal_selesai) : $mulai;
                    return $mulai->lte($tgl) && $selesai->gte($tgl);
                });

                // Jika Minggu, libur, atau tidak punya jadwal
                if ($hari === 'Minggu' || !in_array($hari, $jadwalHari) || $isLibur) {
                    $data['tanggal'][$tanggal] = '';
                    continue;
                }

                // Cek izin
                $izin = optional($izinAll->get($user->id))->first(function ($i) use ($tgl) {
                    $mulai = Carbon::parse($i->tanggal_mulai);
                    $selesai = Carbon::parse($i->tanggal_selesai ?? $i->tanggal_mulai);
                    return $mulai->lte($tgl) && $selesai->gte($tgl);
                });

                // Cek absen
                $absen = optional($absensiAll->get($user->id))->first(fn($a) => Carbon::parse($a->tanggal)->toDateString() === $tanggal);

                if ($absen) {
                    $data['tanggal'][$tanggal] = 'H';
                } elseif ($izin) {
                    $data['tanggal'][$tanggal] = match ($izin->kategori) {
                        'Sakit' => 'S',
                        'Izin/Cuti' => 'I',
                        default => 'X',
                    };
                } else {
                    $data['tanggal'][$tanggal] = 'X';
                }
            }

            $rekap[] = $data;
        }

        // Penentuan nama file PDF
        $tanggalBulan = Carbon::parse($bulan . '-01');
        $tahunAjaran = TahunAjaran::whereDate('mulai', '<=', $tanggalBulan)
            ->whereDate('selesai', '>=', $tanggalBulan)
            ->first();

        $tahunAjaranSlug = str_replace([' ', '/', '\\'], ['_', '-', '-'], strtolower($tahunAjaran?->nama ?? 'tanpa_tahun_ajaran'));
        $bulanSlug = strtolower($tanggalBulan->translatedFormat('F_Y'));

        $namaFile = 'rekap_' . $bulanSlug . '.pdf';

        // Generate PDF
        $pdf = PDF::loadView('laporan.rekap_pdf', compact('rekap', 'bulan', 'users', 'tanggalList', 'liburs', 'tahunAjaran'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($namaFile);
    }


    // Export Excel laporan bulanan
    public function rekapExcel(Request $request)
{
    $bulan = $request->input('bulan');
    $tahunAjaran = TahunAjaran::where('status', 'aktif')->first();

    // Ambil tanggal di bulan yg dipilih
    $tanggalList = collect();
    $startDate = Carbon::parse($bulan . '-01');
    $endDate = $startDate->copy()->endOfMonth();

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $tanggalList->push($date->copy());
    }

    $liburs = Libur::all();
    $users = User::where('role', 'user')->with('jadwals')->get(); // pastikan eager load jadwals

    $rekap = [];
    foreach ($users as $user) {
        // Ambil jadwal dari relasi
        $jadwalHari = [];
        foreach ($user->jadwals as $jadwal) {
            $hariArray = is_string($jadwal->hari) ? json_decode($jadwal->hari, true) : [];
            if (is_array($hariArray)) {
                $jadwalHari = array_merge($jadwalHari, $hariArray);
            }
        }
        $jadwalHari = array_unique($jadwalHari);

        $tanggalData = [];

        foreach ($tanggalList as $tgl) {
            $tanggalStr = $tgl->toDateString();
            $hari = $tgl->translatedFormat('l');

            $isLibur = $liburs->contains(fn($libur) =>
                Carbon::parse($libur->tanggal_mulai)->lte($tgl) &&
                Carbon::parse($libur->tanggal_selesai ?? $libur->tanggal_mulai)->gte($tgl)
            );
            $isMinggu = $hari === 'Minggu';
            $punyaJadwal = in_array($hari, $jadwalHari);

            $absen = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $tanggalStr)
                ->first();

            $izin = Izin::where('user_id', $user->id)
                ->where('status', 'Disetujui')
                ->where(function ($q) use ($tgl) {
                    $q->whereBetween('tanggal_mulai', [$tgl, $tgl])
                      ->orWhereBetween('tanggal_selesai', [$tgl, $tgl]);
                })
                ->first();

            if ($isMinggu || !$punyaJadwal || $isLibur) {
                $value = '';
            } elseif ($absen) {
                $value = 'H';
            } elseif ($izin) {
                $value = match ($izin->kategori) {
                    'Sakit' => 'S',
                    'Izin/Cuti' => 'I',
                    default => 'X',
                };
            } else {
                $value = 'X';
            }

            $tanggalData[$tanggalStr] = $value;
        }

        $rekap[] = [
            'nama' => $user->name,
            'jadwal' => $jadwalHari,
            'tanggal' => $tanggalData,
        ];
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Nama');

    $colIndex = 3;
    foreach ($tanggalList as $tgl) {
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . '1', $tgl->format('d'));
        $colIndex++;
    }

    $totalLabels = ['H', 'I', 'S', 'X', '%'];
    foreach ($totalLabels as $label) {
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . '1', $label);
        $colIndex++;
    }

    $row = 2;
    foreach ($rekap as $index => $data) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $data['nama']);

        $colIndex = 3;
        $hadir = 0;
        $izin = 0;
        $sakit = 0;
        $tanpa = 0;
        $hariAktif = 0;
        $jadwalGuru = $data['jadwal'];

        foreach ($tanggalList as $tgl) {
            $tanggalStr = $tgl->toDateString();
            $hari = $tgl->translatedFormat('l');

            $isLibur = $liburs->contains(fn($libur) =>
                Carbon::parse($libur->tanggal_mulai)->lte($tgl) &&
                Carbon::parse($libur->tanggal_selesai ?? $libur->tanggal_mulai)->gte($tgl)
            );
            $isMinggu = $hari === 'Minggu';
            $isHariAktif = !$isLibur && !$isMinggu;
            $punyaJadwal = in_array($hari, $jadwalGuru);
            $isHitung = $isHariAktif && $punyaJadwal;

            $value = $data['tanggal'][$tanggalStr] ?? '';

            if ($isHitung) {
                $hariAktif++;
                if ($value === 'H') $hadir++;
                elseif ($value === 'I') $izin++;
                elseif ($value === 'S') $sakit++;
                elseif ($value === 'X') $tanpa++;
            } else {
                $value = '';
            }

            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $value);

            // Style warna
            $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($colIndex) . $row);
            $style = $cell->getStyle();

            if ($isMinggu || $isLibur) {
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $style->getFill()->getStartColor()->setRGB('CCCCCC');
            } elseif (!$punyaJadwal && $isHariAktif) {
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $style->getFill()->getStartColor()->setRGB('000000');
                $style->getFont()->getColor()->setRGB('FFFFFF');
            }

            if ($value === 'I' || $value === 'S') {
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $style->getFill()->getStartColor()->setRGB('FFF3B0');
            } elseif ($value === 'X') {
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $style->getFill()->getStartColor()->setRGB('F8D7DA');
            }

            $colIndex++;
        }

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex++) . $row, $hadir);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex++) . $row, $izin);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex++) . $row, $sakit);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex++) . $row, $tanpa);

        $persentase = $hariAktif > 0 ? round(($hadir / $hariAktif) * 100, 2) : 0;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex++) . $row, $persentase . '%');

        $row++;
    }

    $fileName = 'Rekap_Absensi_Bulan_' . Carbon::parse($bulan)->format('Ym') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$fileName}\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}




}
