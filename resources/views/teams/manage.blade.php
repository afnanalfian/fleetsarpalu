@extends('layouts.app')

@section('title', 'Kelola Tim')

@section('content')
<div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-light text-center rounded p-4">
                    <div class="text-start mb-4">
                        <a href="{{ route('teams.index') }}" class="mb-0 text-decoration-none text-black">
                            <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                    <h3 class="mb-4 text-center">Pengaturan Tim</h3>

                    <form action="{{ route('teams.manage.save') }}" method="POST" id="saveForm">
                        @csrf

                        <div class="row g-4">

                            @foreach($teams as $team)
                            <div class="col-md-4">

                                <div class="bg-white p-3 shadow-sm rounded team-box" data-team="{{ $team->id }}">

                                    <h5 class="text-center fw-bold">{{ $team->name }}</h5>

                                    {{-- KETUA --}}
                                    <div class="mt-3">
                                        <strong>Ketua:</strong>
                                        <div class="border p-2 rounded bg-light mt-1 team-leader"
                                            data-team="{{ $team->id }}"
                                            id="leader-{{ $team->id }}">

                                            @if($team->leader)
                                                <div class="drag-item"
                                                    data-user="{{ $team->leader->id }}">
                                                    {{ $team->leader->name }}
                                                </div>
                                            @endif

                                        </div>
                                    </div>

                                    {{-- ANGGOTA --}}
                                    <div class="mt-3">
                                        <strong>Anggota:</strong>
                                        <div class="border p-2 rounded bg-light mt-1 team-members"
                                            data-team="{{ $team->id }}"
                                            id="members-{{ $team->id }}">

                                            @foreach($team->members->where('id','!=',$team->leader_id) as $member)
                                                <div class="drag-item"
                                                    data-user="{{ $member->id }}">
                                                    {{ $member->name }}
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                </div>

                            </div>
                            @endforeach

                        </div>

                        <input type="hidden" name="payload" id="payload">

                        <div class="text-center mt-4">
                            <button class="btn btn-primary px-4">Simpan Perubahan</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
</div>

{{-- SortableJS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Inisialisasi sortable
    document.querySelectorAll('.team-leader, .team-members').forEach(list => {
        new Sortable(list, {
            group: 'shared-team',
            animation: 150,
            ghostClass: 'bg-warning',
        });
    });

    // Saat tombol "Simpan" ditekan
    document.getElementById('saveForm').addEventListener('submit', function(){
        const result = {};

        document.querySelectorAll('.team-box').forEach(box => {
            const teamId = box.dataset.team;

            const leader = box.querySelector('.team-leader .drag-item');
            const members = box.querySelectorAll('.team-members .drag-item');

            result[teamId] = {
                leader: leader ? leader.dataset.user : null,
                members: Array.from(members).map(m => m.dataset.user)
            };
        });

        document.getElementById('payload').value = JSON.stringify(result);
    });

});
</script>


<style>
.drag-item {
    padding: 6px 10px;
    background: #ff9c5e;
    border-radius: 6px;
    margin-bottom: 6px;
    cursor: grab;
    font-weight: 500;
}

.drag-item:hover {
    background: #fcd228;
}

.team-leader, .team-members {
    min-height: 60px;
}
</style>

@endsection
