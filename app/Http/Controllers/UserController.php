<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->input('role');

        $query = User::with('team')
            ->when($role, fn($q) => $q->where('role', $role))
            ->orderBy('id', 'asc');

        $users = $query->paginate(10);

        return view('users.index', [
            'title' => 'Daftar Pegawai',
            'users' => $users,
            'selectedRole' => $role,
        ]);
    }

    public function create()
    {
        $teams = Team::all();
        return view('users.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => 'Kolom :attribute belum terisi.',
            'unique' => ':attribute sudah digunakan.',
            'email' => 'Format :attribute tidak valid.',
            'max' => 'Kolom :attribute maksimal :max karakter.',
            'password.min' => 'Kolom password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];

        $request->validate([
            'NIP' => 'required|unique:users,NIP',
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|max:15',
            'role' => 'required|in:pegawai,ketua_tim,admin,sumda', // dari form
            'team_id' => 'nullable|exists:teams,id',


            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:50',
                'regex:/^.*(?!.*\s)(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\!\@\#\$\%\^\&\*\(\)\-\=\¡\£\_\+\`\~\.\,\<\>\/\?\;\:\'\"\\\|\[\]\{\}]).*$/',
            ],
            'password_confirmation' => 'required',
        ], $messages);

        $data = [
            'NIP' => $request->NIP,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'team_id' => $request->team_id,
            'password' => bcrypt($request->password),
        ];

        User::create($data);

        flash()
            ->killer(true)
            ->layout('bottomRight')
            ->timeout(3000)
            ->success('<b>Berhasil!</b><br>Data pegawai berhasil ditambah.');

        return redirect()->route('users.index');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $teams = Team::all();
        return view('users.edit', compact('user', 'teams'));
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $messages = [
            'required' => 'Kolom :attribute belum terisi.',
            'unique' => ':attribute sudah digunakan.',
            'email' => 'Format :attribute tidak valid.',
            'max' => 'Kolom :attribute maksimal :max karakter.',
            'password.min' => 'Kolom password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];

        $rules = [
            'NIP' => 'required|unique:users,NIP,' . $id,
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|max:15',
            'role' => 'required|in:pegawai,ketua_tim,admin,sumda',
            'team_id' => 'nullable|exists:teams,id',
        ];

        // ✅ password opsional, tetapi jika diisi → wajib confirmed
        if ($request->filled('password')) {
            $rules['password'] = [
                'confirmed',
                'min:8',
                'max:50',
                'regex:/^.*(?!.*\s)(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\!\@\#\$\%\^\&\*\(\)\-\=\¡\£\_\+\`\~\.\,\<\>\/\?\;\:\'\"\\\|\[\]\{\}]).*$/'
            ];
            $rules['password_confirmation'] = 'required';
        }

        $request->validate($rules, $messages);

        $data = [
            'NIP' => $request->NIP,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'team_id' => $request->team_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        flash()
            ->killer(true)
            ->layout('bottomRight')
            ->timeout(3000)
            ->success('<b>Berhasil!</b><br>Data pegawai berhasil diupdate.');

        return redirect()->route('users.index'); // ✅ diperbaiki
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        flash()
        ->killer(true)
        ->layout('bottomRight')
        ->timeout(3000)
        ->success('<b>Berhasil!</b><br>Data pegawai berhasil dihapus.');

        return redirect(route('users.index'));
    }
    public function getTeams()
    {
        $teams = Team::orderBy('name')->get();
        return response()->json($teams);
    }
}
