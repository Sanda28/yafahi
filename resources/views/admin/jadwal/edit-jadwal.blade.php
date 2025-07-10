@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
<div class="container">
    <h1>Edit Jadwal</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.jadwal.updateJadwal', $jadwal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="user_id" class="form-label">User</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">-- Pilih User --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $jadwal->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-control" required>
                <option value="">-- Pilih Tahun Ajaran --</option>
                @foreach ($tahunAjarans as $tahun)
                    <option value="{{ $tahun->id }}" {{ old('tahun_ajaran_id', $jadwal->tahun_ajaran_id) == $tahun->id ? 'selected' : '' }}>
                        {{ $tahun->nama }} ({{ $tahun->mulai }} - {{ $tahun->selesai }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Hari</label><br>
            @php
                $selectedHari = old('hari', json_decode($jadwal->hari, true) ?: []);
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            @endphp
            @foreach ($days as $day)
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="hari[]" value="{{ $day }}" id="hari_{{ $day }}" class="form-check-input"
                        {{ in_array($day, $selectedHari) ? 'checked' : '' }}>
                    <label for="hari_{{ $day }}" class="form-check-label">{{ $day }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Update Jadwal</button>
        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
