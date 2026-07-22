{{-- ===================== NAVBAR ===================== --}}
<header
    class="fixed top-0 left-0 right-0 z-50 nav-glass bg-stone-50/80 dark:bg-stone-950/80 border-b border-stone-200/60 dark:border-stone-800/60 transition-colors duration-500">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <div class="flex items-center gap-3 animate-slide-right">
                <div class="w-9 h-9 rounded-lg bg-sage-600 dark:bg-sage-500 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="hidden sm:block">
                    <p
                        class="font-display font-semibold text-stone-900 dark:text-stone-50 leading-tight text-sm tracking-wide">
                        {{ __('SMILE') }}</p>
                    <p class="text-stone-500 dark:text-stone-400 text-[10px] tracking-widest">PP. Annuqayah Latee</p>
                </div>
            </div>

            {{-- Desktop Nav Links --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach ([['Home', '#home'], ['Fitur', '#fitur'], ['Tentang', '#tentang'], ['Kontak', '#kontak']] as $item)
                    <a href="{{ $item[1] }}"
                        class="px-4 py-2 text-sm font-medium text-stone-600 dark:text-stone-400 hover:text-sage-700 dark:hover:text-sage-300 hover:bg-sage-50 dark:hover:bg-sage-900/20 rounded-lg transition-all duration-200">
                        {{ $item[0] }}
                    </a>
                @endforeach
            </div>

            {{-- Right Controls --}}
            <div class="flex items-center gap-2">
                {{-- Notification Bell --}}
                <button
                    class="relative p-2 rounded-lg text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span
                        class="absolute top-1.5 right-1.5 w-2 h-2 bg-sage-500 rounded-full ring-2 ring-stone-50 dark:ring-stone-950 animate-pulse-soft"></span>
                </button>

                {{-- Dark Mode Toggle --}}
                <button x-data variant="segmented" x-model="$flux.appearance"
                    class="p-2 rounded-lg text-stone-500 dark:text-stone-400 cursor-pointer hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors"
                    :aria-label="darkMode ? 'Dark Mode' : 'Light Mode'" @click="darkMode = !darkMode">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                {{-- CTA Button --}}
                @guest()
                    <a href="{{ route('login') }}"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-sage-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Masuk
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-sage-600/20">
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
                    class="md:hidden p-2 rounded-lg text-stone-500 hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</header>
