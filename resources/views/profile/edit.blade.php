@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 fs-3 fw-semibold text-dark">Pengaturan Profil</h2>

    {{-- Notifikasi Berhasil --}}
    @if (session('status') == 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Profil berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session('status') == 'password-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Password berhasil diubah.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session('status'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                Informasi Profil
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                Ubah Password
            </button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabContent">
        {{-- Informasi Profil --}}
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        {{-- Ubah Password --}}
        <div class="tab-pane fade" id="password" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
