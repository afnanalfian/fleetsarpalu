<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
