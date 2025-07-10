<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #111;
            text-align: center;
            padding: 2px 3px;
            font-size: 10px;
        }
        th {
            background-color: #f0f0f0;
        }
        .grey {
            background-color: #cccccc;
        }
        .kuning {
            background-color: #fff3b0;
        }
        .merah {
            background-color: #f8d7da;
        }
        .hitam {
            background-color: #000000;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        .left {
            text-align: left;
        }
        .libur-ket {
            font-size: 7px;
            display: block;
            color: #333;
        }
    </style>
</head>
<body>

<table style="width: 100%; border: none; margin-bottom: 20px;">
    <tr>
        <td style="width: 80px;">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="Logo" width="80">
        </td>
        <td style="text-align: center;">
            <h2 style="margin: 0;">DAFTAR HADIR GURU</h2>
            <p style="margin: 0; font-size: 12px;">YAYASAN PENDIDIKAN FAUZAN HIKAM</p>
            <p style="margin: 0; font-size: 14px; font-weight: bold;">SMP YAFAHI DRAMAGA</p>
            <p style="margin: 0; font-size: 12px;">
                Tahun Ajaran {{ \Carbon\Carbon::parse($tahunAjaran->mulai)->format('Y') }}/{{ \Carbon\Carbon::parse($tahunAjaran->selesai)->format('Y') }}
            </p>
            <p style="margin: 0; font-size: 12px; text-align: right; padding-right: 10px;">
                Bulan: {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
            </p>
        </td>

        <td style="width: 80px;">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="Logo" width="80">
        </td>
    </tr>
</table>
<hr style="border: 1px solid #000; margin-bottom: 20px;">
<table>
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama</th>
            <th colspan="{{ count($tanggalList) }}">Tanggal</th>
            <th colspan="5">Total</th>
        </tr>
        <tr>
            @foreach ($tanggalList as $tgl)
                @php
                    $hari = $tgl->translatedFormat('l');
                    $isLibur = $liburs->contains(fn($libur) =>
                        \Carbon\Carbon::parse($libur->tanggal_mulai)->lte($tgl) &&
                        \Carbon\Carbon::parse($libur->tanggal_selesai ?? $libur->tanggal_mulai)->gte($tgl)
                    );
                @endphp
                <th class="{{ $hari === 'Minggu' || $isLibur ? 'grey' : '' }}">
                    {{ $tgl->format('d') }}
                    @if ($isLibur)
                        @php
                            $libur = $liburs->first(fn($libur) =>
                                \Carbon\Carbon::parse($libur->tanggal_mulai)->lte($tgl) &&
                                \Carbon\Carbon::parse($libur->tanggal_selesai ?? $libur->tanggal_mulai)->gte($tgl)
                            );
                        @endphp

                    @endif
                </th>
            @endforeach
            <th>H</th>
            <th>I</th>
            <th>S</th>
            <th>X</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rekap as $index => $data)
            @php
                $hadir = 0;
                $izin = 0;
                $sakit = 0;
                $tanpa = 0;
                $hariAktif = 0;
                $jadwalGuru = $data['jadwal'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left">{{ $data['nama'] }}</td>
                @foreach ($tanggalList as $tgl)
                    @php
                        $tanggalStr = $tgl->toDateString();
                        $hari = $tgl->translatedFormat('l');
                        $isLibur = $liburs->contains(fn($libur) =>
                            \Carbon\Carbon::parse($libur->tanggal_mulai)->lte($tgl) &&
                            \Carbon\Carbon::parse($libur->tanggal_selesai ?? $libur->tanggal_mulai)->gte($tgl)
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

                        $tdClass = '';
                        if ($isMinggu || $isLibur) {
                            $tdClass = 'grey';
                        } elseif (!$punyaJadwal && $isHariAktif) {
                            $tdClass = 'hitam';
                        }

                        if ($value === 'I' || $value === 'S') {
                            $tdClass .= ' kuning';
                        } elseif ($value === 'X') {
                            $tdClass .= ' merah';
                        }
                    @endphp
                    <td class="{{ trim($tdClass) }}">
                        {{ $value }}
                    </td>
                @endforeach

                <td>{{ $hadir }}</td>
                <td>{{ $izin }}</td>
                <td>{{ $sakit }}</td>
                <td>{{ $tanpa }}</td>
                <td>
                    {{ $hariAktif > 0 ? round(($hadir / $hariAktif) * 100, 2) : 0 }}%
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
