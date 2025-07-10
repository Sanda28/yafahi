@extends('layouts.app')

@section('title', 'User')

@section('content')
<div class="container mb-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Tambah User</a>

    <table class="table table-bordered" id="userTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td></td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada user.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div ckass="mb-4">
        {{ $users->links() }}
    </div>

</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#userTable').DataTable({
            paging: true,
            searching: true,
            ordering: false,
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
