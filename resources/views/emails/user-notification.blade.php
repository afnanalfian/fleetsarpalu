<!DOCTYPE html>
<html>
<body>
    <h2>{{ $title }}</h2>

    <p>{{ $body }}</p>

    @if($link)
        <p>
            <a href="{{ $link }}" style="background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:6px;">
                Lihat Detail
            </a>
        </p>
    @endif

    <br>
    <p>Salam,<br>FleetSAR Palu</p>
</body>
</html>
