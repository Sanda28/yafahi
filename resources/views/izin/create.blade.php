@extends('layouts.app')

@section('title', 'Ajukan Izin')
@section('content')
<div class="container">

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('izin.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="tanggal_mulai">Tanggal Mulai</label>
            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
            @error('tanggal_mulai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal_selesai">Tanggal Selesai</label>
            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
            @error('tanggal_selesai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="kategori">Kategori</label>
            <select class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                <option value="Izin/Cuti" {{ old('kategori') == 'Izin/Cuti' ? 'selected' : '' }}>Izin/Cuti</option>
                <option value="Sakit" {{ old('kategori') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
            </select>
            @error('kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Ajukan Izin</button>
    </form>
</div>
@endsection
