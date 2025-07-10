<section class="py-4">
    <header>
        <h2 class="text-lg fw-semibold text-dark">
            Hapus Akun
        </h2>
        <p class="mt-1 text-sm text-muted">
            Setelah akun Anda dihapus, semua data dan informasi akan dihapus secara permanen. Sebelum menghapus akun, harap unduh data atau informasi yang ingin Anda simpan.
        </p>
    </header>

    <!-- Tombol Hapus Akun -->
    <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        Hapus Akun
    </button>

    <!-- Modal Konfirmasi Hapus Akun -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteAccountModalLabel">Konfirmasi Penghapusan Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus akun ini? Setelah dihapus, semua data akan hilang secara permanen.</p>
                        <div class="mb-3">
                            <label for="password" class="form-label">Masukkan Password untuk Konfirmasi</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
