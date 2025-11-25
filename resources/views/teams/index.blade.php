@extends('layouts.app')

@section('title', 'Data Tim')

@section('content')

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Data Tim</h6>
                    @if (in_array(strtolower(auth()->user()->role), ['admin','kepala sumber daya']))
                        <div class="d-flex justify-content-end gap-2 mb-3">

                            {{-- Tombol Atur Team --}}
                            <a href="{{ route('teams.manage') }}" class="btn btn-warning rounded">
                                Manage Team <i class="fa-solid ms-1"></i>
                            </a>

                            {{-- Tombol Tambah Team --}}
                            <a href="{{ route('teams.create') }}" class="btn btn-primary rounded">
                                + Tambah Team <i class="fa-solid ms-1"></i>
                            </a>

                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table-hover table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Tim</th>
                            <th scope="col">Ketua Tim</th>
                            <th scope="col" colspan="2">Aksi</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($teams as $team)
                            <tr class="align-middle">
                                <th>{{ ($teams->currentPage()-1) * $teams->perPage() + $loop->iteration }}</th>
                                <td>{{ $team->name }}</td>
                                <td>{{ $team->leader_name }}</td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teams.show', $team->id) }}" class="btn btn-info btn-sm">Detail</a>

                                        @if (in_array(strtolower(auth()->user()->role), ['admin', 'kepala sumber daya']))
                                            <a href="{{ route('teams.edit', $team->id) }}" class="btn btn-success btn-sm">Ubah</a>

                                            <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#Hapus{{ $team->id }}">
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal Hapus --}}
                            <div class="modal fade" id="Hapus{{ $team->id }}" tabindex="-1" aria-labelledby="HapusLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="HapusLabel">Hapus Data</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah anda yakin ingin menghapus Tim ini?<br>
                                            <b>{{ $team->name }} - {{ $team->leader_name }}</b>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('teams.destroy', $team->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <h2 class="text-center py-5">Data Kosong</h2>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {!! $teams->links() !!}
            </div>
        </div>
    </div>
</div>

@endsection
