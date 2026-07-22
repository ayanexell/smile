<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMILE — Sistem Manajemen Inventaris Latee</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

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

        .dark .orb { opacity: 0.12; }

        /* Nav blur backdrop */
        .nav-glass {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #a8c0a8; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #486548; }

        /* Stat card border glow */
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(92,127,92,0.3), transparent 60%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* Delay utilities */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .opacity-0-init { opacity: 0; }

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

<body class="noise-bg font-body bg-stone-50 dark:bg-stone-950 text-stone-800 dark:text-stone-100 transition-colors duration-500 antialiased">

    <x-header-welcome />


    {{-- ===================== HERO SECTION ===================== --}}
    <section id="home" class="relative min-h-screen flex items-center grid-pattern overflow-hidden">

        {{-- Decorative Orbs --}}
        <div class="absolute top-1/4 -left-24 w-96 h-96 bg-sage-400 orb animate-float"></div>
        <div class="absolute bottom-1/4 -right-24 w-80 h-80 bg-sage-300 orb animate-float-slow"></div>
        <div class="absolute top-3/4 left-1/3 w-64 h-64 bg-stone-300 dark:bg-stone-700 orb animate-float" style="animation-delay: -3s;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 w-full">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                {{-- Left: Text Content --}}
                <div class="text-center lg:text-left">

                    {{-- Badge --}}
                    <div class="opacity-0-init animate-fade-up inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sage-100 dark:bg-sage-900/40 border border-sage-200 dark:border-sage-700/50 text-sage-700 dark:text-sage-300 text-xs font-mono font-medium tracking-wider uppercase mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-sage-500 animate-pulse-soft"></span>
                        {{ __("Sistem Aktif & Terintegrasi") }}
                    </div>

                    {{-- Headline --}}
                    <h1 class="opacity-0-init animate-fade-up delay-100 font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight text-stone-900 dark:text-stone-50 mb-4">
                        {{ __("Selamat Datang di") }}<br>
                        <span class="text-sage-600 dark:text-sage-400 italic">SMILE</span>
                    </h1>

                    {{-- Sub Headline --}}
                    <p class="opacity-0-init animate-fade-up delay-200 font-mono text-xs tracking-[0.2em] uppercase text-stone-500 dark:text-stone-400 mb-3">
                        {{ __("Sistem Manajemen Inventaris Latee") }}
                    </p>

                    <p class="opacity-0-init animate-fade-up delay-300 text-base sm:text-lg text-stone-600 dark:text-stone-400 leading-relaxed max-w-md mx-auto lg:mx-0 mb-8">
                        {{ __("Platform pengelolaan inventaris modern untuk") }}
                        <strong class="text-stone-700 dark:text-stone-300 font-medium">{{ __("Pondok Pesantren Annuqayah Latee") }}</strong>,
                        {{ __("Guluk-Guluk Sumenep") }} — {{ __("akurat, efisien, dan mudah digunakan") }}.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="opacity-0-init animate-fade-up delay-400 flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        @guest
                            <a href="{{ route('login') }}"
                           class="text-sm inline-flex items-center justify-center gap-2.5 px-5 py-2 bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 text-white font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg hover:shadow-sage-600/25 hover:-translate-y-0.5">
                            {{ __("Masuk ke Sistem") }}
                        </a>
                        @endguest
                        @auth
                            <a href="{{ route('dashboard') }}"
                           class="text-sm inline-flex items-center justify-center gap-2.5 px-5 py-2 bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 text-white font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg hover:shadow-sage-600/25 hover:-translate-y-0.5">
                            {{ __("Dashboard") }}
                        </a>
                        @endauth
                        <a href="#fitur"
                           class="inline-flex items-center justify-center gap-2.5 px-5 py-2 bg-white dark:bg-stone-800 text-stone-700 dark:text-stone-300 font-medium rounded-xl border border-stone-200 dark:border-stone-700 hover:border-sage-300 dark:hover:border-sage-600 hover:text-sage-700 dark:hover:text-sage-300 transition-all duration-200 hover:-translate-y-0.5">
                            {{ __("Jelajahi Fitur") }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Right: Stats Dashboard Card --}}
                <div class="opacity-0-init animate-fade-up delay-300 relative lg:flex lg:justify-end">
                    <div class="relative max-w-md mx-auto lg:mx-0 w-full">

                        {{-- Main Dashboard Preview Card --}}
                        <div class="bg-white dark:bg-stone-900 rounded-2xl shadow-2xl shadow-stone-900/10 dark:shadow-stone-950/50 border border-stone-200/80 dark:border-stone-700/50 p-6 animate-float">

                            {{-- Card Header --}}
                            <div class="flex items-center justify-between mb-5">
                                <div>
                                    <p class="text-xs font-mono text-stone-400 dark:text-stone-500 uppercase tracking-widest">{{ __("Ringkasan Inventaris") }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2.5 py-1 rounded-full font-medium">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    Live
                                </span>
                            </div>

                            {{-- Stats Grid --}}
                            <div class="grid grid-cols-2 gap-3 mb-5">
                                @foreach([
                                    ['Total Aset', '1,248', '+12%', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'sage'],
                                    ['Dipinjam', '84', '-3%', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'amber'],
                                    ['Kondisi Baik', '96%', '+2%', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'emerald'],
                                    ['Perlu Servis', '23', '+5', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'rose'],
                                ] as $stat)
                                <div class="stat-card relative bg-stone-50 dark:bg-stone-800/50 rounded-xl p-3.5">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="p-1.5 rounded-lg
                                            {{ $stat[4] === 'sage' ? 'bg-sage-100 dark:bg-sage-900/30 text-sage-600 dark:text-sage-400' : '' }}
                                            {{ $stat[4] === 'amber' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                                            {{ $stat[4] === 'emerald' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                                            {{ $stat[4] === 'rose' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' : '' }}
                                        ">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat[3] }}"/>
                                            </svg>
                                        </div>
                                        <span class="text-[10px] font-mono {{ str_starts_with($stat[2], '+') ? 'text-emerald-500' : 'text-rose-500' }}">
                                            {{ $stat[2] }}
                                        </span>
                                    </div>
                                    <p class="text-xl font-display font-bold text-stone-800 dark:text-stone-100">{{ __("$stat[1]") }}</p>
                                    <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-0.5">{{ __("$stat[0]") }}</p>
                                </div>
                                @endforeach
                            </div>

                            {{-- Mini Chart Bar --}}
                            <div class="bg-stone-50 dark:bg-stone-800/50 rounded-xl p-3.5">
                                <p class="text-[11px] font-mono text-stone-400 dark:text-stone-500 uppercase tracking-widest mb-3">{{ __("Aktivitas 7 Hari Terakhir") }}</p>
                                <div class="flex items-end gap-1.5 h-12">
                                    @foreach([40, 65, 45, 80, 55, 70, 90] as $height)
                                    <div class="flex-1 bg-sage-200 dark:bg-sage-800/60 rounded-t-sm transition-all hover:bg-sage-400 dark:hover:bg-sage-500 cursor-pointer"
                                         style="height: {{ $height }}%"></div>
                                    @endforeach
                                </div>
                                <div class="flex justify-between mt-1.5">
                                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                                    <span class="text-[9px] text-stone-400 dark:text-stone-600 font-mono">{{ __($day) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Floating Badge --}}
                        <div class="absolute -bottom-4 -left-4 bg-white dark:bg-stone-800 rounded-xl shadow-lg shadow-stone-900/10 dark:shadow-stone-950/50 border border-stone-200 dark:border-stone-700 px-4 py-2.5 animate-float-slow">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-sage-100 dark:bg-sage-900/40 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-sage-600 dark:text-sage-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-stone-700 dark:text-stone-300">{{ __("Data Aman") }}</p>
                                    <p class="text-[10px] text-stone-400 dark:text-stone-500">{{ __("Terenkripsi & Terlindungi") }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scroll Indicator --}}
            <div class="flex justify-center mt-16 opacity-0-init animate-fade-up delay-600">
                <a href="#fitur" class="flex flex-col items-center gap-2 text-stone-400 dark:text-stone-600 hover:text-sage-500 transition-colors">
                    <span class="text-xs font-mono tracking-widest uppercase">{{ __("Gulir ke Bawah") }}</span>
                    <div class="w-4 h-7 border-2 border-current rounded-full flex items-start justify-center pt-1.5">
                        <div class="w-1 h-2 bg-current rounded-full animate-bounce"></div>
                    </div>
                </a>
            </div>
        </div>
    </section>


    {{-- ===================== FITUR SECTION ===================== --}}
    <section id="fitur" class="py-24 bg-white dark:bg-stone-900 transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section Header --}}
            <div class="text-center max-w-2xl mx-auto mb-16">
                <p class="font-mono text-xs text-sage-600 dark:text-sage-400 uppercase tracking-[0.3em] mb-3">{{ __("Kemampuan Sistem") }}</p>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-stone-900 dark:text-stone-50 mb-4">
                    {{ __("Fitur Unggulan") }} <span class="italic text-sage-600 dark:text-sage-400">SMILE</span>
                </h2>
                <p class="text-stone-500 dark:text-stone-400 leading-relaxed">
                    {{ __("Dirancang khusus untuk memenuhi kebutuhan manajemen inventaris pesantren yang efektif dan efisien.") }}
                </p>
            </div>

            {{-- Features Grid --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    [
                        'Manajemen Aset',
                        'Kelola seluruh inventaris dengan kategori terstruktur, QR code, dan pelacakan real-time.',
                        'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    ],
                    [
                        'Laporan & Analitik',
                        'Ekspor laporan otomatis dalam berbagai format. Visualisasi data yang informatif dan mudah dipahami.',
                        'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'Peminjaman & Pengembalian',
                        'Alur peminjaman yang jelas dengan notifikasi otomatis, batas waktu, dan riwayat lengkap.',
                        'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                    ],
                    [
                        'Manajemen Pengguna',
                        'Kontrol akses berbasis peran — admin, petugas, dan santri dengan hak akses yang dapat dikonfigurasi.',
                        'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    ],
                    [
                        'Notifikasi Otomatis',
                        'Peringatan stok menipis, jadwal perawatan, dan peminjaman jatuh tempo dikirim secara otomatis.',
                        'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                    ],
                    [
                        'Object Detection (YOLO)',
                        'Gunakan teknologi YOLO untuk mendeteksi dan mengkategorikan aset secara otomatis melalui foto.',
                        'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z',
                    ],
                ] as $index => $feature)
                <div class="group relative bg-stone-50 dark:bg-stone-800/50 hover:bg-white dark:hover:bg-stone-800 rounded-2xl p-6 border border-stone-200/80 dark:border-stone-700/50 hover:border-sage-200 dark:hover:border-sage-700/50 hover:shadow-xl hover:shadow-stone-900/5 dark:hover:shadow-stone-950/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="icon-ring w-11 h-11 rounded-xl bg-sage-100 dark:bg-sage-900/30 flex items-center justify-center mb-4 text-sage-600 dark:text-sage-400 group-hover:bg-sage-200 dark:group-hover:bg-sage-800/50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ __($feature[2]) }}"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-stone-800 dark:text-stone-100 mb-2">{{ __($feature[0]) }}</h3>
                    <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed">{{ __($feature[1]) }}</p>

                    {{-- Hover Arrow --}}
                    <div class="absolute bottom-5 right-5 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                        <svg class="w-4 h-4 text-sage-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ===================== TENTANG SECTION ===================== --}}
    <section id="tentang" class="py-24 bg-stone-50 dark:bg-stone-950 transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">

                {{-- Left: Info --}}
                <div>
                    <p class="font-mono text-xs text-sage-600 dark:text-sage-400 uppercase tracking-[0.3em] mb-3">{{ __("Tentang Kami") }}</p>
                    <h2 class="font-display text-3xl sm:text-4xl font-bold text-stone-900 dark:text-stone-50 mb-6 leading-tight">
                        {{ __("Pondok Pesantren") }}<br>
                        <span class="italic text-sage-600 dark:text-sage-400">{{ __("Annuqayah Latee") }}</span>
                    </h2>
                    <p class="text-stone-600 dark:text-stone-400 leading-relaxed mb-4">
                        SMILE {{ __("dikembangkan untuk mendukung operasional Pondok Pesantren Annuqayah Latee di Guluk-Guluk, Sumenep — salah satu lembaga pendidikan Islam terkemuka di Madura.") }}
                    </p>
                    <p class="text-stone-600 dark:text-stone-400 leading-relaxed mb-8">
                        {{ __("Sistem ini hadir untuk menggantikan pencatatan manual yang rawan kesalahan, memastikan setiap aset pesantren dapat dikelola secara transparan, akurat, dan bertanggung jawab.") }}
                    </p>

                    {{-- Stats Row --}}
                    <div class="grid grid-cols-3 gap-4">
                        @foreach([['1,200+', 'Total Aset'], ['50+', 'Kategori'], ['100%', 'Terdigitalisasi']] as $s)
                        <div class="text-center p-4 bg-white dark:bg-stone-800/50 rounded-xl border border-stone-200 dark:border-stone-700/50">
                            <p class="font-display text-2xl font-bold text-sage-600 dark:text-sage-400">{{ $s[0] }}</p>
                            <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">{{ $s[1] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Visual --}}
                <div class="relative">
                    <div class="bg-white dark:bg-stone-800/50 rounded-2xl border border-stone-200 dark:border-stone-700/50 p-8 shadow-xl shadow-stone-900/5">
                        {{-- Timeline --}}
                        <p class="font-mono text-xs text-stone-400 dark:text-stone-500 uppercase tracking-widest mb-6">Alur Sistem</p>
                        <div class="space-y-0">
                            @foreach([
                                ['Pencatatan Aset', 'Input data barang, lokasi, kondisi & foto', 'sage'],
                                ['Kategorisasi', 'Klasifikasi berdasarkan jenis dan unit', 'sage'],
                                ['Peminjaman', 'Permintaan dan persetujuan digital', 'sage'],
                                ['Monitoring', 'Pemantauan status real-time', 'sage'],
                                ['Pelaporan', 'Laporan otomatis berkala', 'sage'],
                            ] as $i => $step)
                            <div class="relative flex gap-4 {{ $i < 4 ? 'pb-6' : '' }}">
                                {{-- Line --}}
                                @if($i < 4)
                                <div class="absolute left-4 top-8 bottom-0 w-px bg-sage-200 dark:bg-sage-800"></div>
                                @endif

                                {{-- Dot --}}
                                <div class="relative flex-shrink-0 w-8 h-8 rounded-full bg-sage-100 dark:bg-sage-900/30 border-2 border-sage-300 dark:border-sage-700 flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full bg-sage-500"></div>
                                </div>

                                {{-- Content --}}
                                <div class="pt-1">
                                    <p class="text-sm font-semibold text-stone-700 dark:text-stone-300">{{ $step[0] }}</p>
                                    <p class="text-xs text-stone-400 dark:text-stone-500 mt-0.5">{{ $step[1] }}</p>
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
    <section id="kontak" class="py-24 bg-sage-700 dark:bg-sage-900 relative overflow-hidden">

        {{-- BG Pattern --}}
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 50%, white 1px, transparent 1px); background-size: 30px 30px;">
        </div>

        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="font-mono text-xs text-sage-300 uppercase tracking-[0.3em] mb-4">{{ __("Mulai Sekarang") }}</p>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mb-6">
                {{ __("Siap Mengelola Inventaris") }}<br class="hidden sm:block">
                <span class="italic">{{ __("Lebih Cerdas?") }}</span>
            </h2>
            <p class="text-sage-200 mb-10 leading-relaxed">
                {{ __("Masuk ke sistem SMILE dan mulai kelola seluruh aset pesantren dengan mudah, transparan, dan efisien.") }}
            </p>
               <a href="{{ route('list-inventaris') }}" wire:navigate
                class="inline-flex items-center gap-3 px-5 py-2 bg-white dark:bg-grey-300 text-sage-700 font-semibold rounded-xl hover:bg-stone-50 transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5 text-sm">
                    {{ __("Telusuri Inventaris") }}
                </a>
        </div>
    </section>


    {{-- ===================== FOOTER ===================== --}}
    <footer class="bg-stone-900 dark:bg-stone-950 text-stone-400 py-10 transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-sage-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-mono text-stone-300">SMILE — {{ __("Sistem Manajemen Inventaris Latee") }}</span>
                </div>
                <p class="text-xs font-mono text-stone-600">
                    &copy; {{ date('Y') }} PP. Annuqayah Latee, Guluk-Guluk Sumenep
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @fluxScripts
</body>
</html>
