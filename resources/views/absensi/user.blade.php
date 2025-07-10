@extends('layouts.app')
@section('title', 'Riwayat Absensi Bulan ')

@section('content')
<div class="container">
    <form method="GET" action="{{ route('absensi.index') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="bulan" class="form-label">Pilih Bulan:</label>
                <input type="month" name="bulan" id="bulan" class="form-control" value="{{ $bulan }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </div>
    </form>

    @if(empty($jadwalHari))
        <div class="alert alert-warning">Anda belum memiliki jadwal kerja pada bulan ini.</div>
    @else
        @php
            $dates = array_keys($dataKalender);
            $firstDate = \Carbon\Carbon::parse($dates[0]);
            $firstWeekDay = $firstDate->dayOfWeekIso;
            $padCount = $firstWeekDay - 1;

            $lastDate = \Carbon\Carbon::parse(end($dates));
            $lastWeekDay = $lastDate->dayOfWeekIso;
            $endPadCount = 7 - $lastWeekDay;
        @endphp

        <div class="calendar-grid row row-cols-7 g-2">
            {{-- Padding awal bulan --}}
            @for ($i = 0; $i < $padCount; $i++)
                <div class="col">
                    <div class="card bg-light border-0" style="min-height: 100px;"></div>
                </div>
            @endfor

            {{-- Tanggal-tanggal --}}
            @foreach ($dataKalender as $tgl => $status)
                @php
                    $carbon = \Carbon\Carbon::parse($tgl);
                    $isToday = $carbon->isToday();
                @endphp
                <div class="col">
                    <div class="card text-center @if($isToday) border-primary @endif" style="min-height: 100px;">
                        <div class="card-header p-1 bg-light">
                            <small>{{ $carbon->translatedFormat('D') }}</small><br>
                            <strong>{{ $carbon->format('d') }}</strong>
                        </div>
                        <div class="card-body p-2">
                            @if ($status === 'H')
                                <span class="badge bg-success">H</span>
                            @elseif ($status === 'I')
                                <span class="badge bg-warning text-dark">I</span>
                            @elseif ($status === 'S')
                                <span class="badge bg-warning text-dark">S</span>
                            @elseif ($status === 'X')
                                <span class="badge bg-danger">X</span>
                            @elseif ($status === '')
                                <span class="text-muted">?</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Padding akhir bulan --}}
            @for ($i = 0; $i < $endPadCount; $i++)
                <div class="col">
                    <div class="card bg-light border-0" style="min-height: 100px;"></div>
                </div>
            @endfor
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .calendar-grid .col {
        flex: 1 0 14.2857%; /* 100% / 7 columns */
        padding: 0.25rem;
    }

    .calendar-grid .card {
        min-height: 100px;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .calendar-grid .card-header {
        background-color: #f8f9fa;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.2;
    }

    .calendar-grid .card-body {
        font-size: 1rem;
    }

    /* Hari ini dengan border tebal biru */
    .calendar-grid .border-primary {
        border-width: 2px !important;
    }

    @media (max-width: 768px) {
        .calendar-grid .col {
            flex: 1 0 33.3333%;
        }
        .calendar-grid .card {
            min-height: 80px;
        }
        .calendar-grid .card-body {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .calendar-grid .col {
            flex: 1 0 50%;
        }
        .calendar-grid .card {
            min-height: 70px;
        }
        .calendar-grid .card-header {
            font-size: 0.65rem;
        }
        .calendar-grid .card-body {
            font-size: 0.85rem;
        }
    }
</style>
@endpush

