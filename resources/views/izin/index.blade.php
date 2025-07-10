@extends('layouts.app')

@section('title', 'Persetujuan Izin')
@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered" id="izintable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($izins as $izin)
            <tr>
                <td></td>
                <td>{{ $izin->user->name }}</td>
                <td>{{ $izin->tanggal_mulai->format('Y-m-d') }}</td>
                <td>{{ $izin->tanggal_selesai->format('Y-m-d') }}</td>
                <td>{{ $izin->kategori }}</td>
                <td>{{ $izin->keterangan }}</td>
                <td>{{ $izin->status }}</td>
                <td>
                    @if($izin->status == 'Menunggu')
                    <form method="POST" action="{{ route('izin.changeStatus', $izin->id) }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="Disetujui">
                        <button type="submit" class="btn btn-success btn-sm">Disetujui</button>
                    </form>
                    <form method="POST" action="{{ route('izin.changeStatus', $izin->id) }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="Ditolak">
                        <button type="submit" class="btn btn-danger btn-sm">Ditolak</button>
                    </form>
                    @elseif(in_array($izin->status, ['Disetujui', 'Ditolak']))
                    <a href="{{ route('izin.edit', $izin->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    @endif
                    @if (auth()->user()->role == 'admin')
                    <form action="{{ route('izin.destroy', $izin->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus izin ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#izintable').DataTable({
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
