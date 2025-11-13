@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Data Pegawai</h6>
                    <a href="{{ route('users.create') }}" class="text-decoration-none">
                        <button type="submit" class="btn btn-sm btn-primary">+ Tambah Pegawai</button>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table-hover table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">NIP</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Tim</th>
                            <th scope="col">Role</th>
                            <th scope="col" colspan="2">Aksi</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($users as $user)
                            <tr class="align-middle">
                                <th>{{ ($users->currentPage()-1) * $users->perPage() + $loop->iteration }}</th>
                                <td>{{ $user->NIP }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ optional($user->team)->name ?? '-' }}</td>
                                <td>{{ $user->role }}</td>

                                <td>
                                    <form action="{{ route('users.edit', ['user' => $user]) }}">
                                        <button class="btn btn-success" type="submit">Ubah</button>
                                    </form>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#Hapus{{ $user->id }}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal Hapus --}}
                            <div class="modal fade" id="Hapus{{ $user->id }}" tabindex="-1" aria-labelledby="HapusLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="HapusLabel">Hapus Data</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah anda yakin ingin menghapus data ini?<br>
                                            <b>{{ $user->NIP }} - {{ $user->name }}</b>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
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

                {!! $users->links() !!}
            </div>
        </div>
    </div>
</div>

@endsection
