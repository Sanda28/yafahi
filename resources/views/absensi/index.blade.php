@extends('layouts.app')
@section('title', 'Data Absensi')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data Absensi</h6></div>
        <div class="card-body">
            {{-- Tombol Tambah --}}
                @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                    <a href="{{ route('absensi.create') }}" class="btn btn-primary mb-3">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Data Absen
                    </a>
                @endif
            <div class="table-responsive">
                <table class="table table-bordered" id="absensiTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Waktu Absen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#absensiTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: '{{ route("absensi.data") }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'user.name' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'waktu_absen', name: 'waktu_absen' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush


