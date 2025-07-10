@extends('layouts.app')

@section('title', 'Edit Izin')
@section('content')
<div class="container">
    <h2>Edit Izin</h2>

    <form action="{{ route('izin.update', $izin->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="tanggal_mulai">Tanggal Mulai</label>
            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $izin->tanggal_mulai->format('Y-m-d')) }}" required>
            @error('tanggal_mulai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal_selesai">Tanggal Selesai</label>
            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $izin->tanggal_selesai->format('Y-m-d')) }}" required>
            @error('tanggal_selesai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="kategori">Kategori</label>
            <select class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                <option value="Izin/Cuti" {{ old('kategori', $izin->kategori) == 'Izin/Cuti' ? 'selected' : '' }}>Izin/Cuti</option>
                <option value="Sakit" {{ old('kategori', $izin->kategori) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
            </select>
            @error('kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $izin->keterangan) }}</textarea>
            @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-warning">Perbarui Izin</button>
    </form>
</div>
@endsection
