<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <title>{{ config('app.name') }} | Login External</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">

            {{-- Left Image --}}
            <div class="col-sm-7 width-xxl px-0 d-none d-sm-block">
                <img src="{{ asset('img/bglog.jpg') }}"
                     alt="Login image" class="w-100 vh-100"
                     style="object-fit: cover; object-position: left;">
            </div>

            {{-- Right Form --}}
            <div class="col-sm-5 bg-log" style="height: 100vh">
                <div class="mx-auto mt-3 pb-xxl-5" style="width: 140px;">
                    <img src="{{ asset('img/logo.png') }}" class="logo img-fluid">
                </div>

                <div class="d-flex align-items-center justify-content-center px-2 pb-2 mt-2">

                    <form action="{{ route('external.login.process') }}" method="POST"
                          style="width: 460px;"
                          class="shadow-lg mt-4 px-4 pt-4 card bg-white">

                        @csrf

                        <h3 class="fw-semibold fs-1 pb-2 text-black">Login External</h3>
                        <i class="fa-solid fa-arrow-right-long icon"
                           style="font-size: 35px"></i>

                        {{-- Email --}}
                        <div class="form-floating mb-4">
                            <input type="email" name="email" id="email"
                                   value="{{ old('email') }}"
                                   class="form-control form-control-lg border-2 border-warning @error('email') is-invalid @enderror"
                                   placeholder="" autocomplete="off" required>
                            <label for="email"><i class="fa-solid fa-user"></i> Email</label>

                            @error('email')
                                <div class="text-danger"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="form-floating mb-4">
                            <input type="password" name="password" id="password"
                                   class="form-control form-control-lg border-2 border-warning @error('password') is-invalid @enderror"
                                   placeholder="" autocomplete="off" required>
                            <label for="password"><i class="fa-solid fa-key"></i> Password</label>

                            @error('password')
                                <div class="text-danger"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="pt-1 mb-3">
                            <button class="button shadow-sm btn w-100 fw-semibold" type="submit">
                                Masuk
                            </button>
                        </div>

                        {{-- Back to internal & register --}}
                        <div class="text-center mt-2">
                            <a href="{{ route('external.register') }}" class="btn btn-link">Belum punya akun? Daftar</a>
                            <br>
                            <a href="{{ route('login') }}" class="btn btn-link">Masuk sebagai Pegawai Internal</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://kit.fontawesome.com/e814145206.js" crossorigin="anonymous"></script>
</body>
</html>
