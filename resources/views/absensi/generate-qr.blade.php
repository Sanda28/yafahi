@extends('layouts.app')

@section('title', 'Generate QR')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">

        <!-- QR CODE -->
        <div class="col-lg-6 text-center mb-4">
            <h3>Scan QR Code untuk Absen</h3>
            <div class="d-flex justify-content-center align-items-center mb-2">
                <div id="refresh-icon" style="transition: transform 0.5s;">🔄</div>
            </div>
            <div id="qr-container" class="my-4">{!! $qr !!}</div>
            <p class="text-muted">QR akan otomatis berganti setiap 15 detik</p>
        </div>

        <!-- TABEL ABSENSI -->
        <div class="col-lg-6" id="table-container">
            <h3>Daftar Absensi Hari Ini</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Waktu Absen</th>
                    </tr>
                </thead>
                <tbody id="absensi-tbody">
                    @forelse($absensiToday as $absen)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $absen->user->name ?? '-' }}</td>
                            <td>{{ $absen->waktu_absen ? \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i:s') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada absensi hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- Script auto-refresh --}}
<script>
    const qrContainer = document.querySelector('#qr-container');
    const refreshIcon = document.querySelector('#refresh-icon');
    const absensiTbody = document.querySelector('#absensi-tbody');

    function refreshQrAndAbsensi() {
        fetch("{{ secure_url(route('absensi.qr.refresh', [], false)) }}")
            .then(res => {
                if (!res.ok) throw new Error("Gagal fetch");
                return res.json();
            })
            .then(data => {
                qrContainer.innerHTML = data.qr;
                absensiTbody.innerHTML = data.absensi;
                qrContainer.style.opacity = '1';
                refreshIcon.style.transform = 'rotate(0deg)';
            })
            .catch(error => {
                console.error("Gagal fetch QR:", error);
                qrContainer.innerHTML = '<div class="text-danger">Gagal memuat QR</div>';
                qrContainer.style.opacity = '1';
                refreshIcon.style.transform = 'rotate(0deg)';
            });
    }

    setInterval(() => {
        refreshIcon.style.transform = 'rotate(360deg)';
        qrContainer.style.opacity = '0.3';
        refreshQrAndAbsensi();
    }, 15000);

    refreshQrAndAbsensi();
</script>
@endsection
