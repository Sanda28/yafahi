@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <h4 class="mb-4">Selamat datang, <span class="text-primary">{{ Auth::user()->name }}</span>!</h4>

    {{-- Status Absensi Hari Ini dan Jadwal Hari Ini --}}
    <div class="row g-3 mb-4">
        {{-- Status Absensi --}}
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-primary h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Status Absensi Hari Ini</h6>
                </div>
                <div class="card-body">
                    @if ($absensiHariIni)
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span><strong>Tanggal</strong></span>
                                <span>{{ \Carbon\Carbon::parse($absensiHariIni->tanggal)->translatedFormat('d M Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><strong>Waktu Absen</strong></span>
                                <span>{{ $absensiHariIni->waktu_absen ?? '-' }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="alert alert-warning mb-0 text-center">
                            Kamu belum melakukan absensi hari ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Jadwal Hari Ini --}}
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-info h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Jadwal Mengajar Hari Ini</h6>
                </div>
                <div class="card-body">
                    @if ($tahunAjaranAktif)
                        <p class="mb-2 fw-semibold small">Tahun Ajaran: <span class="text-dark">{{ $tahunAjaranAktif->nama }}</span></p>
                        <p class="mb-3 small">Tanggal: <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</strong></p>

                        @if ($jadwalHariIni)
                            <div class="alert alert-success py-2 fs-6 mb-0 text-center rounded">
                                Kamu ada jadwal mengajar hari ini: <span class="fw-bold">{{ $jadwalHariIni }}</span>
                            </div>
                        @else
                            <div class="alert alert-secondary py-2 fs-6 mb-0 text-center rounded">
                                Tidak ada jadwal mengajar hari ini.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-0 text-center">
                            Tidak ada tahun ajaran yang aktif saat ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Jadwal Lengkap --}}
    @if ($jadwal)
        @php
            $hariArray = json_decode($jadwal->hari);
        @endphp

        <div class="card shadow-sm border-success mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Jadwal Mengajar Kamu di Tahun Ajaran {{ $tahunAjaranAktif->nama }}</h6>
            </div>
            <div class="card-body">
                <p class="mb-0 small text-muted">
                    @foreach ($hariArray as $index => $hari)
                        <span class="badge bg-success me-1">{{ $hari }}</span>
                    @endforeach
                </p>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            Kamu belum memiliki jadwal mengajar untuk tahun ajaran ini.
        </div>
    @endif
</div>
@endsection
