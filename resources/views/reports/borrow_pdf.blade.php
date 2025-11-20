<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background: #ddd; }
    </style>
</head>
<body>

<h3 style="text-align:center;">
    Laporan Peminjaman Kendaraan Operasional <br>
    Kantor Pencarian dan Pertolongan Kelas B Palu <br>
    Periode {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
</h3>

<table>
    <thead>
        <tr>
            @foreach(array_keys($rows->first() ?? []) as $col)
                <th>{{ $col }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $r)
            <tr>
                @foreach($r as $v)
                    <td>{{ $v }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
