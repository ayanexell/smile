<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Inventaris;
use App\Models\LaporanInventaris;

new class extends Component {
    public $barangTerbaru;
    public $peminjamanTerbaru;
    public $totalUsers;
    public $totalPeminjaman;
    public $totalInventaris;
    public $totalLaporan;
    public $user;
    public $accPeminjaman;
    public $pendingPeminjaman;
    public $ditolakPeminjaman;
    public $terlambatPeminjaman;
    public $dikembalikanPeminjaman;
    public $peminjamanUser;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        if ($this->user->role->nama_role === 'Super Admin' || $this->user->role->nama_role === 'Admin') {
            $this->totalUsers = User::onlyUsers()->count();
            $this->totalPeminjaman = Peminjaman::count();
            $this->totalInventaris = Inventaris::count();
            $this->barangTerbaru = Inventaris::take(5)->latest()->get();
            $this->peminjamanTerbaru = Peminjaman::take(5)->latest()->get();
        } elseif ($this->user->role->nama_role === 'Koordinator') {
            $departemenId = Auth::user()->departemen->id_departemen;
            $this->totalPeminjaman = Peminjaman::query()
                ->whereHas('inventaris.user', function ($query) use ($departemenId) {
                    $query->where('departemen_id', $departemenId);
                })
                ->count();
            $this->totalInventaris = Inventaris::query()
                ->whereHas('user.departemen', function ($q) use ($departemenId) {
                    $q->where('departemen_id', $departemenId);
                })
                ->count();
            $this->barangTerbaru = $this->user->inventaris()->take(5)->latest()->get();
            $this->peminjamanTerbaru = Peminjaman::query()
                ->whereHas('inventaris.user', function ($query) use ($departemenId) {
                    $query->where('departemen_id', $departemenId);
                })
                ->latest()
                ->take(5)
                ->get();
            $this->totalLaporan = LaporanInventaris::query()
                ->whereHas('user.departemen', function ($q) use ($departemenId) {
                    $q->where('departemen_id', $departemenId);
                })
                ->count();
        } else {
            $this->accPeminjaman = Auth::user()->peminjamans()->where('status', 'dipinjam')->count();
            $this->pendingPeminjaman = Auth::user()->peminjamans()->where('status', 'menunggu')->count();
            $this->ditolakPeminjaman = Auth::user()->peminjamans()->where('status', 'ditolak')->count();
            $this->dikembalikanPeminjaman = Auth::user()->peminjamans()->where('status', 'dikembalikan')->count();
            $this->terlambatPeminjaman = Auth::user()->peminjamans()->where('lambat', 1)->count();
            $this->peminjamanUser = Auth::user()->peminjamans()->take(5)->latest()->get();
        }
        return $this->view([
            'totalUsers' => $this->totalUsers,
            'totalPeminjaman' => $this->totalPeminjaman,
            'totalInventaris' => $this->totalInventaris,
            'barangTerbaru' => $this->barangTerbaru,
            'peminjamanTerbaru' => $this->peminjamanTerbaru,
            'accPeminjaman' => $this->accPeminjaman,
            'pendingPeminjaman' => $this->pendingPeminjaman,
            'ditolakPeminjaman' => $this->ditolakPeminjaman,
            'dikembalikanPeminjaman' => $this->dikembalikanPeminjaman,
            'terlambatPeminjaman' => $this->terlambatPeminjaman,
            'peminjamanUser' => $this->peminjamanUser,
        ]);
    }
};
?>

<div>
    @can('isSuperAdminAndAdmin')
        <div>
            {{-- Welcome --}}
            <div class="welcome-banner dash-animate d1">

                {{-- Grid pattern dekoratif --}}
                <svg class="banner-grid" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="dash-grid" width="28" height="28" patternUnits="userSpaceOnUse">
                            <path d="M 28 0 L 0 0 0 28" fill="none" stroke="currentColor" stroke-width="0.5"
                                opacity="0.3" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#dash-grid)" />
                </svg>

                <div class="banner-glow"></div>

                <div class="banner-text">
                    <div class="banner-eyebrow">{{ __('Selamat datang ' . Auth::user()->nama_lengkap) }}</div>
                    <h1 class="banner-title">{{ __('Selamat Datang di Dashboard SMILE') }}</h1>
                    <p class="banner-sub">
                        <span class="banner-sub-small">
                            {{ __('Sistem Informasi Manajemen Inventaris · Pondok Pesantren Annuqayah Latee') }}
                        </span>
                    </p>
                </div>

                <div class="banner-illustration">
                    <img src="{{ asset('assets/svg/dashboard-svg.svg') }}" alt="{{ __('Dashboard Illustration') }}" />
                </div>

            </div>

            {{-- Stats Card --}}
            <div class="stat-grid">

                {{-- Total Users --}}
                <div class="stat-card dash-animate d2">
                    <div class="stat-icon-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
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
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 00-3-3.87" />
                            <path d="M16 3.13a4 4 0 010 7.75" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">{{ __('Total Peminjam') }}</div>
                        <div class="stat-value">{{ $totalPeminjaman ?? 0 }}</div>
                        <div class="stat-unit">{{ __('peminjam aktif') }}</div>
                    </div>
                </div>

                {{-- Total Inventaris --}}
                <div class="stat-card dash-animate d4">
                    <div class="stat-icon-wrap">
                        <svg width="800px" height="800px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M20 9c0 .55-.45 1-1 1h-2v2c0 .55-.45 1-1 1s-1-.45-1-1v-2h-2c-.55 0-1-.45-1-1s.45-1 1-1h2V6c0-.55.45-1 1-1s1 .45 1 1v2h2c.55 0 1 .45 1 1zM4 8h3V3H4v5zm-2 9h5v-7H2v7zm14-2c-.55 0-1 .45-1 1v1H9V6h3c.55 0 1-.45 1-1s-.45-1-1-1H9V2c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v6H1c-.55 0-1 .45-1 1v9c0 .55.45 1 1 1h15c.55 0 1-.45 1-1v-2c0-.55-.45-1-1-1z"
                                fill="currentColor" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">{{ __('Total Inventaris') }}</div>
                        <div class="stat-value">{{ $this->totalInventaris ?? 0 }}</div>
                        <div class="stat-unit">{{ __('item inventaris') }}</div>
                    </div>
                </div>

            </div>

            {{-- Data Tables --}}
            <div class="tables-grid">

                {{-- Barang Terbaru --}}
                <div class="data-card dash-animate d5">
                    <div class="data-card-header">
                        <span class="data-card-title">{{ __('Barang Terbaru') }}</span>
                        <a href="{{ route('admin.inventaris') }}" wire:navigate
                            class="data-card-link">{{ __('Lihat Semua') }}
                            →</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('Nama Barang') }}</th>
                                <th>{{ __('Tipe') }}</th>
                                <th>{{ __('Jumlah') }}</th>
                                <th>{{ __('Dibuat') }}</th>
                                {{-- <th>{{ __('Status') }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->barangTerbaru as $barang)
                                <tr>
                                    <td>{{ $barang->nama_barang }}</td>
                                    <td>{{ $barang->tipe ?? '-' }}</td>
                                    <td>{{ $barang->jumlah }}</td>
                                    <td>{{ $barang->created_at->diffForHumans() }}</td>
                                    {{-- <td>
                                        @php
                                            $badgeMap = [
                                                'baik' => 'badge-green',
                                                'rusak' => 'badge-red',
                                            ];
                                            $badgeClass =
                                                $badgeMap[strtolower($barang->kondisi ?? '')] ?? 'badge-green';
                                        @endphp
                                        <span
                                            class="badge {{ $badgeClass }} text-[9px]">{{ ucfirst($barang->kondisi ?? 'Tersedia') }}</span>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        style="text-align:center; padding: 2rem; color: var(--text-hint); font-size: 12.5px;">
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
                        <a href="{{ route('admin.peminjaman') }}" wire:navigate
                            class="data-card-link">{{ __('Lihat Semua') }} →</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('Nama Peminjam') }}</th>
                                <th>{{ __('Nama Barang') }}</th>
                                <th>{{ __('Tgl. Pinjam') }}</th>
                                <th>{{ __('Dibuat') }}</th>
                                {{-- <th>{{ __('Status') }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjamanTerbaru as $pinjam)
                                <tr>
                                    <td>{{ $pinjam->user->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $pinjam->inventaris->nama_barang ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d M Y') }}</td>
                                    <td>{{ $pinjam->created_at->diffForHumans() ?? '-' }}</td>
                                    {{-- <td>
                                        @php
                                            $badgeMap = [
                                                'dipinjam' => 'badge-yellow',
                                                'dikembalikan' => 'badge-green',
                                                'terlambat' => 'badge-red',
                                                'diproses' => 'badge-blue',
                                            ];
                                            $badgeClass = $badgeMap[strtolower($pinjam->status ?? '')] ?? 'badge-blue';
                                        @endphp
                                        <span
                                            class="badge {{ $badgeClass }} text-[9px]">{{ ucfirst($pinjam->status ?? 'Diproses') }}</span>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        style="text-align:center; padding: 2rem; color: var(--text-hint); font-size: 12.5px;">
                                        {{ __('Belum ada data peminjaman.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @endcan
    @can('isKoordinator')
        <div>
            {{-- Welcome --}}
            <div class="welcome-banner dash-animate d1">

                {{-- Grid pattern dekoratif --}}
                <svg class="banner-grid" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="dash-grid" width="28" height="28" patternUnits="userSpaceOnUse">
                            <path d="M 28 0 L 0 0 0 28" fill="none" stroke="currentColor" stroke-width="0.5"
                                opacity="0.3" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#dash-grid)" />
                </svg>

                <div class="banner-glow"></div>

                <div class="banner-text">
                    <div class="banner-eyebrow">
                        {{ __('Koordinator ' . Auth::user()->departemen->nama_departemen) }}</div>
                    <h1 class="banner-title">{{ __('Selamat Datang di Dashboard SMILE') }}</h1>
                    <p class="banner-sub">
                        <span class="banner-sub-small">
                            {{ __('Sistem Informasi Manajemen Inventaris · Pondok Pesantren Annuqayah Latee') }}
                        </span>
                    </p>
                </div>

                <div class="banner-illustration">
                    <img src="{{ asset('assets/svg/dashboard-svg.svg') }}" alt="{{ __('Dashboard Illustration') }}" />
                </div>

            </div>

            {{-- Stats Card --}}
            <div class="stat-grid">
                {{-- Total Inventaris --}}
                <div class="stat-card dash-animate d4">
                    <div class="stat-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M5.616 21q-.672 0-1.144-.472T4 19.385V8.263q-.43-.178-.715-.577Q3 7.286 3 6.769V4.615q0-.67.472-1.143Q3.944 3 4.616 3h14.769q.67 0 1.143.472q.472.472.472 1.144v2.153q0 .517-.285.916q-.284.4-.715.578v11.122q0 .67-.472 1.143q-.472.472-1.143.472zM5 8.385v10.904q0 .307.221.509T5.77 20h12.616q.269 0 .442-.173t.173-.442v-11zm-.385-1h14.77q.269 0 .442-.173T20 6.769V4.616q0-.27-.173-.443T19.384 4H4.616q-.27 0-.443.１７３T４ ４．６１６v２．１５３q０．２７．１７３．４４２q．１７３．１７３．４４３．１７３m４．７６９ ５．４８２h５．２３V１２h-５．２３zM１２ １４．１９２" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">{{ __('Total Inventaris') }}</div>
                        <div class="stat-value">{{ $this->totalInventaris ?? 0 }}</div>
                        <div class="stat-unit">{{ __('item inventaris') }}</div>
                    </div>
                </div>

                {{-- Total Peminjam --}}
                <div class="stat-card dash-animate d3">
                    <div class="stat-icon-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 00-3-3.87" />
                            <path d="M16 3.13a4 4 0 010 7.75" />
                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">{{ __('Total Peminjam') }}</div>
                        <div class="stat-value">{{ $totalPeminjaman ?? 0 }}</div>
                        <div class="stat-unit">{{ __('peminjam aktif') }}</div>
                    </div>
                </div>

                {{-- Total Laporan --}}
                <div class="stat-card dash-animate d2">
                    <div class="stat-icon-wrap">
                        <svg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">

                            <defs>

                                <style>
                                    .cls-1 {
                                        fill: #0f773d;
                                    }
                                </style>

                            </defs>

                            <title />

                            <g id="xxx-word">

                                <path class="cls-1"
                                    d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,1,1,10,0V95h70a5,5,0,0,1,0,10Z" />

                                <path class="cls-1"
                                    d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z" />

                                <path class="cls-1"
                                    d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z" />

                                <path class="cls-1" d="M275,280H125a5,5,0,1,1,0-10H275a5,5,0,0,1,0,10Z" />

                                <path class="cls-1" d="M200,330H125a5,5,0,1,1,0-10h75a5,5,0,0,1,0,10Z" />

                                <path class="cls-1"
                                    d="M325,280H75a30,30,0,0,1-30-30V173.17a30,30,0,0,1,30-30h.2l250,1.66a30.09,30.09,0,0,1,29.81,30V250A30,30,0,0,1,325,280ZM75,153.17a20,20,0,0,0-20,20V250a20,20,0,0,0,20,20H325a20,20,0,0,0,20-20V174.83a20.06,20.06,0,0,0-19.88-20l-250-1.66Z" />

                                <path class="cls-1"
                                    d="M152.44,236H117.79V182.68h34.3v7.93H127.4v14.45h19.84v7.73H127.4v14.92h25Z" />

                                <path class="cls-1"
                                    d="M190.18,236H180l-8.36-14.37L162.52,236h-7.66L168,215.69l-11.37-19.14h10.2l6.48,11.6,7.38-11.6h7.46L177,213.66Z" />

                                <path class="cls-1"
                                    d="M217.4,221.51l7.66.78q-1.49,7.42-5.74,11A15.5,15.5,0,0,1,209,236.82q-8.17,0-12.56-6a23.89,23.89,0,0,1-4.39-14.59q0-8.91,4.8-14.73a15.77,15.77,0,0,1,12.81-5.82q12.89,0,15.35,13.59l-7.66,1.05q-1-7.34-7.23-7.34a6.9,6.9,0,0,0-6.58,4,20.66,20.66,0,0,0-2.05,9.59q0,6,2.13,9.22a6.74,6.74,0,0,0,6,3.24Q215.49,229,217.4,221.51Z" />

                                <path class="cls-1"
                                    d="M257,223.42l8,1.09a16.84,16.84,0,0,1-6.09,8.83,18.13,18.13,0,0,1-11.37,3.48q-8.2,0-13.2-5.51t-5-14.92q0-8.94,5-14.8t13.67-5.86q8.44,0,13,5.78t4.61,14.84l0,1H238.61a22.12,22.12,0,0,0,.76,6.45,8.68,8.68,0,0,0,3,4.22,8.83,8.83,0,0,0,5.66,1.8Q254.67,229.83,257,223.42Zm-.55-11.8a9.92,9.92,0,0,0-2.56-7,8.63,8.63,0,0,0-12.36-.18,11.36,11.36,0,0,0-2.89,7.13Z" />

                                <path class="cls-1" d="M282.71,236h-8.91V182.68h8.91Z" />

                            </g>

                        </svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">{{ __('Total Laporan') }}</div>
                        <div class="stat-value">{{ $totalLaporan ?? 0 }}</div>
                        <div class="stat-unit">{{ __('laporan dibuat') }}</div>
                    </div>
                </div>
            </div>

            {{-- Data Tables --}}
            <div class="tables-grid">

                {{-- Barang Terbaru --}}
                <div class="data-card dash-animate d5">
                    <div class="data-card-header">
                        <span class="data-card-title">{{ __('Barang Terbaru') }}</span>
                        <a href="{{ route('admin.inventaris') }}" wire:navigate
                            class="data-card-link">{{ __('Lihat Semua') }}
                            →</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('Nama Barang') }}</th>
                                <th>{{ __('Tipe') }}</th>
                                <th>{{ __('Jumlah') }}</th>
                                <th>{{ __('Dibuat') }}</th>
                                {{-- <th>{{ __('Status') }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->barangTerbaru as $barang)
                                <tr>
                                    <td>{{ $barang->nama_barang }}</td>
                                    <td>{{ $barang->tipe ?? '-' }}</td>
                                    <td>{{ $barang->jumlah }}</td>
                                    <td>{{ $barang->created_at->diffForHumans() }}</td>
                                    {{-- <td>
                                        @php
                                            $badgeMap = [
                                                'baik' => 'badge-green',
                                                'rusak' => 'badge-red',
                                            ];
                                            $badgeClass =
                                                $badgeMap[strtolower($barang->kondisi ?? '')] ?? 'badge-green';
                                        @endphp
                                        <span
                                            class="badge {{ $badgeClass }} text-[9px]">{{ ucfirst($barang->kondisi ?? 'Tersedia') }}</span>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        style="text-align:center; padding: 2rem; color: var(--text-hint); font-size: 12.5px;">
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
                        <a href="{{ route('koordinator.peminjaman') }}" wire:navigate
                            class="data-card-link">{{ __('Lihat Semua') }} →</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('Nama Peminjam') }}</th>
                                <th>{{ __('Nama Barang') }}</th>
                                <th>{{ __('Tgl. Pinjam') }}</th>
                                <th>{{ __('Dibuat') }}</th>
                                {{-- <th>{{ __('Status') }}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjamanTerbaru as $pinjam)
                                <tr>
                                    <td>{{ $pinjam->user->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $pinjam->inventaris->nama_barang ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d M Y') }}</td>
                                    <td>{{ $pinjam->created_at->diffForHumans() }}</td>
                                    {{-- <td>
                                        @php
                                            $badgeMap = [
                                                'dipinjam' => 'badge-yellow',
                                                'dikembalikan' => 'badge-green',
                                                'terlambat' => 'badge-red',
                                                'diproses' => 'badge-blue',
                                            ];
                                            $badgeClass = $badgeMap[strtolower($pinjam->status ?? '')] ?? 'badge-blue';
                                        @endphp
                                        <span
                                            class="badge {{ $badgeClass }} text-[9px]">{{ ucfirst($pinjam->status ?? 'Diproses') }}</span>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        style="text-align:center; padding: 2rem; color: var(--text-hint); font-size: 12.5px;">
                                        {{ __('Belum ada data peminjaman.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @endcan
    @can('isUser')
        {{-- Welcome --}}
        <div class="welcome-banner dash-animate d1">

            {{-- Grid pattern dekoratif --}}
            <svg class="banner-grid" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="dash-grid" width="28" height="28" patternUnits="userSpaceOnUse">
                        <path d="M 28 0 L 0 0 0 28" fill="none" stroke="currentColor" stroke-width="0.5"
                            opacity="0.3" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#dash-grid)" />
            </svg>

            <div class="banner-glow"></div>

            <div class="banner-text">
                <div class="banner-eyebrow">{{ __('Selamat datang ' . Auth::user()->nama_lengkap) }}</div>
                <h1 class="banner-title">{{ __('Selamat Datang di Dashboard SMILE') }}</h1>
                <p class="banner-sub">
                    <span class="banner-sub-small">
                        {{ __('Sistem Informasi Manajemen Inventaris · Pondok Pesantren Annuqayah Latee') }}
                    </span>
                </p>
            </div>

            <div class="banner-illustration">
                <img src="{{ asset('assets/svg/dashboard-svg.svg') }}" alt="{{ __('Dashboard Illustration') }}" />
            </div>

        </div>

        {{-- Stats Card --}}
        <div class="stat-grid">

            {{-- Accepted --}}
            <div class="stat-card dash-animate d2">
                <div class="stat-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">{{ __('Diterima') }}</div>
                    <div class="stat-value">{{ $accPeminjaman ?? 0 }}</div>
                    <div class="stat-unit">{{ __('item inventaris') }}</div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="stat-card dash-animate d3">
                <div class="stat-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" />
                        <path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">{{ __('Menunggu') }}</div>
                    <div class="stat-value">{{ $pendingPeminjaman ?? 0 }}</div>
                    <div class="stat-unit">{{ __('peminjam aktif') }}</div>
                </div>
            </div>

            {{-- Ditolak --}}
            <div class="stat-card dash-animate d4">
                <div class="stat-icon-wrap">
                    <svg width="800px" height="800px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M20 9c0 .55-.45 1-1 1h-2v2c0 .55-.45 1-1 1s-1-.45-1-1v-2h-2c-.55 0-1-.45-1-1s.45-1 1-1h2V6c0-.55.45-1 1-1s1 .45 1 1v2h2c.55 0 1 .45 1 1zM4 8h3V3H4v5zm-2 9h5v-7H2v7zm14-2c-.55 0-1 .45-1 1v1H9V6h3c.55 0 1-.45 1-1s-.45-1-1-1H9V2c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v6H1c-.55 0-1 .45-1 1v9c0 .55.45 1 1 1h15c.55 0 1-.45 1-1v-2c0-.55-.45-1-1-1z"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">{{ __('Ditolak') }}</div>
                    <div class="stat-value">{{ $this->ditolakPeminjaman ?? 0 }}</div>
                    <div class="stat-unit">{{ __('item inventaris') }}</div>
                </div>
            </div>

            {{-- Dikembalikan --}}
            <div class="stat-card dash-animate d4">
                <div class="stat-icon-wrap">
                    <svg width="800px" height="800px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M20 9c0 .55-.45 1-1 1h-2v2c0 .55-.45 1-1 1s-1-.45-1-1v-2h-2c-.55 0-1-.45-1-1s.45-1 1-1h2V6c0-.55.45-1 1-1s1 .45 1 1v2h2c.55 0 1 .45 1 1zM4 8h3V3H4v5zm-2 9h5v-7H2v7zm14-2c-.55 0-1 .45-1 1v1H9V6h3c.55 0 1-.45 1-1s-.45-1-1-1H9V2c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v6H1c-.55 0-1 .45-1 1v9c0 .55.45 1 1 1h15c.55 0 1-.45 1-1v-2c0-.55-.45-1-1-1z"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">{{ __('Dikembalikan') }}</div>
                    <div class="stat-value">{{ $this->dikembalikanPeminjaman ?? 0 }}</div>
                    <div class="stat-unit">{{ __('item inventaris') }}</div>
                </div>
            </div>

            {{-- Terlambat --}}
            <div class="stat-card dash-animate d4">
                <div class="stat-icon-wrap">
                    <svg width="800px" height="800px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M20 9c0 .55-.45 1-1 1h-2v2c0 .55-.45 1-1 1s-1-.45-1-1v-2h-2c-.55 0-1-.45-1-1s.45-1 1-1h2V6c0-.55.45-1 1-1s1 .45 1 1v2h2c.55 0 1 .45 1 1zM4 8h3V3H4v5zm-2 9h5v-7H2v7zm14-2c-.55 0-1 .45-1 1v1H9V6h3c.55 0 1-.45 1-1s-.45-1-1-1H9V2c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v6H1c-.55 0-1 .45-1 1v9c0 .55.45 1 1 1h15c.55 0 1-.45 1-1v-2c0-.55-.45-1-1-1z"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">{{ __('Terlambat') }}</div>
                    <div class="stat-value">{{ $this->terlambatPeminjaman ?? 0 }}</div>
                    <div class="stat-unit">{{ __('item inventaris') }}</div>
                </div>
            </div>
        </div>
        {{-- Peminjaman Terbaru --}}
        <div class="data-card dash-animate d6">
            <div class="data-card-header">
                <span class="data-card-title">{{ __('Peminjaman Terbaru') }}</span>
                <a href="{{ route('user.peminjaman') }}" wire:navigate class="data-card-link">{{ __('Lihat Semua') }}
                    →</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Nama Barang') }}</th>
                        <th>{{ __('Tgl. Pinjam') }}</th>
                        <th>{{ __('Tgl. Kembali') }}</th>
                        <th>{{ __('Jumlah') }}</th>
                        <th>{{ __('Dibuat') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamanUser as $pinjam)
                        <tr>
                            {{-- Gambar + Nama Barang + Departemen --}}
                            <td class="px-2.5 py-1.5">
                                <div class="max-w-45 flex items-center gap-2 sm:max-w-xs">
                                    {{-- Thumbnail gambar atau placeholder --}}
                                    @if ($pinjam->inventaris->img_path && Storage::exists($pinjam->inventaris->img_path))
                                        <img src="{{ Storage::url($pinjam->inventaris->img_path) }}"
                                            alt="{{ $pinjam->inventaris->nama_barang }}"
                                            class="h-7 w-7 shrink-0 rounded-lg border border-stone-200 object-cover dark:border-stone-700" />
                                    @else
                                        <div
                                            class="bg-sage-100 dark:bg-sage-900/40 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-stone-200 dark:border-stone-700">
                                            <svg class="text-sage-500 dark:text-sage-400 h-3.5 w-3.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="truncate">
                                        <div class="truncate font-medium text-stone-800 dark:text-stone-200">
                                            {{ $pinjam->inventaris->nama_barang }}
                                        </div>
                                        <div class="truncate text-[10px] text-stone-400 dark:text-stone-500">
                                            {{ $pinjam->inventaris->user->departemen->nama_departemen ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d M Y') ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($pinjam->tgl_kembali)->format('d M Y') ?? '-' }}</td>
                            <td>{{ $pinjam->jumlah ?? '-' }}</td>
                            <td>{{ $pinjam->created_at->diffForHumans() ?? '-' }}</td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'dipinjam' => 'badge-yellow',
                                        'dikembalikan' => 'badge-green',
                                        'terlambat' => 'badge-red',
                                        'diproses' => 'badge-blue',
                                    ];
                                    $badgeClass = $badgeMap[strtolower($pinjam->status ?? '')] ?? 'badge-blue';
                                @endphp
                                <span
                                    class="badge {{ $badgeClass }} text-[9px]">{{ ucfirst($pinjam->status ?? 'Diproses') }}</span>
                            </td>
                        </tr>
                    @empty
                        {{-- <tr>
                            <td colspan="6"
                                style="text-align:center; padding: 2rem; color: var(--text-hint); font-size: 12.5px;">
                                {{ __('Belum ada data peminjaman.') }}
                            </td>
                        </tr> --}}
                        <tr>
                            <td colspan="6" class="px-2.5 py-10 text-center">
                                <div class="flex flex-col items-center gap-2 text-stone-400">
                                    <svg class="h-8 w-8 text-stone-300 dark:text-stone-700" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-xs font-medium">Tidak ada barang ditemukan</p>
                                    <p class="text-[10px] text-stone-300 dark:text-stone-600">
                                        Kunjungi Halaman Inventaris untuk menambah peminjaman
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endcan
</div>
