<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <title>{{ config('app.name') }} | {{ auth()->user()->role_label }} | @yield('title')</title>
</head>

<body>
<div class="container-fluid p-0">
    @include('layouts.sidebar')
    <main class="content">
        @include('layouts.header')

        <div class="p-4">
            @yield('content')
        </div>

        @include('layouts.footer')
    </main>
</div>

<script src="https://kit.fontawesome.com/e814145206.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
