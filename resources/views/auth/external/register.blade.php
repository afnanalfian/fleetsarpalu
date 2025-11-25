<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <title>{{ config('app.name') }} | Registrasi External</title>
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

            {{-- Form Right --}}
            <div class="col-sm-5 bg-log" style="height: 100vh">
                <div class="mx-auto mt-3 pb-xxl-5" style="width: 140px;">
                    <img src="{{ asset('img/logo.png') }}" class="logo img-fluid">
                </div>

                <div class="d-flex align-items-center justify-content-center px-2 pb-2 mt-2">

                    <form action="{{ route('external.register.process') }}" method="POST"
                          style="width: 460px;"
                          class="shadow-lg mt-4 px-4 pt-4 card bg-white">

                        @csrf

                        <h3 class="fw-semibold fs-1 pb-2 text-black">Registrasi External</h3>
                        <i class="fa-solid fa-user-plus icon"
                           style="font-size: 35px"></i>

                        {{-- Name --}}
                        <div class="form-floating mb-4">
                            <input type="text" name="name" class="form-control border-2 border-warning"
                                   value="{{ old('name') }}" placeholder="" required>
                            <label><i class="fa-solid fa-user"></i> Nama Lengkap</label>

                            @error('name')
                                <div class="text-danger"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-floating mb-4">
                            <input type="email" name="email" class="form-control border-2 border-warning"
                                   value="{{ old('email') }}" placeholder="" required>
                            <label><i class="fa-solid fa-envelope"></i> Email</label>

                            @error('email')
                                <div class="text-danger"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="form-floating mb-4">
                            <input type="text" name="phone" class="form-control border-2 border-warning"
                                   value="{{ old('phone') }}" placeholder="">
                            <label><i class="fa-solid fa-phone"></i> Telepon</label>

                            @error('phone')
                                <div class="text-danger"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        {{-- Institution --}}
                        <div class="form-floating mb-4">
                            <input type="text" name="institution"
                                   class="form-control border-2 border-warning"
                                   value="{{ old('institution') }}" placeholder="">
                            <label><i class="fa-solid fa-building"></i> Instansi / Individu</label>

                            @error('institution')
                                <div class="text-danger"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="form-floating mb-4">
                            <input type="password" name="password" class="form-control border-2 border-warning"
                                   placeholder="" required>
                            <label><i class="fa-solid fa-key"></i> Password</label>

                            @error('password')
                                <div class="text-danger"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="form-floating mb-4">
                            <input type="password" name="password_confirmation"
                                   class="form-control border-2 border-warning"
                                   placeholder="" required>
                            <label><i class="fa-solid fa-key"></i> Konfirmasi Password</label>

                            @error('password_confirmation')
                                <div class="text-danger"><small>{{ $message }}</small></div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="pt-1 mb-3">
                            <button class="button btn shadow-sm w-100 fw-semibold" type="submit">
                                Daftar
                            </button>
                        </div>

                        {{-- Links --}}
                        <div class="text-center mt-2">
                            <a href="{{ route('external.login') }}" class="btn btn-link">Sudah punya akun? Masuk</a>
                            <br>
                            <a href="{{ route('login') }}" class="btn btn-link">Masuk sebagai Internal</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://kit.fontawesome.com/e814145206.js" crossorigin="anonymous"></script>
</body>
</html>
