<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            @foreach ($tanggalList as $tgl)
                @php
                    $hari = $tgl->translatedFormat('l');
                    $isLibur = $liburs->contains(fn($libur) =>
                        \Carbon\Carbon::parse($libur->tanggal_mulai)->lte($tgl) &&
                        \Carbon\Carbon::parse($libur->tanggal_selesai ?? $libur->tanggal_mulai)->gte($tgl)
                    );
                @endphp
                <th>{{ $tgl->format('d') }}</th>
            @endforeach
            <th>H</th>
            <th>I</th>
            <th>D</th>
            <th>S</th>
            <th>X</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rekap as $index => $data)
            @php
                $hadir = 0; $izin = 0; $dinas = 0; $sakit = 0; $tanpa = 0;
                $hariAktif = 0; $jadwalGuru = $data['jadwal'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data['nama'] }}</td>
                @foreach ($tanggalList as $tgl)
                    @php
                        $tanggalStr = $tgl->toDateString();
                        $hari = $tgl->translatedFormat('l');
                        $isLibur = $liburs->contains(fn($libur) =>
                            \Carbon\Carbon::parse($libur->tanggal_mulai)->lte($tgl) &&
                            \Carbon\Carbon::parse($libur->tanggal_selesai ?? $libur->tanggal_mulai)->gte($tgl)
                        );
                        $isMinggu = $hari === 'Minggu';
                        $punyaJadwal = in_array($hari, $jadwalGuru);
                        $isHitung = !$isLibur && !$isMinggu && $punyaJadwal;

                        $value = $data['tanggal'][$tanggalStr] ?? '';
                        if ($isHitung) {
                            $hariAktif++;
                            if ($value === 'H') $hadir++;
                            elseif ($value === 'I') $izin++;
                            elseif ($value === 'D') $dinas++;
                            elseif ($value === 'S') $sakit++;
                            elseif ($value === 'X') $tanpa++;
                        } else {
                            $value = '';
                        }
                    @endphp
                    <td>{{ $value }}</td>
                @endforeach
                <td>{{ $hadir }}</td>
                <td>{{ $izin }}</td>
                <td>{{ $dinas }}</td>
                <td>{{ $sakit }}</td>
                <td>{{ $tanpa }}</td>
                <td>
                    {{ $hariAktif > 0 ? round((($hadir + $izin + $sakit + $dinas) / $hariAktif) * 100, 2) : 0 }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
