@extends('layouts.app')
@section('title', 'Data Absensi')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Absensi</h6>
        </div>
        <div class="card-body">
            {{-- Tombol Tambah --}}
            @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                <a href="{{ route('absensi.create') }}" class="btn btn-primary mb-3">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Data Absen
                </a>
            @endif

            {{-- Filter Nama --}}
            <form method="GET" class="mb-3 row g-2 align-items-center">
                <div class="col-auto">
                    <label for="nama" class="form-label mb-0">Filter Nama</label>
                </div>
                <div class="col-auto">
                    <select name="nama" id="nama" class="form-select">
                        <option value="">-- Semua --</option>
                        @foreach ($daftarNama as $nama)
                            <option value="{{ $nama }}" {{ request('nama') == $nama ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>

            {{-- Tabel --}}
            @if($absensis->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Waktu Absen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absensis as $index => $absen)
                                <tr>
                                    <td>{{ $absensis->firstItem() + $index }}</td>
                                    <td>{{ $absen->user->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($absen->tanggal)->format('d M Y') }}</td>
                                    <td>{{ $absen->waktu_absen ? \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i:s') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('absensi.edit', $absen->id) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form method="POST" action="{{ route('absensi.destroy', $absen->id) }}" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $absensis->withQueryString()->onEachSide(1)->links('pagination::simple-bootstrap-5') }}
                </div>
            @else
                <div class="alert alert-info">Belum ada data absensi.</div>
            @endif
        </div>
    </div>
</div>
@endsection
