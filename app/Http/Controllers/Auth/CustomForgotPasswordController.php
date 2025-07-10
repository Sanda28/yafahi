<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CustomForgotPasswordController extends Controller
{
    /**
     * Menampilkan form lupa password
     */
    public function showForm()
    {
        return view('auth.custom-forgot-password');
    }

    /**
     * Menghandle reset password berdasarkan email dan tanggal lahir
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'tanggal_lahir' => 'required|date',
        ]);

        // Cari user berdasarkan email & tanggal lahir
        $user = User::where('email', $request->email)
                    ->where('tanggal_lahir', $request->tanggal_lahir)
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email atau tanggal lahir tidak cocok.',
            ]);
        }

        // Format password baru menjadi ddmmyyyy dari tanggal lahir
        $newPasswordPlain = Carbon::parse($user->tanggal_lahir)->format('dmY');

        // Simpan password baru
        $user->password = Hash::make($newPasswordPlain);
        $user->save();

        return back()->with([
            'status' => 'Password berhasil direset.',
            'new_password' => $newPasswordPlain
        ]);
    }
}
