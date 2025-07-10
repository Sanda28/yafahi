<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Jadwal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat Tahun Ajaran 2024/2025
        $tahunAjaran = TahunAjaran::create([
            'nama' => '2024/2025',
            'mulai' => '2024-09-01',
            'selesai' => '2025-08-31',
            'status' => 'aktif',
        ]);


        // Superadmin
        User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('123'),
            'role' => 'superadmin',
        ]);

        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123'),
            'role' => 'admin',
        ]);

        // Data pengguna
        $users = [
            [
                'name' => 'Euis Yulita',
                'email' => 'euis.yulita1205@gmail.com',
                'jabatan' => 'Kepsek',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1968-05-12',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201165205680003',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'SITI NURMELANI',
                'email' => 'snurmelani@gmail.com',
                'jabatan' => 'Wakasek - Kurikulum',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1980-02-03',
                'tempat_lahir' => 'BOGOR',
                'nik' => '3201304312800003',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'M Nuriansah,S.E',
                'email' => 'yafahidramaga@gmail.com',
                'jabatan' => 'Wakasek - Kesiswaan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1992-06-07',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201160706920004',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Anang Sopandi, S.Pd.',
                'email' => 'anang.sopandi10@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1973-06-10',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201301006730004',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Lucky Rahman',
                'email' => 'luckyrahman715@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1990-04-03',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201302304900001',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            ],
            [
                'name' => 'Hilda Wijaya Kusumah',
                'email' => 'hildawijaya411@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1989-01-27',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201152701890004',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Sabtu'],
            ],
            [
                'name' => 'Wiga Yuliana',
                'email' => 'wigayuliana442@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1987-07-16',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201305607870011',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'FATHONAH S,Pd',
                'email' => 'utiputriericaaalmuttaqin@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1975-07-14',
                'tempat_lahir' => 'Tegal',
                'nik' => '3201305406750001',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Sri Haryati, S.Pd.I',
                'email' => 'sriharyati2785@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1985-01-27',
                'tempat_lahir' => 'Bogor',
                'nik' => '3271046701850017',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            ],
            [
                'name' => 'Asep suryana',
                'email' => 'asurtea7@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1970-08-07',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201300708700005',
                'jadwal' => ['Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Neni Susanti',
                'email' => '689nenisusanti@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1986-11-24',
                'tempat_lahir' => 'Jakarta',
                'nik' => '3201156411860001',
                'jadwal' => ['Rabu', 'Sabtu'],
            ],
            [
                'name' => 'RISWAN',
                'email' => 'wawan14@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1983-10-21',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201162110830002',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'NURBAETI',
                'email' => 'nurbaeti111078@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1978-10-11',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201155110780003',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Siti Namisah',
                'email' => 'Sha.ntrie@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1986-06-02',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201304206860002',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Sumarni',
                'email' => 'marniefaiza@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1971-05-25',
                'tempat_lahir' => 'Jakarta',
                'nik' => '3201166505710003',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis'],
            ],
            [
                'name' => 'Vivi Harrifrianti S.pd',
                'email' => 'vharrifrianti@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1976-06-09',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201304906760002',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Desi Herawati',
                'email' => 'desi723@guru.smp.belajar.id',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1998-12-17',
                'tempat_lahir' => 'Indramayu',
                'nik' => '3212024112980004',
                'jadwal' => ['Senin', 'Selasa', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Jamuri,S.Pd',
                'email' => 'jamurijamuri987@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1979-06-16',
                'tempat_lahir' => 'Bogor',
                'nik' => '3271041606790018',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Sintia Malenia, S.Pd',
                'email' => 'sintiamalenia35@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2000-03-01',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201154103000004',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Hesti Sundari, S. Pd',
                'email' => 'sundarihes85@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1985-07-24',
                'tempat_lahir' => 'Riau',
                'nik' => '3201156407850007',
                'jadwal' => ['Senin', 'Selasa', 'Rabu'],
            ],
            [
                'name' => 'Muhammad Ihsan wijayanto SE',
                'email' => 'ihsanwijayanto552@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1996-08-14',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201151408960004',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'ADRI MUHAMAD RAFI S.Pd.',
                'email' => 'Adrimuhamadrafi7@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1998-11-22',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201042211980005',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Jihan Marifatul Hidayah, S.Pd',
                'email' => 'jihanhidayah54@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2001-02-03',
                'tempat_lahir' => 'Madiun',
                'nik' => '3201164302010009',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Lena Utari',
                'email' => 'lena123utari@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2000-03-22',
                'tempat_lahir' => 'Madiun',
                'nik' => '3271046203000005',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Dita pratiwi',
                'email' => 'ditaatul14@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1989-09-21',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201306109890001',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
             [
                'name' => 'Alda Rai',
                'email' => 'aldaaaaarai@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2003-05-06',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201166605030004',
                'jadwal' => ['Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Riza Apriansah',
                'email' => 'rizaapriansyah05@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2004-04-06',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201150604040002',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            ],
            [
                'name' => 'Haikal Algaesani Kamaludin',
                'email' => 'haikalalgaesani19@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2002-01-19',
                'tempat_lahir' => 'Bogor',
                'nik' => '3201151901020001',
                'jadwal' => [ 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
            [
                'name' => 'Zaki Romdon',
                'email' => 'zakiromdon99@gmail.com',
                'jabatan' => 'Guru',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2000-11-22',
                'tempat_lahir' => 'Garut',
                'nik' => '3205302212000001',
                'jadwal' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],

        ];

        // Membuat pengguna dan mengaitkannya dengan Tahun Ajaran 2024/2025
        foreach ($users as $user) {
            $user['name'] = ucwords(strtolower($user['name']));

            // Simpan jadwal dulu, lalu buang dari array user
            $jadwal = $user['jadwal'];
            unset($user['jadwal']);

            // Buat password dari tanggal lahir (format: ddmmyyyy)
            $password = Hash::make(date('dmY', strtotime($user['tanggal_lahir'])));

            // Simpan user
            $newUser = User::create(array_merge($user, [
                'role' => 'user',
                'password' => $password,
            ]));

            // Simpan jadwal user
            Jadwal::create([
                'user_id' => $newUser->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'hari' => json_encode($jadwal),
            ]);
        }

    }
}
