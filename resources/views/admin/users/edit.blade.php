@extends('layouts.app')

@section('title', 'Edit User')
@section('content')
<div class="container">

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Kolom KIRI (5 form) -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                </div>

                <div class="mb-3">
                    <label>Password (kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $user->jabatan) }}">
                </div>
            </div>

            <!-- Kolom KANAN (4 form) -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control">
                        <option value="" disabled {{ is_null($user->jenis_kelamin) ? 'selected' : '' }}>Pilih</option>
                        <option value="L" {{ $user->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $user->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $user->tempat_lahir) }}">
                </div>

                <div class="mb-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}">
                </div>

                <div class="mb-3">
                    <label>NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ old('nik', $user->nik) }}">
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
