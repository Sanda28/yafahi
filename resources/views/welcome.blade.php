@extends('layouts.apps')

@section('title', 'Selamat Datang')

@section('content')
<div class="text-center">
    <h1 class="display-4 mb-4">Selamat Datang di Aplikasi Absensi</h1>
    <p class="lead mb-5">Aplikasi ini membantu memudahkan absensi karyawan secara otomatis menggunakan QR Code.</p>

    @guest
        <a href="{{ route('login') }}" class="btn btn-primary me-2">Login</a>
        <a href="{{ route('register') }}" class="btn btn-outline-light">Register</a>
    @else
        <a href="{{ route('dashboard') }}" class="btn btn-success">Masuk ke Dashboard</a>
    @endguest
</div>
@endsection
