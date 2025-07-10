@extends('layout.app')

@section('title', 'Reset Password')

@section('content')
<div class="form-container forgot-form">
    <form method="POST" action="{{ route('custom.password.reset') }}">
        @csrf
        <h1>Reset Password</h1>

        <input type="email" name="email" placeholder="Email" required>
        @error('email') <span class="text-danger">{{ $message }}</span> @enderror

        <input type="date" name="tanggal_lahir" placeholder="Tanggal Lahir" required>
        @error('tanggal_lahir') <span class="text-danger">{{ $message }}</span> @enderror

        @if (session('status'))
            <div class="text-success">{{ session('status') }}</div>
        @endif

        @if (session('new_password'))
            <div class="text-success">
                Password akun Anda telah direset menjadi: <strong>{{ session('new_password') }}</strong>
            </div>
        @endif

        <button type="submit">Reset Password</button>
        <a href="#" id="showLogin">Kembali ke Login</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const showLogin = document.getElementById('showLogin');

    showLogin?.addEventListener('click', function (e) {
        e.preventDefault();
        container.classList.remove("active");
    });
</script>
@endpush
