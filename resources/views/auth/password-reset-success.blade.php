@extends('layouts.app')

@section('title', 'Reset Password Berhasil')

@section('content')
<div class="container mt-5 text-center">
    <h4 class="mb-3">Reset Password Berhasil</h4>
    <p>Password akun Anda telah diatur ulang ke: <strong>password123</strong></p>
    <p>Silakan login kembali menggunakan password tersebut dan segera ubah password Anda untuk keamanan akun.</p>
    <a href="{{ route('login') }}" class="btn btn-primary mt-3">Kembali ke Login</a>
</div>
@endsection
