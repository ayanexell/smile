<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.webp') }}" class="h-2 w-2">
    <title>SMILE — Sistem Manajemen Inventaris Latee</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        /* Subtle noise texture overlay */
        .noise-bg::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.035'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* Decorative grid pattern */
        .grid-pattern {
            background-image:
                linear-gradient(rgba(92, 127, 92, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(92, 127, 92, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .dark .grid-pattern {
            background-image:
                linear-gradient(rgba(168, 192, 168, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168, 192, 168, 0.04) 1px, transparent 1px);
        }

        /* Animated gradient orb */
        .orb {
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.18;
        }

        .dark .orb {
            opacity: 0.12;
        }

        /* Nav blur backdrop */
        .nav-glass {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #a8c0a8;
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #486548;
        }

        /* Stat card border glow */
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(92, 127, 92, 0.3), transparent 60%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* Delay utilities */
        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        .delay-400 {
            animation-delay: 0.4s;
        }

        .delay-500 {
            animation-delay: 0.5s;
        }

        .delay-600 {
            animation-delay: 0.6s;
        }

        .opacity-0-init {
            opacity: 0;
        }

        /* Feature icon ring */
        .icon-ring {
            box-shadow: 0 0 0 6px rgba(92, 127, 92, 0.08), 0 0 0 12px rgba(92, 127, 92, 0.04);
        }

        .dark .icon-ring {
            box-shadow: 0 0 0 6px rgba(92, 127, 92, 0.12), 0 0 0 12px rgba(92, 127, 92, 0.06);
        }
    </style>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body
    class="noise-bg font-body bg-stone-50 text-stone-800 antialiased transition-colors duration-500 dark:bg-stone-950 dark:text-stone-100">

    <x-header-welcome />


    {{-- ===================== HERO SECTION ===================== --}}
    <section id="home" class="grid-pattern relative flex min-h-screen items-center overflow-hidden">

        {{-- Decorative Orbs --}}
        <div class="bg-sage-400 orb animate-float absolute -left-24 top-1/4 h-96 w-96"></div>
        <div class="bg-sage-300 orb animate-float-slow absolute -right-24 bottom-1/4 h-80 w-80"></div>
        <div class="orb animate-float absolute left-1/3 top-3/4 h-64 w-64 bg-stone-300 dark:bg-stone-700"
            style="animation-delay: -3s;"></div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-4 pb-16 pt-24 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">

                {{-- Left: Text Content --}}
                <div class="text-center lg:text-left">

                    {{-- <div >
                    </div> --}}
                    {{-- Badge --}}
                    <div
                        class="opacity-0-init animate-fade-up bg-sage-100 dark:bg-sage-900/40 border-sage-200 dark:border-sage-700/50 text-sage-700 dark:text-sage-300 mb-6 inline-flex items-center gap-2 rounded-full border px-3 py-1.5 font-mono text-xs font-medium uppercase tracking-wider">
                        <span class="bg-sage-500 animate-pulse-soft h-1.5 w-1.5 rounded-full"></span>
                        {{ __('Sistem Aktif & Terintegrasi') }}
                    </div>

                    {{-- Headline --}}
                    <h1
                        class="opacity-0-init animate-fade-up font-display mb-4 text-4xl font-bold leading-tight text-stone-900 delay-100 sm:text-5xl lg:text-6xl dark:text-stone-50">
                        {{ __('Selamat Datang di') }}<br>
                        <span class="text-sage-600 dark:text-sage-400 italic">SMILE</span>
                    </h1>

                    {{-- Sub Headline --}}
                    <p
                        class="opacity-0-init animate-fade-up mb-3 font-mono text-xs uppercase tracking-[0.2em] text-stone-500 delay-200 dark:text-stone-400">
                        {{ __('Sistem Manajemen Inventaris Latee') }}
                    </p>

                    <p
                        class="opacity-0-init animate-fade-up mx-auto mb-8 max-w-md text-base leading-relaxed text-stone-600 delay-300 sm:text-lg lg:mx-0 dark:text-stone-400">
                        {{ __('Platform pengelolaan inventaris modern untuk') }}
                        <strong
                            class="font-medium text-stone-700 dark:text-stone-300">{{ __('Pondok Pesantren Annuqayah Latee') }}</strong>,
                        {{ __('Guluk-Guluk Sumenep') }} — {{ __('akurat, efisien, dan mudah digunakan') }}.
                    </p>

                    {{-- CTA Buttons --}}
                    <div
                        class="opacity-0-init animate-fade-up delay-400 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                        @guest
                            <a href="{{ route('login') }}"
                                class="bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 hover:shadow-sage-600/25 inline-flex items-center justify-center gap-2.5 rounded-xl px-5 py-2 text-sm font-medium text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                                {{ __('Masuk ke Sistem') }}
                            </a>
                        @endguest
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 hover:shadow-sage-600/25 inline-flex items-center justify-center gap-2.5 rounded-xl px-5 py-2 text-sm font-medium text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                                {{ __('Dashboard') }}
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Right: Stats Dashboard Card --}}
                <div class="opacity-0-init animate-fade-up relative delay-300 lg:flex lg:justify-end">
                    <div class="relative mx-auto w-full max-w-md lg:mx-0">

                        {{-- Main Dashboard Preview Card --}}
                        <div
                            class="animate-float rounded-2xl border border-stone-200/80 bg-white p-6 shadow-2xl shadow-stone-900/10 dark:border-stone-700/50 dark:bg-stone-900 dark:shadow-stone-950/50">

                            {{-- Card Header --}}
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <p
                                        class="font-mono text-xs uppercase tracking-widest text-stone-400 dark:text-stone-500">
                                        {{ __('Ringkasan Inventaris') }}</p>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400">
                                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                                    Live
                                </span>
                            </div>

                            {{-- Stats Grid --}}
                            <livewire:welcome-stats />
                        </div>

                        {{-- Floating Badge --}}
                        <div
                            class="animate-float-slow absolute -bottom-4 -left-4 rounded-xl border border-stone-200 bg-white px-4 py-2.5 shadow-lg shadow-stone-900/10 dark:border-stone-700 dark:bg-stone-800 dark:shadow-stone-950/50">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="bg-sage-100 dark:bg-sage-900/40 flex h-8 w-8 items-center justify-center rounded-lg">
                                    <svg class="text-sage-600 dark:text-sage-400 h-4 w-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-stone-700 dark:text-stone-300">
                                        {{ __('Data Aman') }}</p>
                                    <p class="text-[10px] text-stone-400 dark:text-stone-500">
                                        {{ __('Terenkripsi & Terlindungi') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scroll Indicator --}}
            <div class="opacity-0-init animate-fade-up delay-600 mt-16 flex justify-center">
                <a href="#fitur"
                    class="hover:text-sage-500 flex flex-col items-center gap-2 text-stone-400 transition-colors dark:text-stone-600">
                    <span class="font-mono text-xs uppercase tracking-widest">{{ __('Gulir ke Bawah') }}</span>
                    <div class="flex h-7 w-4 items-start justify-center rounded-full border-2 border-current pt-1.5">
                        <div class="h-2 w-1 animate-bounce rounded-full bg-current"></div>
                    </div>
                </a>
            </div>
        </div>
    </section>


    {{-- ===================== INVENTARIS SERING DIPINJAM SECTION ===================== --}}
    <livewire:top-inventaris />


    {{-- ===================== TENTANG SECTION ===================== --}}
    <section id="tentang" class="bg-stone-50 py-24 transition-colors duration-500 dark:bg-stone-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-16 lg:grid-cols-2">

                {{-- Left: Info --}}
                <div>
                    <p class="text-sage-600 dark:text-sage-400 mb-3 font-mono text-xs uppercase tracking-[0.3em]">
                        {{ __('Tentang Kami') }}</p>
                    <h2
                        class="font-display mb-6 text-3xl font-bold leading-tight text-stone-900 sm:text-4xl dark:text-stone-50">
                        {{ __('Pondok Pesantren') }}<br>
                        <span class="text-sage-600 dark:text-sage-400 italic">{{ __('Annuqayah Latee') }}</span>
                    </h2>
                    <p class="mb-4 leading-relaxed text-stone-600 dark:text-stone-400">
                        SMILE
                        {{ __('dikembangkan untuk mendukung operasional Pondok Pesantren Annuqayah Latee di Guluk-Guluk, Sumenep — salah satu lembaga pendidikan Islam terkemuka di Madura.') }}
                    </p>
                    <p class="mb-8 leading-relaxed text-stone-600 dark:text-stone-400">
                        {{ __('Sistem ini hadir untuk menggantikan pencatatan manual yang rawan kesalahan, memastikan setiap aset pesantren dapat dikelola secara transparan, akurat, dan bertanggung jawab.') }}
                    </p>

                    {{-- Stats Row --}}
                    {{-- <div class="grid grid-cols-3 gap-4">
                        @foreach ([['1,200+', 'Total Aset'], ['50+', 'Kategori'], ['100%', 'Terdigitalisasi']] as $s)
                            <div
                                class="rounded-xl border border-stone-200 bg-white p-4 text-center dark:border-stone-700/50 dark:bg-stone-800/50">
                                <p class="font-display text-sage-600 dark:text-sage-400 text-2xl font-bold">
                                    {{ $s[0] }}</p>
                                <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">{{ $s[1] }}</p>
                            </div>
                        @endforeach
                    </div> --}}
                </div>

                {{-- Right: Visual --}}
                <div class="relative">
                    <div
                        class="rounded-2xl border border-stone-200 bg-white p-8 shadow-xl shadow-stone-900/5 dark:border-stone-700/50 dark:bg-stone-800/50">
                        {{-- Timeline --}}
                        <p class="mb-6 font-mono text-xs uppercase tracking-widest text-stone-400 dark:text-stone-500">
                            Alur & Regulasi Peminjaman</p>
                        <div class="space-y-0">
                            @foreach ([['Mendaftarkan Diri', 'Registrasi akun peminjam', 'sage'], ['Memilih Inventaris', 'Pilih barang yang akan dipinjam', 'sage'], ['Verifikasi Admin', 'Admin menyetujui permohonan', 'sage'], ['Cetak Bukti Peminjaman', 'Dokumen bukti transaksi', 'sage'], ['Pengembalian', 'Inventaris dikembalikan tepat waktu', 'sage']] as $i => $step)
                                <div class="{{ $i < 4 ? 'pb-6' : '' }} relative flex gap-4">
                                    {{-- Line --}}
                                    @if ($i < 4)
                                        <div class="bg-sage-200 dark:bg-sage-800 absolute bottom-0 left-4 top-8 w-px">
                                        </div>
                                    @endif

                                    {{-- Dot --}}
                                    <div
                                        class="bg-sage-100 dark:bg-sage-900/30 border-sage-300 dark:border-sage-700 relative flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2">
                                        <div class="bg-sage-500 h-2 w-2 rounded-full"></div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="pt-1">
                                        <p class="text-sm font-semibold text-stone-700 dark:text-stone-300">
                                            {{ $step[0] }}</p>
                                        <p class="mt-0.5 text-xs text-stone-400 dark:text-stone-500">
                                            {{ $step[1] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ===================== PETA & KONTAK SECTION ===================== --}}
    <section id="peta-kontak" class="bg-white py-24 transition-colors duration-500 dark:bg-stone-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Section Header --}}
            <div class="mx-auto mb-16 max-w-2xl text-center">
                <p class="text-sage-600 dark:text-sage-400 mb-3 font-mono text-xs uppercase tracking-[0.3em]">
                    {{ __('Lokasi & Kontak') }}</p>
                <h2 class="font-display mb-4 text-3xl font-bold text-stone-900 sm:text-4xl dark:text-stone-50">
                    {{ __('Temukan') }} <span
                        class="text-sage-600 dark:text-sage-400 italic">{{ __('Kami') }}</span>
                </h2>
                <p class="leading-relaxed text-stone-500 dark:text-stone-400">
                    {{ __('Kunjungi atau hubungi kami untuk informasi lebih lanjut mengenai SMILE.') }}
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-2 lg:items-stretch">

                {{-- Left: Map --}}
                <div
                    class="overflow-hidden rounded-2xl border border-stone-200/80 shadow-xl shadow-stone-900/5 dark:border-stone-700/50">
                    <iframe
                        src="https://www.google.com/maps?q=Pondok+Pesantren+Annuqayah+Latee,+Guluk-Guluk,+Sumenep&output=embed"
                        class="h-full min-h-[420px] w-full grayscale-[10%] dark:grayscale-0" style="border:0;"
                        allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        title="{{ __('Lokasi Pondok Pesantren Annuqayah Latee') }}">
                    </iframe>
                </div>

                {{-- Right: Contact Info --}}
                <div
                    class="flex flex-col justify-between rounded-2xl border border-stone-200/80 bg-stone-50 p-8 dark:border-stone-700/50 dark:bg-stone-800/50">
                    <div class="space-y-6">
                        @foreach ([['Alamat', 'Jl. Pondok Pesantren Annuqayah, Guluk-Guluk, Sumenep, Madura, Jawa Timur', 'M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'], ['Telepon', '(0328) 821-XXX', 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'], ['Email', 'info@smile-latee.ac.id', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'], ['Jam Operasional', 'Senin – Sabtu, 08.00 – 16.00 WIB', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z']] as $c)
                            <div class="flex items-start gap-4">
                                <div
                                    class="bg-sage-100 dark:bg-sage-900/30 text-sage-600 dark:text-sage-400 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $c[2] }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-stone-700 dark:text-stone-300">
                                        {{ __($c[0]) }}</p>
                                    <p class="mt-0.5 text-sm text-stone-500 dark:text-stone-400">
                                        {{ __($c[1]) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- WhatsApp CTA --}}
                    <a href="https://wa.me/62XXXXXXXXXX" target="_blank" rel="noopener"
                        class="bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 hover:shadow-sage-600/25 mt-8 inline-flex items-center justify-center gap-2.5 rounded-xl px-5 py-2.5 text-sm font-medium text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.28-1.38a9.9 9.9 0 004.76 1.21h.01c5.46 0 9.9-4.45 9.9-9.9C21.96 6.45 17.5 2 12.04 2zm5.85 14.03c-.25.7-1.24 1.28-2.02 1.44-.55.11-1.26.2-3.65-.79-2.6-1.08-4.28-3.72-4.41-3.9-.13-.18-1.06-1.41-1.06-2.7 0-1.28.68-1.91.92-2.17.25-.26.54-.32.72-.32.18 0 .36 0 .52.01.17.01.39-.06.61.47.25.6.85 2.08.92 2.23.07.15.12.32.02.51-.09.19-.14.31-.28.48-.14.16-.29.36-.42.48-.14.13-.28.28-.12.55.16.27.72 1.19 1.55 1.93 1.06.95 1.96 1.24 2.23 1.38.27.13.43.11.59-.07.16-.18.68-.79.87-1.06.18-.27.36-.22.61-.13.25.09 1.57.74 1.84.87.27.13.45.2.51.31.07.11.07.62-.18 1.32z" />
                        </svg>
                        {{ __('Hubungi via WhatsApp') }}
                    </a>
                </div>
            </div>
        </div>
    </section>


    {{-- ===================== CTA SECTION ===================== --}}
    <section id="kontak" class="bg-sage-700 dark:bg-sage-900 relative overflow-hidden py-24">

        {{-- BG Pattern --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 50%, white 1px, transparent 1px); background-size: 30px 30px;">
        </div>

        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-sage-300 mb-4 font-mono text-xs uppercase tracking-[0.3em]">{{ __('Mulai Sekarang') }}</p>
            <h2 class="font-display mb-6 text-3xl font-bold text-white sm:text-4xl">
                {{ __('Siap Mengelola Inventaris') }}<br class="hidden sm:block">
                <span class="italic">{{ __('Lebih Cerdas?') }}</span>
            </h2>
            <p class="text-sage-200 mb-10 leading-relaxed">
                {{ __('Masuk ke sistem SMILE dan mulai kelola seluruh aset pesantren dengan mudah, transparan, dan efisien.') }}
            </p>
            <a href="{{ route('list-inventaris') }}" wire:navigate
                class="dark:bg-grey-300 text-sage-700 inline-flex items-center gap-3 rounded-xl bg-white px-5 py-2 text-sm font-semibold shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:bg-stone-50 hover:shadow-xl">
                {{ __('Telusuri Inventaris') }}
            </a>
        </div>
    </section>


    {{-- ===================== FOOTER ===================== --}}
    <footer class="bg-stone-900 py-10 text-stone-400 transition-colors duration-500 dark:bg-stone-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <img src="{{ asset('assets/logo.webp') }}" alt="Logo" class="w-7" />
                    </div>
                    <span class="font-mono text-sm text-stone-300">SMILE —
                        {{ __('Sistem Manajemen Inventaris Latee') }}</span>
                </div>
                <p class="font-mono text-xs text-stone-600">
                    &copy; {{ date('Y') }} PP. Annuqayah Latee, Guluk-Guluk Sumenep
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @fluxScripts
</body>

</html>
