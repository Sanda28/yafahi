@extends('layouts.app')

@section('title', 'Laporan Bulanan - Admin')

@section('content')
<div class="container mt-4">

    <!-- Form Filter -->
    <form method="GET" class="row g-3 mb-4 align-items-end" id="filterForm">
        <div class="col-md-6">
            <label for="bulan" class="form-label">Bulan</label>
            <input type="month" name="bulan" id="bulan" class="form-control" value="{{ old('bulan', $bulan) }}">
        </div>

        <div class="col-md-6 d-flex gap-2">
            @if ($isBulanSelesai)
                <a href="{{ route('laporan.rekap.pdf', ['bulan' => $bulan]) }}" class="btn btn-success w-50">
                    Cetak PDF
                </a>
                <a href="{{ route('laporan.rekap.excel', ['bulan' => $bulan]) }}" class="btn btn-primary w-50">
                    Cetak Excel
                </a>
            @else
                <div class="alert alert-info mb-0 w-100">Cetak PDF dan Excel hanya tersedia setelah bulan selesai.</div>
            @endif
        </div>

    </form>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(isset($tahunAjaran))
        <p><strong>Tahun Ajaran:</strong>
            {{ \Carbon\Carbon::parse($tahunAjaran->mulai)->format('F Y') }}
            -
            {{ \Carbon\Carbon::parse($tahunAjaran->selesai)->format('F Y') }}
        </p>
    @endif

    <!-- Tabel Laporan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Tanpa Keterangan</th>
                <th>Persentase Kehadiran (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data['nama'] }}</td>
                    <td>{{ $data['hadir'] }}</td>
                    <td>{{ $data['izin'] }}</td>
                    <td>{{ $data['sakit'] }}</td>
                    <td>{{ $data['tanpa_keterangan'] }}</td>
                    <td>{{ $data['persentase'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // Submit otomatis saat bulan diubah
    document.getElementById('bulan').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    </script>
@endsection

