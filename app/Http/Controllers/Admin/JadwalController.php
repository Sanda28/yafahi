<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {


        $users = User::where('role', 'user')->get();

        $tahunAjarans = TahunAjaran::orderBy('mulai', 'desc')->get();
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();

        $tahunAjaranId = $request->input('tahun_ajaran_id', $tahunAktif?->id);

        $jadwals = Jadwal::with(['user', 'tahunAjaran'])
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->get();

        return view('admin.jadwal.index', compact('jadwals', 'tahunAjarans', 'users', 'tahunAjaranId'));
    }

    public function storeTahunAjaran(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'mulai' => 'required|date_format:Y-m',
            'selesai' => 'required|date_format:Y-m|after_or_equal:mulai',
        ]);

        $existing = TahunAjaran::where('mulai', $request->mulai)
            ->where('selesai', $request->selesai)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Tahun ajaran dengan periode tersebut sudah ada.');
        }

        $tahunAjaran = TahunAjaran::create([
            'nama' => $request->nama,
            'mulai' => $request->mulai,
            'selesai' => $request->selesai,
        ]);

        $users = User::where('role', 'user')->get();
        foreach ($users as $user) {
            Jadwal::create([
                'user_id' => $user->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'hari' => json_encode([]),
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'create',
            'model' => 'TahunAjaran',
            'model_id' => $tahunAjaran->id,
            'description' => 'Menambahkan tahun ajaran: ' . $tahunAjaran->nama
        ]);

        return redirect()->route('admin.jadwal.index')->with('success', 'Tahun ajaran dan jadwal untuk semua user berhasil dibuat.');
    }

    public function editTahunAjaran($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return view('admin.jadwal.edit-tahun-ajaran', compact('tahunAjaran'));
    }

    public function updateTahunAjaran(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
            'mulai' => 'required|string',
            'selesai' => 'required|string',
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update($request->only(['nama', 'mulai', 'selesai']));

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'update',
            'model' => 'TahunAjaran',
            'model_id' => $tahunAjaran->id,
            'description' => 'Memperbarui tahun ajaran: ' . $tahunAjaran->nama
        ]);

        return redirect()->route('admin.jadwal.index')->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function aktifkan($id)
    {
        TahunAjaran::query()->update(['status' => 'nonaktif']);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->status = 'aktif';
        $tahunAjaran->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'update',
            'model' => 'TahunAjaran',
            'model_id' => $tahunAjaran->id,
            'description' => 'Mengaktifkan tahun ajaran: ' . $tahunAjaran->nama
        ]);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil diaktifkan.');
    }

    public function editJadwal($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $users = User::where('role', 'user')->get();
        $tahunAjarans = TahunAjaran::all();

        return view('admin.jadwal.edit-jadwal', compact('jadwal', 'users', 'tahunAjarans'));
    }

    public function updateJadwal(Request $request, $id)
    {
        $request->validate([
            'hari' => 'required|array',
            'hari.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update([
            'hari' => json_encode($request->hari),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'update',
            'model' => 'Jadwal',
            'model_id' => $jadwal->id,
            'description' => 'Memperbarui jadwal untuk user: ' . $jadwal->user->name
        ]);

        return redirect()->route('admin.jadwal.index')->with('success', 'Hari mengajar berhasil diperbarui.');
    }

    public function destroyTahunAjaran($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->jadwals()->delete();
        $tahunAjaran->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'delete',
            'model' => 'TahunAjaran',
            'model_id' => $id,
            'description' => 'Menghapus tahun ajaran: ' . $tahunAjaran->nama
        ]);

        return redirect()->route('admin.jadwal.index')->with('success', 'Tahun ajaran dan seluruh jadwal terkait berhasil dihapus.');
    }
}
