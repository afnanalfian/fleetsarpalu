<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>Verifikasi Berhasil | FleetSAR Palu</title>
</head>
<body>

    <div class="container-fluid">
        <div class="row">

            {{-- Left Image --}}
            <div class="col-sm-7 width-xxl px-0 d-none d-sm-block">
                <img src="{{ asset('img/bglog.jpg') }}"
                     alt="Verification image"
                     class="w-100 vh-100"
                     style="object-fit: cover; object-position: left;">
            </div>

            {{-- Right Section --}}
            <div class="col-sm-5 bg-log d-flex justify-content-center align-items-center" style="height: 100vh">

                <div class="card shadow-lg bg-white p-4 text-center" style="width: 460px; border-radius: 1rem;">

                    <img src="{{ asset('img/logo.png') }}"
                         class="img-fluid mb-3"
                         style="width: 120px">

                    <h2 class="fw-bold text-success mb-3">
                        <i class="fa-solid fa-circle-check"></i> Verifikasi Berhasil
                    </h2>

                    <p class="text-muted mb-4" style="font-size: 15px">
                        Selamat! Email Anda telah berhasil diverifikasi.<br>
                        Silakan klik tombol di bawah untuk masuk ke akun External Anda.
                    </p>

                    <a href="{{ route('external.login') }}"
                       class="btn btn-success fw-semibold w-100"
                       style="padding: 10px; font-size: 16px;">
                        Masuk Sekarang
                    </a>

                    <div class="mt-3">
                        <a href="{{ route('login') }}" class="btn btn-link">Masuk sebagai Pegawai Internal</a>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script src="https://kit.fontawesome.com/e814145206.js" crossorigin="anonymous"></script>

</body>
</html>
