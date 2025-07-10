@extends('layouts.app')
@section('title', 'Kelola Admin')

@section('content')
<div class="container">
    <h3>Daftar Admin</h3>
    <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary mb-3">Tambah Admin</a>

    <table class="table table-bordered">
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
            @foreach($admins as $admin)
                <tr>
                    <td></td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>

                    <td>{{ $admin->role }}</td>
                    <td>
                        <a href="{{ route('superadmin.admins.edit', $admin->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('superadmin.admins.destroy', $admin->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus admin ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $admins->links() }}
</div>
@endsection
