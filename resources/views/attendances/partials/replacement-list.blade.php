<table class="table table-hover">
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIP</th>
            <th>Pilih</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->NIP }}</td>
                <td>
                    <button type="button"
                            class="btn btn-success btn-sm choose-replacement"
                            data-id="{{ $user->id }}"
                            data-name="{{ $user->name }}">
                        Pilih
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted">
                    Tidak ada hasil.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
