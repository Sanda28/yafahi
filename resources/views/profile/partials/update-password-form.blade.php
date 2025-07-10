<section class="py-4">
    <header>
        <h2 class="h5 fw-semibold text-dark">
            Perbarui Kata Sandi
        </h2>
        <p class="text-muted">
            Pastikan akun Anda menggunakan kata sandi panjang dan acak untuk menjaga keamanan.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
            @error('current_password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi Baru</label>
            <input type="password" class="form-control" id="password" name="password" required>
            @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            @error('password_confirmation')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>

        @if (session('status') === 'password-updated')
            <p class="text-success mt-2">Kata sandi berhasil diperbarui.</p>
        @endif
    </form>
</section>

