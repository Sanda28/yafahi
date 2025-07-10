<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Libur;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LiburController extends Controller
{
    // Menampilkan daftar hari libur
    public function index()
    {
        $liburs = Libur::orderByDesc('tanggal_selesai')->get();
        return view('admin.libur.index', compact('liburs'));
    }

    // Menampilkan form tambah hari libur
    public function create()
    {
        return view('admin.libur.create');
    }

    // Menyimpan hari libur baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:255',
        ]);

        $libur = Libur::create([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
        ]);

        // Mencatat aktivitas log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'create',
            'model' => 'Libur',
            'model_id' => $libur->id,
            'description' => 'Menambahkan hari libur dari tanggal ' . $libur->tanggal_mulai . ' hingga ' . $libur->tanggal_selesai,
        ]);

        return redirect()->route('admin.libur.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit hari libur
    public function edit($id)
    {
        $libur = Libur::findOrFail($id);
        return view('admin.libur.edit', compact('libur'));
    }

    // Mengupdate data hari libur
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:255',
        ]);

        $libur = Libur::findOrFail($id);
        $libur->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
        ]);

        // Mencatat aktivitas log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'update',
            'model' => 'Libur',
            'model_id' => $libur->id,
            'description' => 'Mengubah hari libur dari tanggal ' . $libur->tanggal_mulai . ' hingga ' . $libur->tanggal_selesai,
        ]);

        return redirect()->route('admin.libur.index')->with('success', 'Hari libur berhasil diperbarui.');
    }

    // Menghapus hari libur
    public function destroy($id)
    {
        $libur = Libur::findOrFail($id);

        // Mencatat aktivitas log sebelum menghapus data
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'delete',
            'model' => 'Libur',
            'model_id' => $libur->id,
            'description' => 'Menghapus hari libur dari tanggal ' . $libur->tanggal_mulai . ' hingga ' . $libur->tanggal_selesai,
        ]);

        $libur->delete();

        return redirect()->route('admin.libur.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
