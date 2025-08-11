@extends('layouts.app')

@section('title', 'Detail User')
@section('content')
<div class="container">
    <h4 class="mb-3">Detail User</h4>

    <table class="table table-bordered">
        <tr><th>Nama</th><td>{{ $user->name }}</td></tr>
        <tr><th>Email</th><td>{{ $user->email }}</td></tr>
        <tr><th>Role</th><td>{{ ucfirst($user->role) }}</td></tr>
        <tr><th>Jabatan</th><td>{{ $user->jabatan ?? '-' }}</td></tr>
        <tr><th>Jenis Kelamin</th>
            <td>
                @if($user->jenis_kelamin === 'L') Laki-laki
                @elseif($user->jenis_kelamin === 'P') Perempuan
                @else -
                @endif
            </td>
        </tr>
        <tr><th>Tempat Lahir</th><td>{{ $user->tempat_lahir ?? '-' }}</td></tr>
        <tr><th>Tanggal Lahir</th><td>{{ $user->tanggal_lahir ?? '-' }}</td></tr>
        <tr><th>NIK</th><td>{{ $user->nik ?? '-' }}</td></tr>
        <tr><th>Status</th><td>{{ $user->deleted_at ? 'Nonaktif' : 'Aktif' }}</td></tr>
        <tr><th>Dibuat</th><td>{{ $user->created_at }}</td></tr>
        <tr><th>Diperbarui</th><td>{{ $user->updated_at }}</td></tr>
    </table>

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
