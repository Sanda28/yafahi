@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')

@section('content')
<div class="container">
    <h1>Edit Tahun Ajaran</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.jadwal.updateTahunAjaran', $tahunAjaran->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Tahun Ajaran</label>
            <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $tahunAjaran->nama) }}" required>
        </div>

        <div class="mb-3">
            <label for="mulai" class="form-label">Mulai (format: YYYY-MM)</label>
            <input type="month" name="mulai" id="mulai" class="form-control" value="{{ old('mulai', $tahunAjaran->mulai) }}" required>
        </div>

        <div class="mb-3">
            <label for="selesai" class="form-label">Selesai (format: YYYY-MM)</label>
            <input type="month" name="selesai" id="selesai" class="form-control" value="{{ old('selesai', $tahunAjaran->selesai) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Tahun Ajaran</button>
        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
