@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Laporan Bulanan Saya</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="bulan" class="form-label">Bulan</label>
            <input type="month" name="bulan" id="bulan" class="form-control" value="{{ $bulan }}">
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensi as $absen)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $absen->tanggal }}</td>
                    <td>{{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i:s') ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($absen->jam_keluar)->format('H:i:s') ?? '-' }}</td>

                    <td>
                        @if ($absen->status === 'telat')
                            <span class="badge bg-warning text-dark">Telat</span>
                        @else
                            <span class="badge bg-success">Tepat Waktu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
