<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Jadwal;

class AbsensiSeeder extends Seeder
{
    public function run()
    {
        // === STEP 1: Tambah data LIBUR ===
        $liburRanges = [
            ['2024-09-21', '2024-09-21', 'Persiapan PTS'],
            ['2024-09-23', '2024-09-28', 'PTS'],

            ['2024-11-27', '2024-11-27', 'Libur Nasional'],
            ['2024-11-30', '2024-11-30', 'Libur Nasional'],
            ['2024-12-02', '2024-12-07', 'Libur Nasional'],
            ['2024-12-20', '2024-12-31', 'Libur Nasional'],

            ['2025-01-01', '2025-01-04', 'Tahun Baru & Cuti Bersama'],
            ['2025-01-27', '2025-01-29', 'Libur Nasional'],

        ];

        $liburDates = [];
        foreach ($liburRanges as [$mulai, $selesai, $keterangan]) {
            foreach ($this->rangeDate($mulai, $selesai) as $tanggal) {
                $liburDates[] = $tanggal;

                DB::table('liburs')->insertOrIgnore([
                    'tanggal_mulai' => $tanggal,
                    'tanggal_selesai' => $tanggal,
                    'keterangan' => $keterangan,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // === STEP 2: Tambah data IZIN ===
        $izinData = [
            [11, '2024-09-02', '2024-09-02', 'Sakit'],
            [16, '2024-09-02', '2024-09-02', 'Sakit'],
            [19, '2024-09-09', '2024-09-09', 'Izin/Cuti'],

            [5, '2024-10-23', '2024-10-24', 'Izin/Cuti'],
            [5, '2024-10-28', '2024-10-30', 'Izin/Cuti'],
            [6, '2024-10-05', '2024-10-05', 'Izin/Cuti'],
            [6, '2024-10-10', '2024-10-10', 'Izin/Cuti'],
            [6, '2024-10-19', '2024-10-19', 'Izin/Cuti'],
            [8, '2024-10-17', '2024-10-17', 'Izin/Cuti'],
            [9, '2024-10-10', '2024-10-12', 'Izin/Cuti'],
            [14, '2024-10-26', '2024-10-26', 'Izin/Cuti'],
            [15, '2024-10-15', '2024-10-15', 'Izin/Cuti'],
            [17, '2024-10-10', '2024-10-10', 'Izin/Cuti'],
            [17, '2024-10-29', '2024-10-29', 'Izin/Cuti'],
            [19, '2024-10-15', '2024-10-17', 'Izin/Cuti'],
            [21, '2024-10-25', '2024-10-25', 'Izin/Cuti'],
            [23, '2024-10-11', '2024-10-12', 'Izin/Cuti'],
            [24, '2024-10-26', '2024-10-26', 'Izin/Cuti'],
            [26, '2024-10-23', '2024-10-23', 'Izin/Cuti'],
            [28, '2024-10-04', '2024-10-04', 'Izin/Cuti'],
            [31, '2024-10-25', '2024-10-26', 'Izin/Cuti'],

            [6, '2024-11-02', '2024-11-02', 'Izin/Cuti'],
            [24, '2024-11-02', '2024-11-02', 'Izin/Cuti'],
            [4, '2024-11-04', '2024-11-04', 'Izin/Cuti'],
            [18, '2024-11-05', '2024-11-06', 'Sakit'],
            [24, '2024-11-07', '2024-11-07', 'Izin/Cuti'],
            [17, '2024-11-14', '2024-11-19', 'Izin/Cuti'],
            [24, '2024-11-14', '2024-11-16', 'Izin/Cuti'],
            [26, '2024-11-16', '2024-11-16', 'Izin/Cuti'],
            [6, '2024-11-23', '2024-11-23', 'Izin/Cuti'],
            [17, '2024-11-21', '2024-11-21', 'Izin/Cuti'],
            [31, '2024-11-21', '2024-11-21', 'Izin/Cuti'],
            [17, '2024-11-28', '2024-11-28', 'Izin/Cuti'],
            [20, '2024-11-25', '2024-11-25', 'Izin/Cuti'],
            [29, '2024-11-25', '2024-11-25', 'Izin/Cuti'],

            [5, '2025-01-30', '2025-01-31', 'Sakit'],
            [15, '2025-01-09', '2025-01-09', 'Sakit'],
            [21, '2025-01-23', '2025-01-24', 'Izin/Cuti'],
            [23, '2025-01-25', '2025-01-25', 'Izin/Cuti'],
            [25, '2025-01-23', '2025-01-24', 'Sakit'],
            [26, '2025-01-25', '2025-01-25', 'Izin/Cuti'],
            [30, '2025-01-09', '2025-01-10', 'Sakit'],
            [31, '2025-01-06', '2025-01-07', 'Izin/Cuti'],
            [31, '2025-01-09', '2025-01-09', 'Izin/Cuti'],
        ];

        foreach ($izinData as [$userId, $from, $to, $kategori]) {
            $mulai = Carbon::parse($from);
            $selesai = Carbon::parse($to);

            // Menyimpan data izin dalam tabel `izins` dengan tanggal mulai dan selesai
            DB::table('izins')->insertOrIgnore([
                'user_id' => $userId,
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $selesai->toDateString(),
                'kategori' => $kategori,
                'keterangan' => 'Izin ' . $kategori,
                'status' => 'Disetujui',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // === STEP 3: Tambah data ABSENSI ===
        $start = Carbon::parse('2024-09-01');
        $end = Carbon::parse('2025-06-17');
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            // Ambil jadwal dari tabel jadwals
            $jadwal = Jadwal::where('user_id', $user->id)
                ->where('tahun_ajaran_id', 1) // Gantilah dengan ID tahun ajaran yang sesuai
                ->first();

            if ($jadwal) {
                $jadwalHari = json_decode($jadwal->hari ?? '[]', true); // Ex: ["Senin", "Selasa"]
            } else {
                $jadwalHari = []; // Jika jadwal tidak ada, berarti tidak ada jadwal
            }

            $current = $start->copy();
            while ($current->lte($end)) {
                $tanggal = $current->toDateString();
                $hari = $current->locale('id')->isoFormat('dddd'); // contoh: "Senin"

                // Mengecek apakah tanggal tersebut adalah libur, izin, atau tidak sesuai jadwal
                $isLibur = in_array($tanggal, $liburDates);
                $isIzin = DB::table('izins')
                    ->where('user_id', $user->id)
                    ->whereDate('tanggal_mulai', '<=', $tanggal)
                    ->whereDate('tanggal_selesai', '>=', $tanggal)
                    ->exists();
                $isHariAktif = in_array($hari, $jadwalHari);

                // Jika tanggal adalah libur, ada izin, atau hari tidak sesuai jadwal, lewati
                if ($isLibur || $isIzin || !$isHariAktif) {
                    $current->addDay();
                    continue;
                }

                DB::table('absensis')->insertOrIgnore([
                    'user_id' => $user->id,
                    'tanggal' => $tanggal,
                    'waktu_absen' => now()->setTime(rand(7, 8), rand(0, 59)),
                    'latitude' => -6.2,
                    'longitude' => 106.8,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $current->addDay();
            }
        }

        // === Hapus Data Absen pada Tanggal 31 untuk User ID 5 ===
        DB::table('absensis')
            ->where('user_id', 15)
            ->where('tanggal', '2025-01-31')
            ->delete();
    }

    private function rangeDate($from, $to)
    {
        $start = Carbon::parse($from);
        $end = Carbon::parse($to);
        $dates = [];

        while ($start->lte($end)) {
            $dates[] = $start->copy()->toDateString();
            $start->addDay();
        }

        return $dates;
    }
}
