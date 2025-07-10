@extends('layouts.app')
@section('title', 'Edit Absensi')

@section('content')
<div class="container mt-4">
    <h4>Edit Absensi</h4>
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('absensi.update', $absensi->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="waktu_absen" class="form-label">Waktu Absen</label>
                    <input type="time" name="waktu_absen" id="waktu_absen" class="form-control"
                           value="{{ old('waktu_absen', \Carbon\Carbon::parse($absensi->waktu_absen)->format('H:i')) }}">
                </div>

                <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Kembali</a>
                <button class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
