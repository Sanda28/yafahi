<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan form edit profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update informasi profil user.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'nik'             => 'nullable|string|max:50',
            'jabatan'         => 'nullable|string|max:100',
            'jenis_kelamin'   => 'nullable|in:L,P',
            'tempat_lahir'    => 'nullable|string|max:100',
            'tanggal_lahir'   => 'nullable|date',
        ]);

        $user = $request->user();

        $user->name           = $request->name;
        $user->nik            = $request->nik;
        $user->jabatan        = $request->jabatan;
        $user->jenis_kelamin  = $request->jenis_kelamin;
        $user->tempat_lahir   = $request->tempat_lahir;
        $user->tanggal_lahir  = $request->tanggal_lahir;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Hapus akun user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
