@extends('layouts.app')

@section('title', 'Scan QR Absensi')

@section('content')
<div class="container mt-4 text-center">
    <h3 class="mb-3">Scan QR Code Absensi</h3>

    {{-- Flash messages --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if($isBlocked)
        <div class="alert alert-warning">{{ $reason }}</div>
    @else
        <div id="preview" style="width: 100%; max-width: 400px; margin: auto; border: 2px solid #000; border-radius: 10px;"></div>
        <p id="message" class="mt-3 text-muted">Menunggu kamera...</p>
    @endif
</div>
@endsection

@push('scripts')
@if(!$isBlocked)
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const previewElementId = "preview";
    const message = document.getElementById('message');
    if (!message) return;

    const previewElement = document.getElementById(previewElementId);
    if (!previewElement) return;

    const scanner = new Html5Qrcode(previewElementId);

    let userLatitude = null;
    let userLongitude = null;

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                userLatitude = position.coords.latitude;
                userLongitude = position.coords.longitude;
                message.innerText = "Lokasi GPS berhasil diperoleh. Siap memindai QR code.";
            }, function(error) {
                message.innerText = "Gagal mengambil lokasi. Aktifkan GPS.";
            });
        } else {
            message.innerText = "Browser tidak mendukung GPS.";
        }
    }

    getLocation();

    function onScanSuccess(decodedText) {
        if (!userLatitude || !userLongitude) {
            message.innerText = "Lokasi belum tersedia. Pastikan GPS aktif.";
            return;
        }

        message.innerText = "QR terdeteksi. Memproses...";

        scanner.stop().then(() => {
            try {
                const url = new URL(decodedText);
                url.searchParams.append('latitude', userLatitude);
                url.searchParams.append('longitude', userLongitude);
                window.location.href = url.toString();
            } catch (e) {
                message.innerText = "QR code tidak valid.";
            }
        }).catch(err => {
            message.innerText = "Gagal berhenti scanner: " + err;
        });
    }

    Html5Qrcode.getCameras().then(devices => {
        if (devices.length > 0) {
            let backCamera = devices.find(device =>
                device.label.toLowerCase().includes('back') ||
                device.label.toLowerCase().includes('environment')
            );
            const cameraId = backCamera ? backCamera.id : devices[0].id;

            scanner.start(
                cameraId,
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                errorMessage => {}
            ).catch(err => {
                message.innerText = "Gagal memulai kamera: " + err;
            });
        } else {
            message.innerText = "Tidak ada kamera ditemukan.";
        }
    }).catch(err => {
        message.innerText = "Akses kamera gagal: " + err;
    });
});
</script>
@endif
@endpush
