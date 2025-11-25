<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\ExternalVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ExternalAuthController extends Controller
{
    /**
     * Show external registration form
     */
    public function showRegisterForm()
    {
        return view('auth.external.register');
    }

    /**
     * Handle external registration
     */
    public function register(Request $request)
    {
        $messages = [
            'required' => 'Kolom :attribute belum terisi.',
            'email' => 'Format :attribute tidak valid.',
            'max' => 'Kolom :attribute maksimal :max karakter.',
            'confirmed' => 'Konfirmasi password tidak sama.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
        ];

        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|max:20',
            'institution' => 'nullable|max:191',

            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:50',
                'regex:/^.*(?!.*\s)(?=.*[a-z])(?=.*[A-Z])(?=.*\d)
                (?=.*[\!\@\#\$\%\^\&\*\(\)\-\=\_\+\`\~\.\,\<\>\/\?\;\:\'\"\\\|\[\]\{\}]).*$/x'
            ],
        ], $messages);

        // Generate verification token
        $verificationToken = Str::random(64);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'institution'  => $request->institution,
            'role'         => 'External',
            'password'     => Hash::make($request->password),
            'verification_token' => $verificationToken,
            'email_verified_at' => null,
        ]);

        // Buat URL verifikasi
        $verificationUrl = route('external.verify', ['token' => $verificationToken]);

        // Kirim email verifikasi
        Mail::to($user->email)->send(new ExternalVerificationMail($user, $verificationUrl));

        flash()->addSuccess('Registrasi berhasil! Silakan cek email untuk verifikasi akun.');
        return redirect()->route('external.login');
    }

    /**
     * Verifikasi email external
     */
    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            flash()->addError('Token verifikasi tidak valid.');
            return redirect()->route('external.login');
        }

        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        return view('auth.external.verify-success');
    }

    /**
     * Show external login form
     */
    public function showLoginForm()
    {
        return view('auth.external.login');
    }

    /**
     * Handle external login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            flash()->addError('Email atau password salah.');
            return back()->withInput();
        }

        $user = Auth::user();

        // Pastikan role external
        if (strtolower($user->role) !== 'external') {
            Auth::logout();
            flash()->addError('Akun ini bukan akun External.');
            return back()->withInput();
        }

        // Pastikan email sudah diverifikasi
        if (!$user->email_verified_at) {
            Auth::logout();
            return view('auth.external.login');
        }

        // Sukses login
        $request->session()->regenerate();

        flash()->addSuccess('Login berhasil.');
        return redirect()->route('external.dashboard');
    }

    /**
     * External logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        flash()->addInfo('Anda telah logout.');
        return redirect()->route('external.login');
    }
}
