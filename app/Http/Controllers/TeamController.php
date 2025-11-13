<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $teams = Team::with('leader')
            ->when($keyword, fn($q) =>
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhereHas('leader', fn($l) =>
                      $l->where('name', 'like', "%{$keyword}%")
                  )
            )
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->appends(['keyword' => $keyword]);

        return view('teams.index', [
            'title' => 'Daftar Tim',
            'teams' => $teams,
            'keyword' => $keyword,
        ]);
    }

    public function create()
    {
        // User yang bisa jadi ketua = Pegawai / Ketua Tim dan belum punya tim
        $leaders = User::whereIn('role', ['Pegawai', 'Ketua Tim'])
                       ->whereNull('team_id')
                       ->get();

        return view('teams.create', compact('leaders'));
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => 'Kolom :attribute wajib diisi.',
            'unique' => ':attribute sudah digunakan.',
            'exists' => ':attribute tidak ditemukan.',
        ];

        $request->validate([
            'name' => 'required|unique:teams,name',
            'leader_id' => 'required|exists:users,id',
        ], $messages);

        // Buat tim
        $team = Team::create([
            'name' => $request->name,
            'leader_id' => $request->leader_id,
        ]);

        // Update role ketua → Ketua Tim
        $leader = User::find($request->leader_id);
        $leader->update([
            'team_id' => $team->id,
            'role' => 'Ketua Tim',
        ]);

        flash()
            ->killer(true)
            ->layout('bottomRight')
            ->timeout(3000)
            ->success('<b>Berhasil!</b><br>Tim baru berhasil ditambahkan.');

        return redirect()->route('teams.index');
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);

        $leaders = User::whereIn('role', ['Pegawai', 'Ketua Tim'])
            ->where(function ($q) use ($team) {
                $q->whereNull('team_id')          // user belum punya tim
                ->orWhere('team_id', $team->id) // anggota tim
                ->orWhere('id', $team->leader_id); // ketua lama
            })
            ->get();

        return view('teams.edit', compact('team', 'leaders'));
    }


    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $messages = [
            'required' => 'Kolom :attribute wajib diisi.',
            'unique' => ':attribute sudah digunakan.',
            'exists' => ':attribute tidak ditemukan.',
        ];

        $request->validate([
            'name' => 'required|unique:teams,name,' . $id,
            'leader_id' => 'required|exists:users,id',
        ], $messages);

        $oldLeaderId = $team->leader_id;
        $newLeaderId = $request->leader_id;

        // === Update nama tim dan ketua ===
        $team->update([
            'name' => $request->name,
            'leader_id' => $newLeaderId,
        ]);

        // === Jika ketua berganti ===
        if ($oldLeaderId != $newLeaderId) {

            // 1. Ketua lama menjadi Pegawai, tetapi tetap anggota tim
            if ($oldLeaderId) {
                User::where('id', $oldLeaderId)->update([
                    'role' => 'Pegawai',
                    'team_id' => $team->id,   // tetap di tim
                ]);
            }

            // 2. Ketua baru menjadi Ketua Tim dan masuk tim (jika belum)
            User::where('id', $newLeaderId)->update([
                'role' => 'Ketua Tim',
                'team_id' => $team->id,
            ]);
        }

        flash()
            ->killer(true)
            ->layout('bottomRight')
            ->timeout(3000)
            ->success('<b>Berhasil!</b><br>Data tim berhasil diperbarui.');

        return redirect()->route('teams.index');
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);

        // Ketua tim jadi pegawai
        User::where('id', $team->leader_id)->update([
            'role' => 'Pegawai',
            'team_id' => null,
        ]);

        // Semua anggota tim → team_id null
        User::where('team_id', $team->id)->update([
            'team_id' => null,
        ]);

        // Hapus tim
        $team->delete();

        flash()
            ->killer(true)
            ->layout('bottomRight')
            ->timeout(3000)
            ->success('<b>Berhasil!</b><br>Data tim berhasil dihapus.');

        return redirect()->route('teams.index');
    }

    public function show($id)
    {
        $team = Team::with(['leader', 'members'])->findOrFail($id);
        return view('teams.show', compact('team'));
    }
}
