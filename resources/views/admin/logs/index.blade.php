@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="container mt-4">


    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="logtable">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Pengguna</th>
                    <th>Jenis Aksi</th>
                    <th>Model</th>
                    <th>Deskripsi</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
    @forelse($logs as $log)
        <tr>
            <td></td>
            <td>{{ $log->user->name ?? '-' }}</td>
            <td>
                <span class="badge bg-{{ $log->action_type === 'create' ? 'success' : ($log->action_type === 'update' ? 'warning' : 'danger') }}">
                    {{ ucfirst($log->action_type) }}
                </span>
            </td>
            <td>{{ $log->model }}</td>
            <td>{{ $log->description }}</td>
            <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
        </tr>
    @empty

    @endforelse
</tbody>

        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links() }} {{-- Laravel pagination --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#logtable').DataTable({
            paging: false,        // karena pakai pagination Laravel
            searching: true,      // aktifkan pencarian
            ordering: false,      // matikan semua pengurutan
            info: false,          // hilangkan info DataTables
        });

        // Tambahkan nomor urut otomatis
        table.on('draw.dt', function () {
            table.column(0, { page: 'current' })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
        }).draw();
    });
</script>
@endpush
