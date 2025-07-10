@extends('layouts.app')

@section('title', 'Tambah Absen')

@section('content')
<div class="container">

    {{-- Notifikasi Error & Success --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-6">
        <form action="{{ route('absensi.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>User</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Pilih User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mt-2">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="form-group mt-2">
                <label>Waktu Absen (opsional)</label>
                <input type="time" name="waktu_absen" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
        </div>
    </div>
</div>
@endsection
