<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function storelogin(Request $request)
    {
        $messages = [
            'required' => 'Kolom :attribute belum terisi.',
            'email' => 'Kolom :attribute harus berformat email yang valid.',
            'password.max' => 'Kolom password maksimal berisi 50 karakter.',
        ];

        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|max:50',
        ], $messages);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->role) {
                Auth::logout();
                flash()->addError('<b>Error!</b><br>Akun tidak memiliki role.');
                return redirect()->route('login');
            }

            switch ($user->role) {
                case 'Admin':
                    flash()->addSuccess('<b>Berhasil!</b><br>Proses login berhasil.');
                    return redirect()->route('admin.dashboard');

                case 'Pegawai':
                    flash()->addSuccess('<b>Berhasil!</b><br>Proses login berhasil.');
                    return redirect()->route('pegawai.dashboard');

                case 'Kepala Sumber Daya':
                    flash()->addSuccess('<b>Berhasil!</b><br>Proses login berhasil.');
                    return redirect()->route('kepalasumberdaya.dashboard');

                case 'Ketua Tim':
                    flash()->addSuccess('<b>Berhasil!</b><br>Proses login berhasil.');
                    return redirect()->route('ketuatim.dashboard');

                default:
                    Auth::logout();
                    flash()->addError('<b>Error!</b><br>Role tidak dikenali.');
                    return redirect()->route('login');
            }
        }

        flash()->addError('<b>Error!</b><br>Email atau sandi tidak sesuai.');
        return back()->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        flash()->addInfo('<b>Info</b><br>Anda telah logout.');
        return redirect()->route('login');
    }
}
