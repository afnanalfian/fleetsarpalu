<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'phone' => 'nullable|max:15',

            // password baru opsional
            'old_password' => 'nullable',
            'password' => 'nullable|min:8|max:50|confirmed',
        ]);

        // Jika user ingin mengubah password
        if ($request->filled('password')) {

            // WAJIB isi password lama
            if (!$request->filled('old_password')) {
                return back()->withErrors(['old_password' => 'Password lama wajib diisi untuk mengganti password.']);
            }

            // Password lama harus benar
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'Password lama tidak sesuai.']);
            }

            // Set password baru
            $user->password = Hash::make($request->password);
        }

        // Update phone
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
