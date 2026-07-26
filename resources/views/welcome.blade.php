<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <a href="#fitur"
                            class="hover:border-sage-300 dark:hover:border-sage-600 hover:text-sage-700 dark:hover:text-sage-300 inline-flex items-center justify-center gap-2.5 rounded-xl border border-stone-200 bg-white px-5 py-2 font-medium text-stone-700 transition-all duration-200 hover:-translate-y-0.5 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                            {{ __('Jelajahi Fitur') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
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
                            <div class="mb-5 grid grid-cols-2 gap-3">
                                @foreach ([['Total Aset', '1,248', '+12%', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'sage'], ['Dipinjam', '84', '-3%', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'amber'], ['Kondisi Baik', '96%', '+2%', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'emerald'], ['Perlu Servis', '23', '+5', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'rose']] as $stat)
                                    <div class="stat-card relative rounded-xl bg-stone-50 p-3.5 dark:bg-stone-800/50">
                                        <div class="mb-2 flex items-start justify-between">
                                            <div
                                                class="{{ $stat[4] === 'sage' ? 'bg-sage-100 dark:bg-sage-900/30 text-sage-600 dark:text-sage-400' : '' }} {{ $stat[4] === 'amber' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }} {{ $stat[4] === 'emerald' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }} {{ $stat[4] === 'rose' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' : '' }} rounded-lg p-1.5">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="{{ $stat[3] }}" />
                                                </svg>
                                            </div>
                                            <span
                                                class="{{ str_starts_with($stat[2], '+') ? 'text-emerald-500' : 'text-rose-500' }} font-mono text-[10px]">
                                                {{ $stat[2] }}
                                            </span>
                                        </div>
                                        <p class="font-display text-xl font-bold text-stone-800 dark:text-stone-100">
                                            {{ __("$stat[1]") }}</p>
                                        <p class="mt-0.5 text-[11px] text-stone-500 dark:text-stone-400">
                                            {{ __("$stat[0]") }}</p>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Mini Chart Bar --}}
                            <div class="rounded-xl bg-stone-50 p-3.5 dark:bg-stone-800/50">
                                <p
                                    class="mb-3 font-mono text-[11px] uppercase tracking-widest text-stone-400 dark:text-stone-500">
                                    {{ __('Aktivitas 7 Hari Terakhir') }}</p>
                                <div class="flex h-12 items-end gap-1.5">
                                    @foreach ([40, 65, 45, 80, 55, 70, 90] as $height)
                                        <div class="bg-sage-200 dark:bg-sage-800/60 hover:bg-sage-400 dark:hover:bg-sage-500 flex-1 cursor-pointer rounded-t-sm transition-all"
                                            style="height: {{ $height }}%"></div>
                                    @endforeach
                                </div>
                                <div class="mt-1.5 flex justify-between">
                                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                                        <span
                                            class="font-mono text-[9px] text-stone-400 dark:text-stone-600">{{ __($day) }}</span>
                                    @endforeach
                                </div>
                            </div>
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


    {{-- ===================== FITUR SECTION ===================== --}}
    <section id="fitur" class="bg-white py-24 transition-colors duration-500 dark:bg-stone-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Section Header --}}
            <div class="mx-auto mb-16 max-w-2xl text-center">
                <p class="text-sage-600 dark:text-sage-400 mb-3 font-mono text-xs uppercase tracking-[0.3em]">
                    {{ __('Kemampuan Sistem') }}</p>
                <h2 class="font-display mb-4 text-3xl font-bold text-stone-900 sm:text-4xl dark:text-stone-50">
                    {{ __('Fitur Unggulan') }} <span class="text-sage-600 dark:text-sage-400 italic">SMILE</span>
                </h2>
                <p class="leading-relaxed text-stone-500 dark:text-stone-400">
                    {{ __('Dirancang khusus untuk memenuhi kebutuhan manajemen inventaris pesantren yang efektif dan efisien.') }}
                </p>
            </div>

            {{-- Features Grid --}}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
        ['Manajemen Aset', 'Kelola seluruh inventaris dengan kategori terstruktur, QR code, dan pelacakan real-time.', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['Laporan & Analitik', 'Ekspor laporan otomatis dalam berbagai format. Visualisasi data yang informatif dan mudah dipahami.', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['Peminjaman & Pengembalian', 'Alur peminjaman yang jelas dengan notifikasi otomatis, batas waktu, dan riwayat lengkap.', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        ['Manajemen Pengguna', 'Kontrol akses berbasis peran — admin, petugas, dan santri dengan hak akses yang dapat dikonfigurasi.', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['Notifikasi Otomatis', 'Peringatan stok menipis, jadwal perawatan, dan peminjaman jatuh tempo dikirim secara otomatis.', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
        ['Object Detection (YOLO)', 'Gunakan teknologi YOLO untuk mendeteksi dan mengkategorikan aset secara otomatis melalui foto.', 'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z'],
    ] as $index => $feature)
                    <div
                        class="hover:border-sage-200 dark:hover:border-sage-700/50 group relative rounded-2xl border border-stone-200/80 bg-stone-50 p-6 transition-all duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-xl hover:shadow-stone-900/5 dark:border-stone-700/50 dark:bg-stone-800/50 dark:hover:bg-stone-800 dark:hover:shadow-stone-950/50">
                        <div
                            class="icon-ring bg-sage-100 dark:bg-sage-900/30 text-sage-600 dark:text-sage-400 group-hover:bg-sage-200 dark:group-hover:bg-sage-800/50 mb-4 flex h-11 w-11 items-center justify-center rounded-xl transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ __($feature[2]) }}" />
                            </svg>
                        </div>
                        <h3 class="font-display mb-2 font-semibold text-stone-800 dark:text-stone-100">
                            {{ __($feature[0]) }}</h3>
                        <p class="text-sm leading-relaxed text-stone-500 dark:text-stone-400">{{ __($feature[1]) }}
                        </p>

                        {{-- Hover Arrow --}}
                        <div
                            class="absolute bottom-5 right-5 translate-x-2 transform opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">
                            <svg class="text-sage-500 h-4 w-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


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
                    <div class="grid grid-cols-3 gap-4">
                        @foreach ([['1,200+', 'Total Aset'], ['50+', 'Kategori'], ['100%', 'Terdigitalisasi']] as $s)
                            <div
                                class="rounded-xl border border-stone-200 bg-white p-4 text-center dark:border-stone-700/50 dark:bg-stone-800/50">
                                <p class="font-display text-sage-600 dark:text-sage-400 text-2xl font-bold">
                                    {{ $s[0] }}</p>
                                <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">{{ $s[1] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Visual --}}
                <div class="relative">
                    <div
                        class="rounded-2xl border border-stone-200 bg-white p-8 shadow-xl shadow-stone-900/5 dark:border-stone-700/50 dark:bg-stone-800/50">
                        {{-- Timeline --}}
                        <p class="mb-6 font-mono text-xs uppercase tracking-widest text-stone-400 dark:text-stone-500">
                            Alur Sistem</p>
                        <div class="space-y-0">
                            @foreach ([['Pencatatan Aset', 'Input data barang, lokasi, kondisi & foto', 'sage'], ['Kategorisasi', 'Klasifikasi berdasarkan jenis dan unit', 'sage'], ['Peminjaman', 'Permintaan dan persetujuan digital', 'sage'], ['Monitoring', 'Pemantauan status real-time', 'sage'], ['Pelaporan', 'Laporan otomatis berkala', 'sage']] as $i => $step)
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
                    <div class="bg-sage-600 flex h-7 w-7 items-center justify-center rounded-lg">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
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
