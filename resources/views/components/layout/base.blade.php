@props(['title' => null, 'bodyClass' => 'min-h-full'])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#047857">
    <meta name="description" content="SIMTAG - Sistem Informasi Survei Kepuasan Pelanggan PDAM Tirta Pinang Kota Pangkalpinang.">
    <title>{{ isset($title) ? $title.' · ' : '' }}SIMTAG Tirta Pinang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="{{ $bodyClass }}">
    {{ $slot }}
    @stack('scripts')
</body>
</html>
