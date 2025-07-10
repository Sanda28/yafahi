@extends('layouts.app')
@section('title', 'Manajemen Jadwal Guru')
@section('content')
<div class="container my-5">
    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tombol Aksi --}}
    <div class="d-flex justify-content-between mb-4">
        <div>
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTahunAjaran">
                Tambah Tahun Ajaran
            </button>
        </div>
    </div>

    {{-- Card: Tahun Ajaran --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Daftar Tahun Ajaran</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle" id="tahunajaranTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Periode</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tahunAjarans as $index => $ta)
                        <tr>
                            <td></td>
                            <td>{{ $ta->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($ta->mulai)->translatedFormat('F Y') }} - {{ \Carbon\Carbon::parse($ta->selesai)->translatedFormat('F Y') }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditTahunAjaran"
                                    data-id="{{ $ta->id }}"
                                    data-nama="{{ $ta->nama }}"
                                    data-mulai="{{ \Carbon\Carbon::parse($ta->mulai)->format('Y-m') }}"
                                    data-selesai="{{ \Carbon\Carbon::parse($ta->selesai)->format('Y-m') }}">
                                    Edit
                                </button>

                                <form action="{{ route('admin.tahunajaran.destroy', $ta->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tahun ajaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>

                                @if($ta->status !== 'aktif')
                                    <form action="{{ route('admin.tahunajaran.aktifkan', $ta->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Aktifkan</button>
                                    </form>
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Card: Jadwal Guru --}}
    <div class="card">

        <div class="card-header bg-light">
            <h5 class="mb-0">Daftar Jadwal Guru</h5>
        </div>
        <div class="card-body table-responsive">
            {{-- Filter Tahun Ajaran --}}
            <form method="GET" class="mb-3 d-flex align-items-center">
                <label for="tahunAjaranFilter" class="me-2 mb-0">Tahun Ajaran:</label>
                <select name="tahun_ajaran_id" id="tahunAjaranFilter" class="form-select w-auto me-2" onchange="this.form.submit()">
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ $ta->id == $tahunAjaranId ? 'selected' : '' }}>
                            {{ $ta->nama }} ({{ \Carbon\Carbon::parse($ta->mulai)->format('Y') }} - {{ \Carbon\Carbon::parse($ta->selesai)->format('Y') }})
                        </option>
                    @endforeach
                </select>
                <noscript><button type="submit" class="btn btn-primary">Tampilkan</button></noscript>
            </form>
            <table class="table table-bordered table-hover align-middle" id="jadwalTable">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Guru</th>
                        <th>Tahun Ajaran</th>
                        <th>Hari</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwals as $index => $jadwal)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $jadwal->user->name }}</td>
                            <td>{{ $jadwal->tahunAjaran->nama }}</td>
                            <td>{{ implode(', ', json_decode($jadwal->hari)) }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditJadwal"
                                    data-id="{{ $jadwal->id }}"
                                    data-hari="{{ implode(',', json_decode($jadwal->hari)) }}"
                                    data-namaguru="{{ $jadwal->user->name }}"
                                    data-namata="{{ $jadwal->tahunAjaran->nama }}">
                                    Edit Hari
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah Tahun Ajaran --}}
<div class="modal fade" id="modalTahunAjaran" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.tahunajaran.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Mulai</label>
                    <input type="month" name="mulai" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Selesai</label>
                    <input type="month" name="selesai" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Tahun Ajaran --}}
<div class="modal fade" id="modalEditTahunAjaran" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditTahunAjaran" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Tahun Ajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editTahunAjaranId">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" id="editNama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Mulai</label>
                    <input type="month" name="mulai" id="editMulai" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Selesai</label>
                    <input type="month" name="selesai" id="editSelesai" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Jadwal --}}
<div class="modal fade" id="modalEditJadwal" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditJadwal" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editJadwalId">
            <div class="modal-header">
                <h5 class="modal-title">Edit Hari Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Guru</label>
                    <input type="text" id="editNamaGuru" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label>Tahun Ajaran</label>
                    <input type="text" id="editNamaTahunAjaran" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label>Hari Mengajar</label><br>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="hari[]" value="{{ $hari }}" id="editHari{{ $hari }}">
                            <label class="form-check-label" for="editHari{{ $hari }}">{{ $hari }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning">Update Hari</button>
            </div>
        </form>
    </div>
</div>



@endsection

@push('scripts')
<script>
    // Edit Tahun Ajaran
    const modalEditTA = document.getElementById('modalEditTahunAjaran');
    modalEditTA.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nama = button.getAttribute('data-nama');
        const mulai = button.getAttribute('data-mulai');
        const selesai = button.getAttribute('data-selesai');

        document.getElementById('formEditTahunAjaran').action = '/admin/tahunajaran/' + id;
        document.getElementById('editTahunAjaranId').value = id;
        document.getElementById('editNama').value = nama;
        document.getElementById('editMulai').value = mulai;
        document.getElementById('editSelesai').value = selesai;
    });
</script>
<script>
    // Modal Edit Jadwal
    const modalEditJadwal = document.getElementById('modalEditJadwal');
    modalEditJadwal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const hariString = button.getAttribute('data-hari');
        const namaGuru = button.getAttribute('data-namaguru');
        const namaTA = button.getAttribute('data-namata');

        // Set nilai readonly
        modalEditJadwal.querySelector('#editNamaGuru').value = namaGuru;
        modalEditJadwal.querySelector('#editNamaTahunAjaran').value = namaTA;

        // Set ID dan action form
        const form = modalEditJadwal.querySelector('#formEditJadwal');
        modalEditJadwal.querySelector('#editJadwalId').value = id;
        form.action = `/admin/jadwal/${id}`;

        // Reset semua checkbox
        modalEditJadwal.querySelectorAll('input[name="hari[]"]').forEach(cb => cb.checked = false);

        // Centang hari yang sesuai
        if (hariString) {
            const hariArray = hariString.split(',');
            hariArray.forEach(hari => {
                const checkbox = modalEditJadwal.querySelector(`input[value="${hari}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }
    });
</script>
@endpush
@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#tahunajaranTable').DataTable({
            paging: true,
            searching: true,
            ordering: false, // jika ingin bisa urutkan kolom
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
<script>
    $(document).ready(function () {
        let table = $('#jadwalTable').DataTable({
            paging: true,
            searching: true,
            ordering: false, // jika ingin bisa urutkan kolom
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
