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
                <th>Status</th>
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
                    <td>{{ $user->deleted_at ? 'Nonaktif' : 'Aktif' }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>

                        @if ($user->deleted_at)
                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button class="btn btn-sm btn-success">Aktifkan Kembali</button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menonaktifkan user ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Nonaktifkan</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada user.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mb-4">
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
        info: true,
        columnDefs: [{
            targets: 0,
            searchable: false,
            orderable: false,
        }],
        drawCallback: function (settings) {
            let api = this.api();
            api.column(0, { search: 'applied', order: 'applied', page: 'current' })
               .nodes()
               .each(function (cell, i) {
                   cell.innerHTML = i + 1 + settings._iDisplayStart;
               });
        }
    });
});

</script>
@endpush
