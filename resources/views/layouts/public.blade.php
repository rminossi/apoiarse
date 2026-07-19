<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! $head ?? '' !!}
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex min-h-full w-full flex-col overflow-x-hidden antialiased" x-data="toast">
    @include('web.includes.header')

    <main class="w-full min-w-0 flex-1 pt-[4.5rem] sm:pt-20">
        @yield('content')
    </main>

    @include('web.includes.footer')

    <div x-show="visible" x-transition x-cloak
         class="fixed bottom-4 left-4 right-4 z-50 mx-auto max-w-sm rounded-lg px-4 py-3 text-center text-sm font-medium text-white shadow-lg sm:bottom-6 sm:left-auto sm:right-6 sm:text-left"
         :class="type === 'success' ? 'bg-brand-600' : 'bg-red-600'">
        <span x-text="message"></span>
    </div>

    @stack('scripts')
</body>
</html>
