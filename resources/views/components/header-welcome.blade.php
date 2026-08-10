{{-- ===================== NAVBAR ===================== --}}
<header
    class="nav-glass fixed left-0 right-0 top-0 z-50 border-b border-stone-200/60 bg-stone-50/80 transition-colors duration-500 dark:border-stone-800/60 dark:bg-stone-950/80">
    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            {{-- Logo --}}
            <div class="animate-slide-right flex items-center gap-3">
                <div class="dark:bg-sage-600 flex items-center justify-center rounded-lg">
                    <img src="{{ asset('assets/logo.webp') }}" alt="Logo" class="w-12" />
                </div>
                <div class="hidden sm:block">
                    <p
                        class="font-display text-md font-semibold leading-tight tracking-wide text-stone-900 dark:text-stone-50">
                        {{ __('SMILE') }}</p>
                    <p class="text-[10px] tracking-widest text-stone-500 dark:text-stone-400">PP. Annuqayah Latee</p>
                </div>
            </div>

            {{-- Desktop Nav Links --}}
            <div class="hidden items-center gap-1 md:flex">
                @foreach ([['Home', '#home'], ['Fitur', '#fitur'], ['Tentang', '#tentang'], ['Kontak', '#kontak']] as $item)
                    <a href="{{ $item[1] }}"
                        class="hover:text-sage-700 dark:hover:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-900/20 rounded-lg px-4 py-2 text-sm font-medium text-stone-600 transition-all duration-200 dark:text-stone-400">
                        {{ $item[0] }}
                    </a>
                @endforeach
            </div>

            {{-- Right Controls --}}
            <div class="flex items-center gap-2">
                {{-- Notification Bell --}}
                {{-- <button
                    class="relative rounded-lg p-2 text-stone-500 transition-colors hover:bg-stone-100 dark:text-stone-400 dark:hover:bg-stone-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span
                        class="bg-sage-500 animate-pulse-soft absolute right-1.5 top-1.5 h-2 w-2 rounded-full ring-2 ring-stone-50 dark:ring-stone-950"></span>
                </button> --}}

                {{-- Dark Mode Toggle --}}
                <button x-data variant="segmented" x-model="$flux.appearance"
                    class="cursor-pointer rounded-lg p-2 text-stone-500 transition-colors hover:bg-stone-100 dark:text-stone-400 dark:hover:bg-stone-800"
                    :aria-label="darkMode ? 'Dark Mode' : 'Light Mode'" @click="darkMode = !darkMode">
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                {{-- CTA Button --}}
                @guest()
                    <a href="{{ route('login') }}"
                        class="bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 hover:shadow-sage-600/20 hidden items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:shadow-md sm:inline-flex">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Masuk
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 hover:shadow-sage-600/20 hidden items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:shadow-md sm:inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Dashboard
                    </a>
                @endauth

                {{-- Mobile Menu Button --}}
                <button x-data="{ open: false }" @click="open = !open"
                    class="rounded-lg p-2 text-stone-500 transition-colors hover:bg-stone-100 md:hidden dark:hover:bg-stone-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</header>
