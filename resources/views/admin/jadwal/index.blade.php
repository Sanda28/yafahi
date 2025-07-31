@extends('layouts.app')
@section('title', 'Manajemen Jadwal Guru')
@section('content')
<div class="container my-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-4">
        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTahunAjaran">Tambah Tahun Ajaran</button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">Tambah Jadwal Guru</button>
    </div>

    {{-- Tahun Ajaran --}}
    <div class="card mb-4">
        <div class="card-header">Tahun Ajaran</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tahunAjarans as $ta)
                        <tr>
                            <td>{{ $ta->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($ta->mulai)->format('F Y') }} - {{ \Carbon\Carbon::parse($ta->selesai)->format('F Y') }}</td>
                            <td>
                                @if($ta->status === 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <form method="POST" action="{{ route('admin.tahunajaran.aktifkan', $ta->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Aktifkan</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.tahunajaran.destroy', $ta->id) }}" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Filter Tahun Ajaran untuk Jadwal --}}
    <form method="GET" class="mb-3">
        <label>Tahun Ajaran:</label>
        <select name="tahun_ajaran_id" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
            @foreach($tahunAjarans as $ta)
                <option value="{{ $ta->id }}" {{ $ta->id == $tahunAjaranId ? 'selected' : '' }}>{{ $ta->nama }}</option>
            @endforeach
        </select>
    </form>

    {{-- Jadwal Guru --}}
    <div class="card">
        <div class="card-header">Daftar Jadwal Guru</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Guru</th>
                        <th>Tahun Ajaran</th>
                        <th>Hari</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwals as $index => $jadwal)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $jadwal->user->name }}</td>
                            <td>{{ $jadwal->tahunAjaran->nama }}</td>
                            <td>{{ implode(', ', json_decode($jadwal->hari)) }}</td>
                            <td>
                                <a href="{{ route('admin.jadwal.edit', $jadwal->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jadwal guru ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah Tahun Ajaran --}}
<div class="modal fade" id="modalTahunAjaran" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.tahunajaran.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Mulai</label>
                    <input type="month" name="mulai" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Selesai</label>
                    <input type="month" name="selesai" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Jadwal Guru --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jadwal Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Guru</label>
                    <select name="user_id" class="form-select" required>
                        @foreach($users as $user)
                            @php
                                $sudahAda = $jadwals->contains(function ($j) use ($user, $tahunAjaranId) {
                                    return $j->user_id === $user->id && $j->tahun_ajaran_id === (int)$tahunAjaranId;
                                });
                            @endphp
                            @if(!$sudahAda)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Hari Mengajar</label>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="hari[]" value="{{ $hari }}" id="hari{{ $hari }}">
                            <label class="form-check-label" for="hari{{ $hari }}">{{ $hari }}</label>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaranId }}">
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>
@endsection
