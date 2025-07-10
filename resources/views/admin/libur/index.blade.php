@extends('layouts.app')

@section('title', 'Daftar Hari Libur')
@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('admin.libur.create') }}" class="btn btn-primary mb-3">Tambah Hari Libur</a>

        <table class="table table-bordered table-hover align-middle" id="liburTable">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Tanggal Mulai - Tanggal Selesai</th>
                    <th>Keterangan</th>
                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin')
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($liburs as $libur)
                    <tr>
                        <td></td>
                        <td>{{ \Carbon\Carbon::parse($libur->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($libur->tanggal_selesai)->format('d M Y') }}</td>
                        <td>{{ $libur->keterangan }}</td>
                        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin')
                            <td>
                                <a href="{{ route('admin.libur.edit', $libur->id) }}" class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.libur.destroy', $libur->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus hari libur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada hari libur yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#liburTable').DataTable({
            paging: true,
            searching: true,
            ordering: false, // jika ingin bisa urutkan kolom
            columnDefs: [{
                targets: 0, // kolom pertama (No)
                searchable: false,
                orderable: false,
            }]
        });

        // Tambahkan nomor urut sesuai filter dan halaman
        table.on('order.dt search.dt draw.dt', function () {
            table.column(0, { search: 'applied', order: 'applied', page: 'current' })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
        }).draw();
    });
</script>
@endpush
