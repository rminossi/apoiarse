<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! $head ?? '' !!}
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col" x-data="toast">
    @include('web.includes.header')

    <main class="flex-1 pt-20">
        @yield('content')
    </main>

    @include('web.includes.footer')

    <div x-show="visible" x-transition
         class="fixed bottom-6 right-6 z-50 rounded-lg px-4 py-3 text-sm font-medium text-white shadow-lg"
         :class="type === 'success' ? 'bg-brand-600' : 'bg-red-600'"
         x-cloak>
        <span x-text="message"></span>
    </div>

    @stack('scripts')
</body>
</html>
