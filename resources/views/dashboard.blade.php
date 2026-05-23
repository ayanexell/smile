<x-layouts::app :heading="__('Dashboard')">

    {{-- ══════════════════════════════════
         WELCOME BANNER
    ══════════════════════════════════ --}}
    <div class="welcome-banner dash-animate d1">

        {{-- Grid pattern dekoratif --}}
        <svg class="banner-grid" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="dash-grid" width="28" height="28" patternUnits="userSpaceOnUse">
                    <path d="M 28 0 L 0 0 0 28" fill="none" stroke="currentColor" stroke-width="0.5" opacity="0.3"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#dash-grid)"/>
        </svg>

        <div class="banner-glow"></div>

        <div class="banner-text">
            <div class="banner-eyebrow">{{ __('Selamat datang') }}</div>
            <h1 class="banner-title">{{ __('Selamat Datang di SMILE') }}</h1>
            <p class="banner-sub">
                {{ __('Sistem Manajemen Inventaris Latee') }}<br>
                <span class="banner-sub-small">
                    {{ __('Sistem Informasi Manajemen Inventaris · Pondok Pesantren Annuqayah Latee') }}
                </span>
            </p>
        </div>

        <div class="banner-illustration">
            <img src="{{ asset('assets/svg/dashboard-svg.svg') }}" alt="{{ __('Dashboard Illustration') }}" />
        </div>

    </div>

    {{-- ══════════════════════════════════
         STAT CARDS
    ══════════════════════════════════ --}}
    <div class="stat-grid">

        {{-- Total Users --}}
        <div class="stat-card dash-animate d2">
            <div class="stat-icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">{{ __('Total Users') }}</div>
                <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
                <div class="stat-unit">{{ __('pengguna terdaftar') }}</div>
            </div>
        </div>

        {{-- Total Peminjam --}}
        <div class="stat-card dash-animate d3">
            <div class="stat-icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                    <path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">{{ __('Total Peminjam') }}</div>
                <div class="stat-value">{{ $totalPeminjam ?? 0 }}</div>
                <div class="stat-unit">{{ __('peminjam aktif') }}</div>
            </div>
        </div>

        {{-- Total Inventaris --}}
        <div class="stat-card dash-animate d4">
            <div class="stat-icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                    <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                    <line x1="12" y1="12" x2="12" y2="16"/>
                    <line x1="10" y1="14" x2="14" y2="14"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-label">{{ __('Total Inventaris') }}</div>
                <div class="stat-value">{{ $totalInventaris ?? 0 }}</div>
                <div class="stat-unit">{{ __('item inventaris') }}</div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════
         DATA TABLES
    ══════════════════════════════════ --}}
    <div class="tables-grid">

        {{-- Barang Terbaru --}}
        <div class="data-card dash-animate d5">
            <div class="data-card-header">
                <span class="data-card-title">{{ __('Barang Terbaru') }}</span>
                <a href="#" wire:navigate class="data-card-link">{{ __('Lihat Semua') }} →</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Nama Barang') }}</th>
                        <th>{{ __('Kategori') }}</th>
                        <th>{{ __('Jumlah') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangTerbaru ?? [] as $barang)
                        <tr>
                            <td>{{ $barang->nama_barang }}</td>
                            <td>{{ $barang->kategori?->nama_kategori ?? '-' }}</td>
                            <td>{{ $barang->jumlah }}</td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'tersedia'    => 'badge-green',
                                        'dipinjam'    => 'badge-yellow',
                                        'rusak'       => 'badge-red',
                                        'maintenance' => 'badge-blue',
                                    ];
                                    $badgeClass = $badgeMap[strtolower($barang->status ?? '')] ?? 'badge-green';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($barang->status ?? 'Tersedia') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 2rem; color: var(--text-hint); font-size: 12.5px;">
                                {{ __('Belum ada data barang.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Peminjaman Terbaru --}}
        <div class="data-card dash-animate d6">
            <div class="data-card-header">
                <span class="data-card-title">{{ __('Peminjaman Terbaru') }}</span>
                <a href="#" wire:navigate class="data-card-link">{{ __('Lihat Semua') }} →</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Nama Peminjam') }}</th>
                        <th>{{ __('Nama Barang') }}</th>
                        <th>{{ __('Tgl. Pinjam') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamanTerbaru ?? [] as $pinjam)
                        <tr>
                            <td>{{ $pinjam->user?->nama_lengkap ?? $pinjam->nama_peminjam }}</td>
                            <td>{{ $pinjam->barang?->nama_barang ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d M Y') }}</td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'dipinjam'     => 'badge-yellow',
                                        'dikembalikan' => 'badge-green',
                                        'terlambat'    => 'badge-red',
                                        'diproses'     => 'badge-blue',
                                    ];
                                    $badgeClass = $badgeMap[strtolower($pinjam->status ?? '')] ?? 'badge-blue';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($pinjam->status ?? 'Diproses') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 2rem; color: var(--text-hint); font-size: 12.5px;">
                                {{ __('Belum ada data peminjaman.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</x-layouts::app>
