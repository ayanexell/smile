<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="bg-stone-100 dark:bg-stone-950 text-stone-800 dark:text-stone-100">

<div class="app-shell">

    {{-- ═══════════════ SIDEBAR ═══════════════ --}}
    <aside class="app-sidebar" id="appSidebar">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5 flex-shrink-0 no-underline px-5 py-3 border-b border-stone-200 dark:border-stone-800">
            <div class="sidebar-logo-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="sidebar-logo-name">SMILE</div>
                <div class="sidebar-logo-sub">Sistem Manajemen Inventaris</div>
            </div>
        </a>

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            <div class="sidebar-group">
                <div class="sidebar-group-label">{{ __('Platform') }}</div>
                <a href="{{ route('dashboard') }}" wire:navigate
                   class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    {{ __('Dashboard') }}
                </a>
            </div>

            {{-- Slot untuk navigasi tambahan --}}
            @isset($navigation)
                {{ $navigation }}
            @endisset

        </nav>

    </aside>

    {{-- ═══════════════ MAIN ═══════════════ --}}
    <div class="app-main">

        {{-- Mobile header --}}
        <header class="mobile-header">
            <button class="header-icon-btn" onclick="toggleSidebar()" aria-label="Menu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="mobile-title">SMILE</span>
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </header>

        {{-- Desktop header --}}
        <header class="app-header">

            <div class="header-page-title">
                @isset($heading)
                    {{ $heading }}
                @else
                    {{ __('Dashboard') }}
                @endisset
            </div>

            <div class="header-actions">

                {{-- Dark mode toggle --}}
                <button class="header-icon-btn" onclick="toggleDark()" title="{{ __('Toggle dark mode') }}">
                    <svg id="iconMoon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    <svg id="iconSun" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 hidden">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                </button>

                {{-- Notifications --}}
                <button class="header-icon-btn" title="{{ __('Notifikasi') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                    <span class="notif-badge"></span>
                </button>

                <div class="header-divider"></div>

                {{-- User dropdown --}}
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->initials()" />
            </div>
        </header>

        {{-- Mobile overlay --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        {{-- Page content --}}
        <main>
            {{ $slot }}
        </main>

    </div>
</div>

@persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
@endpersist

@fluxScripts

<script>
    function toggleSidebar() {
        document.getElementById('appSidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    }

    function toggleDark() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        document.getElementById('iconMoon').classList.toggle('hidden', !isDark);
        document.getElementById('iconSun').classList.toggle('hidden', isDark);
    }

    // Inisialisasi tema
    (function () {
        const saved       = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark      = saved === 'dark' || (!saved && prefersDark);
        document.documentElement.classList.toggle('dark', isDark);
        document.getElementById('iconMoon').classList.toggle('hidden', !isDark);
        document.getElementById('iconSun').classList.toggle('hidden', isDark);
    })();
</script>

</body>
</html>
