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

class UserController extends Controller
{
    public function index()
    {
        $users = auth()->user()->role === 'admin'
            ? User::withTrashed()->where('role', 'user')->simplePaginate(30)
            : User::withTrashed()->simplePaginate(30);

        return view('admin.users.index', compact('users'));
    }
    public function show(User $user)
    {
        if (auth()->user()->role === 'admin' && $user->role !== 'user') abort(403);
        return view('admin.users.show', compact('user'));
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
        if (auth()->user()->role === 'admin' && $user->role !== 'user') abort(403);

        $roles = auth()->user()->role === 'superadmin' ? ['admin', 'user'] : ['user'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->role === 'admin' && $user->role !== 'user') abort(403);

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

        $user->update(array_merge(
            $validated,
            [
                'password' => !empty($validated['password']) ? Hash::make($validated['password']) : $user->password,
            ]
        ));

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
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dinonaktifkan.');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'restore',
            'model' => 'user',
            'model_id' => $user->id,
            'description' => 'Mengaktifkan kembali user: ' . $user->name,
        ]);

        return redirect()->back()->with('success', 'User berhasil diaktifkan kembali.');
    }
}
