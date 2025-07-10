<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->paginate(15);
        return view('superadmin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('superadmin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $admin)
    {
        return view('superadmin.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin->update([
            'name' => $validated['name'],
            'password' => !empty($validated['password']) ? Hash::make($validated['password']) : $admin->password,
        ]);

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroy(User $admin)
    {
        $admin->delete();
        return redirect()->route('superadmin.admins.index')->with('success', 'Admin berhasil dihapus.');
    }
}
