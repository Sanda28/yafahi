@extends('layouts.app')
@section('title', 'Edit Admin')

@section('content')
<div class="container">
    <h3>Edit Admin</h3>
    <form method="POST" action="{{ route('superadmin.admins.update', $admin->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" value="{{ $admin->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password Baru (opsional)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
