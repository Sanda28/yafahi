<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;

use App\Models\Jadwal;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        // Misal ambil tahun ajaran aktif (harus sesuaikan dengan datamu)
        $tahunAjaranAktif = TahunAjaran::where('status', 'aktif')->first();

        // Ambil jadwal user untuk tahun ajaran aktif
        $jadwal = $user->jadwals()->where('tahun_ajaran_id', $tahunAjaranAktif->id)->first();

        if (!$jadwal) {
            return back()->with('error', 'Anda belum memiliki jadwal pada tahun ajaran aktif.');
        }

        $hariKerja = $jadwal->hari; // otomatis array jika kolom 'hari' bertipe json

        return view('izin.create', compact('hariKerja'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kategori' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $tahunAjaranAktif = TahunAjaran::where('status', 'aktif')->first();
        $jadwal = $user->jadwals()->where('tahun_ajaran_id', $tahunAjaranAktif->id)->first();

        if (!$jadwal) {
            return back()->with('error', 'Anda belum memiliki jadwal pada tahun ajaran aktif.');
        }

        $hariKerja = json_decode($jadwal->hari, true); // contoh: ['Senin', 'Selasa', 'Rabu']

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);

        $hariMulai = $tanggalMulai->locale('id')->isoFormat('dddd');
        $hariSelesai = $tanggalSelesai->locale('id')->isoFormat('dddd');

        if (!in_array($hariMulai, $hariKerja)) {
            return back()->with('error', 'Tanggal mulai tidak termasuk hari jadwal Anda.');
        }

        if (!in_array($hariSelesai, $hariKerja)) {
            return back()->with('error', 'Tanggal selesai tidak termasuk hari jadwal Anda.');
        }

        Izin::create([
            'user_id' => $user->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('izin.my')->with('success', 'Izin berhasil diajukan.');
    }



    public function myIzin()
    {
        $izins = Izin::where('user_id', Auth::id())->latest()->get();
        return view('izin.my_izin', compact('izins'));
    }

    public function index()
    {
        $izins = Izin::with('user')->orderByDesc('tanggal_selesai')->get();

        return view('izin.index', compact('izins'));
    }


    public function changeStatus(Izin $izin, Request $request)
    {
        $status = $request->input('status');
        if (!in_array($status, ['Disetujui', 'Ditolak'])) {
            return back()->with('error', 'Status tidak valid.');
        }

        $izin->status = $status;
        $izin->save();

        // Log aktivitas
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'action_type' => 'izin',
            'model' => 'Izin',
            'model_id' => $izin->id,
            'description' => 'Mengubah status izin menjadi ' . $status . ' untuk tanggal ' . $izin->tanggal_mulai,
        ]);

        return back()->with('success', "Izin telah $status.");
    }

    public function edit(Izin $izin)
    {
        if ($izin->status != 'Disetujui' && $izin->status != 'Ditolak') {
            return redirect()->route('izin.index')->with('error', 'Izin belum disetujui atau ditolak.');
        }
        return view('izin.edit', compact('izin'));
    }

    public function update(Request $request, Izin $izin)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kategori' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->only(['tanggal_mulai', 'tanggal_selesai', 'kategori', 'keterangan']);
        $izin->update($data);

        // Log aktivitas
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'action_type' => 'izin',
            'model' => 'Izin',
            'model_id' => $izin->id,
            'description' => 'Mengedit data izin untuk tanggal mulai ' . $izin->tanggal_mulai,
        ]);

        // Redirect berdasarkan role
        $role = Auth::user()->role;
        if ($role === 'admin' || $role === 'superadmin') {
            return redirect()->route('izin.index')->with('success', 'Izin berhasil diperbarui.');
        }

        return redirect()->route('izin.my')->with('success', 'Izin berhasil diperbarui.');
    }
    public function destroy(Izin $izin)
    {
        // Log aktivitas (opsional)
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'action_type' => 'izin',
            'model' => 'Izin',
            'model_id' => $izin->id,
            'description' => 'Menghapus data izin tanggal mulai ' . $izin->tanggal_mulai,
        ]);

        $izin->delete();

        return back()->with('success', 'Data izin berhasil dihapus.');
    }


}
