<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index()
    {
        $users = auth()->user()->role === 'admin'
            ? User::where('role', 'user')->simplePaginate(30)
            : User::simplePaginate(30);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = auth()->user()->role === 'superadmin' ? ['admin', 'user'] : ['user'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $roles = auth()->user()->role === 'superadmin' ? 'admin,user' : 'user';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:' . $roles,
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'create',
            'model' => 'user',
            'model_id' => $user->id,
            'description' => 'Menambah user baru: ' . $user->name,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        if (auth()->user()->role === 'admin' && $user->role !== 'user') {
            abort(403, 'Akses ditolak.');
        }

        $roles = auth()->user()->role === 'superadmin' ? ['admin', 'user'] : ['user'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->role === 'admin' && $user->role !== 'user') {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,user',
            'jabatan' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:50',
        ]);

        $user->update([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'jabatan' => $validated['jabatan'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'password' => !empty($validated['password']) ? Hash::make($validated['password']) : $user->password,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'update',
            'model' => 'user',
            'model_id' => $user->id,
            'description' => 'Mengubah data user: ' . $user->name,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cek apakah punya absensi atau jadwal tahun sebelumnya
        $punyaJadwalLama = $user->jadwals()->where('tahun_ajaran_id', '!=', TahunAjaran::aktif()->id)->exists();
        $punyaAbsensiLama = $user->absensis()->whereHas('tahunAjaran', function($q) {
            $q->where('status', 'nonaktif');
        })->exists();

        if ($punyaJadwalLama || $punyaAbsensiLama) {
            return redirect()->back()->with('error', 'User tidak bisa dihapus karena masih memiliki data pada tahun ajaran sebelumnya.');
        }

        // Jika tidak punya data lama, boleh dihapus
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }


}
