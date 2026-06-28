<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | Staff Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/admin.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}?ver=1.0.3">
    @if(app()->getLocale() === 'ar')
        <link rel="stylesheet" href="{{ asset('assets/admin/css/visa-rtl.css') }}">
    @endif
    @stack('css')
</head>

<body class="{{ admin()->theme ?? '' }}">
<div class="page-wrapper">
    @include('layouts.dashboard.navbar')
    <div class="page-body-wrapper">
        @include('layouts.staff-portal.sidebar')
        <div class="page-body">
            @yield('content')
        </div>
        @include('layouts.dashboard.footer')
    </div>
</div>
<script src="{{ asset('assets/admin/js/admin.js') }}?ver=1.1.0"></script>
@stack('js')
</body>
</html>
