@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">

    {{-- Statistik Ringkas --}}
    <div class="row">
        {{-- Total Guru --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Guru Aktif</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGuru }}</div>
                </div>
            </div>
        </div>

        {{-- Hadir Hari Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir Hari Ini</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahHadir }}</div>
                </div>
            </div>
        </div>

        {{-- Izin Hari Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Izin Hari Ini</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahIzin }}</div>
                </div>
            </div>
        </div>

        {{-- Sakit Hari Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Sakit Hari Ini</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahSakit }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bulan & Tahun --}}
    <div class="bg-white rounded shadow p-4">
    <div class="flex flex-wrap justify-between items-center mb-3">
        <h3 class="text-lg font-semibold">Grafik Kehadiran Bulanan</h3>

        <form method="GET" class="flex flex-wrap gap-2 items-center" aria-label="Filter bulan dan tahun">
            @php
                $bulanSekarang = $bulan ?? date('m');
                $tahunSekarang = $tahun ?? date('Y');
            @endphp

            <label>
                <select name="bulan" onchange="this.form.submit()" class="border rounded p-2">
                    @foreach(range(1,12) as $bln)
                        <option value="{{ str_pad($bln, 2, '0', STR_PAD_LEFT) }}" {{ $bulanSekarang == str_pad($bln, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $bln, 1)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                <select name="tahun" onchange="this.form.submit()" class="border rounded p-2">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $tahunSekarang == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </label>
        </form>
    </div>

    <canvas id="absensiChart" height="120" class="w-full"></canvas>
</div>


    {{-- Tabel Guru Sudah Absen --}}
    <div class="row">
        <div class="col-xl-5 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Guru Sudah Absen Hari Ini</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Waktu Absen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guruSudahAbsen as $guru)
                            <tr>
                                <td>{{ $guru->name }}</td>
                                <td>{{ $guru->absensis->first()?->waktu_absen?->format('H:i:s') ?? '-' }}</td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">Tidak ada guru yang sudah absen hari ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-7 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">Guru Belum Absen Hari Ini</h6>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Izin</th>
                                <th>Kategori Izin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guruBelumAbsen as $guru)
                            <tr>
                                <td>{{ $guru->name }}</td>
                                <td>{{ $guru->izins->isNotEmpty() ? 'Ya' : 'Tidak' }}</td>
                                <td>{{ $guru->izins->isNotEmpty() ? $guru->izins->first()->kategori : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Semua guru sudah absen hari ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('absensiChart').getContext('2d');

    const data = {
        labels: {!! json_encode(collect($absensiBulanan)->pluck('label')) !!},
        datasets: [{
            label: 'Jumlah Hadir',
            data: {!! json_encode(collect($absensiBulanan)->pluck('hadir')) !!},
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            scales: {
                y: { beginAtZero: true, stepSize: 1 }
            },
            plugins: {
                legend: { display: true }
            }
        }
    };

    new Chart(ctx, config);
</script>
@endpush
