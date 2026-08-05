<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">

<head>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.webp') }}" class="h-2 w-2">
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    @include('partials.head')
</head>

<body
    class="noise-bg font-body bg-stone-50 text-stone-800 antialiased transition-colors duration-500 dark:bg-stone-950 dark:text-stone-100">
    <x-header-welcome />
    {{ $slot }}
    @livewireScripts
    @fluxScripts
</body>

</html>
