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
            <a href="{{ route('dashboard') }}" wire:navigate
                class="flex items-center gap-2.5 flex-shrink-0 no-underline px-5 py-3 border-b border-stone-200 dark:border-stone-800">
                <div class="sidebar-logo-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
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
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        {{ __('Dashboard') }}
                    </a>
                </div>

                <div class="sidebar-group" x-data="{ open: {{ request()->routeIs(['admin.*', 'koordinator.*', 'user.*']) ? 'true' : 'false' }} }">
                    {{-- Label Grup Menu --}}
                    <div class="sidebar-group-label">{{ __('Administrator') }}</div>

                    {{-- User Navigasi --}}
                    <div>
                        {{-- Dropdown Trigger (User) --}}
                        <button @click="open = !open"
                            class="sidebar-item w-full flex justify-between items-center text-left cursor-pointer transition-colors text-xs py-1.5">
                            <div class="flex items-center gap-2.5">
                                {{-- Ikon Users --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                                </svg>

                                <span class="font-medium">{{ __('User') }}</span>
                            </div>

                            {{-- Ikon Chevron (Berputar otomatis menggunakan Alpine) --}}
                            <svg :class="open ? 'rotate-180' : ''"
                                class="w-3.5 h-3.5 transition-transform duration-200 text-stone-400 dark:text-stone-500"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Sub Navigasi Vertikal --}}
                        <div x-show="open" x-collapse class="flex flex-col pl-4 pr-1 mt-0.5 space-y-0.5"
                            style="display: none;">

                            {{-- Admin --}}
                            <a href="{{ route('admin.admins') }}" wire:navigate
                                class="sidebar-item text-[11px] py-1 pl-3 flex items-center gap-2 {{ request()->routeIs('admin.admins') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke="currentColor"
                                    class="w-3.5 h-3.5 flex-shrink-0 text-stone-400 dark:text-stone-500 group-[.active]:text-current">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span>{{ __('Admin') }}</span>
                            </a>

                            {{-- Koordinator --}}
                            <a href="{{ route('admin.koordinators') }}" wire:navigate
                                class="sidebar-item text-[11px] py-1 pl-3 flex items-center gap-2 {{ request()->routeIs('admin.koordinators') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke="currentColor"
                                    class="w-3.5 h-3.5 flex-shrink-0 text-stone-400 dark:text-stone-500 group-[.active]:text-current">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                                </svg>
                                <span>{{ __('Koordinator') }}</span>
                            </a>

                            {{-- User --}}
                            <a href="{{ route('admin.users') }}" wire:navigate
                                class="sidebar-item text-[11px] py-1 pl-3 flex items-center gap-2 {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke="currentColor"
                                    class="w-3.5 h-3.5 flex-shrink-0 text-stone-400 dark:text-stone-500 group-[.active]:text-current">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                <span>{{ __('User') }}</span>
                            </a>

                        </div>
                    </div>

                    {{-- Inventaris --}}
                    <a href="{{ route('admin.inventaris') }}" wire:navigate
                        class="sidebar-item w-full flex justify-between items-center text-left cursor-pointer transition-colors text-xs py-1.5">
                        <div class="flex items-center gap-2.5">
                            {{-- Ikon Inventaris --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M5.616 21q-.672 0-1.144-.472T4 19.385V8.263q-.43-.178-.715-.577Q3 7.286 3 6.769V4.615q0-.67.472-1.143Q3.944 3 4.616 3h14.769q.67 0 1.143.472q.472.472.472 1.144v2.153q0 .517-.285.916q-.284.4-.715.578v11.122q0 .67-.472 1.143q-.472.472-1.143.472zM5 8.385v10.904q0 .307.221.509T5.77 20h12.616q.269 0 .442-.173t.173-.442v-11zm-.385-1h14.77q.269 0 .442-.173T20 6.769V4.616q0-.27-.173-.443T19.384 4H4.616q-.27 0-.443.173T4 4.616v2.153q0 .27.173.442q.173.173.443.173m4.769 5.482h5.23V12h-5.23zM12 14.192" />
                            </svg>

                            <span class="font-medium">{{ __('Inventaris') }}</span>
                        </div>
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
            <header class="mobile-header ">
                <button class="header-icon-btn" onclick="toggleSidebar()" aria-label="Menu">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <line x1="3" y1="12" x2="21" y2="12" />
                        <line x1="3" y1="18" x2="21" y2="18" />
                    </svg>
                </button>
                <span class="mobile-title">SMILE</span>
                <flux:dropdown position="top" align="end">
                    <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                    <flux:menu>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer">
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
                        <svg id="iconMoon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                        <svg id="iconSun" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                            class="w-5 h-5 hidden">
                            <circle cx="12" cy="12" r="5" />
                            <line x1="12" y1="1" x2="12" y2="3" />
                            <line x1="12" y1="21" x2="12" y2="23" />
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                            <line x1="1" y1="12" x2="3" y2="12" />
                            <line x1="21" y1="12" x2="23" y2="12" />
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                        </svg>
                    </button>

                    {{-- Notifications --}}
                    <button class="header-icon-btn" title="{{ __('Notifikasi') }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 01-3.46 0" />
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
        (function() {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = saved === 'dark' || (!saved && prefersDark);
            document.documentElement.classList.toggle('dark', isDark);
            document.getElementById('iconMoon').classList.toggle('hidden', !isDark);
            document.getElementById('iconSun').classList.toggle('hidden', isDark);
        })();
    </script>

</body>

</html>
