<x-layouts::auth :title="__('Masuk — SMILE')">
    <div class="flex h-screen items-center justify-center bg-stone-100 dark:bg-stone-950 p-4 sm:p-6 lg:p-8">
        {{-- Card Utama --}}
        <div class="w-full max-w-5xl h-full max-h-[800px] overflow-hidden rounded-2xl shadow-2xl shadow-stone-400/20 dark:shadow-black/40
                    grid grid-cols-1 lg:grid-cols-2">

            {{-- ═══════════════════════════════ KOLOM KIRI — Branding ═══════════════════════════════ --}}
            <div class="hidden lg:flex flex-col relative overflow-hidden justify-between p-10 h-full
                        bg-stone-50 dark:bg-stone-900">

                {{-- Dot grid overlay --}}
                <div class="absolute inset-0 grid-pattern opacity-60 pointer-events-none"></div>

                {{-- Gradient orbs (disesuaikan untuk latar terang) --}}
                <div class="absolute top-1/4 -left-16 w-72 h-72 rounded-full
                            bg-sage-400 blur-[100px] opacity-10 animate-float pointer-events-none"></div>
                <div class="absolute bottom-1/3 right-0 w-64 h-64 rounded-full
                            bg-sage-300 blur-[80px] opacity-10 animate-float-slow pointer-events-none"></div>

                {{-- Logo --}}
                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sage-600 flex items-center justify-center shadow-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-display font-semibold text-stone-800 dark:text-stone-100 text-sm tracking-wide leading-tight">
                            SMILE
                        </p>
                        <p class="text-stone-400 dark:text-stone-500 text-[10px] font-mono tracking-widest uppercase">
                            Sistem Manajemen Inventaris Latee
                        </p>
                    </div>
                </div>

                {{-- Ilustrasi SVG Card dengan animasi --}}
                <div class="relative z-10 flex items-center justify-center animate-float">
                    <img src="{{ asset('assets/svg/login-svg.svg') }}" alt="Ringkasan Inventaris SMILE"
                         class="w-full max-w-sm drop-shadow-xl">
                </div>

                {{-- Footer --}}
                <div class="relative z-10">
                    <p class="text-[11px] font-mono text-stone-400 dark:text-stone-500 tracking-wider">
                        &copy; {{ date('Y') }} PP. Annuqayah Latee &middot; Guluk-Guluk, Sumenep
                    </p>
                </div>
            </div>

            {{-- ═══════════════════════════════ KOLOM KANAN — Form Login ═══════════════════════════════ --}}
            <div class="flex items-center justify-center h-full overflow-y-auto px-6 py-8 sm:px-10
                        bg-white dark:bg-stone-950
                        border-l border-stone-200 dark:border-stone-800">

                <div class="w-full max-w-sm">

                    {{-- Mobile logo (hanya tampil di layar kecil) --}}
                    <div class="flex items-center gap-3 mb-10 lg:hidden">
                        <div class="w-9 h-9 rounded-xl bg-sage-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-display font-semibold text-stone-900 dark:text-stone-50 text-sm">SMILE</p>
                            <p class="text-stone-400 text-[10px] font-mono tracking-widest uppercase">Inventaris Latee</p>
                        </div>
                    </div>

                    {{-- Heading --}}
                    <div class="mb-8">
                        <h2 class="font-display text-4xl font-bold text-stone-900 dark:text-stone-50 mb-2">
                            Login
                        </h2>
                        <p class="text-stone-500 dark:text-stone-400 text-sm">
                            Masuk untuk mengakses sistem inventaris
                        </p>
                    </div>

                    {{-- Session Status --}}
                    <x-auth-session-status
                        class="mb-5 text-sm text-sage-700 dark:text-sage-300
                               bg-sage-50 dark:bg-sage-900/30 rounded-xl px-4 py-3
                               border border-sage-200 dark:border-sage-800"
                        :status="session('status')" />

                    {{-- Form --}}
                    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div class="space-y-1.5">
                            <label for="email" class="block text-sm font-medium text-stone-700 dark:text-stone-300">
                                Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-stone-400 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                    autocomplete="email" placeholder="email@example.com"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm
                                           bg-stone-50 dark:bg-stone-800
                                           text-stone-800 dark:text-stone-100
                                           border border-stone-200 dark:border-stone-700
                                           placeholder-stone-400 dark:placeholder-stone-500
                                           focus:outline-none focus:ring-2 focus:ring-sage-500/40 focus:border-sage-500
                                           transition-all duration-200
                                           @error('email') border-rose-400 dark:border-rose-500 @enderror" />
                            </div>
                            @error('email')
                                <p class="text-xs text-rose-500 flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1.5" x-data="{ show: false }">
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-sm font-medium text-stone-700 dark:text-stone-300">
                                    Password
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" wire:navigate
                                        class="text-xs text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 transition-colors font-medium">
                                        Lupa Password?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-stone-400 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                                    autocomplete="current-password" placeholder="••••••••"
                                    class="w-full pl-10 pr-11 py-2.5 rounded-xl text-sm
                                           bg-stone-50 dark:bg-stone-800
                                           text-stone-800 dark:text-stone-100
                                           border border-stone-200 dark:border-stone-700
                                           placeholder-stone-400 dark:placeholder-stone-500
                                           focus:outline-none focus:ring-2 focus:ring-sage-500/40 focus:border-sage-500
                                           transition-all duration-200
                                           @error('password') border-rose-400 dark:border-rose-500 @enderror" />
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-stone-400 dark:text-stone-500 hover:text-stone-600 dark:hover:text-stone-300 transition-colors">
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-rose-500 flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center">
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} class="peer sr-only" />
                                    <div class="w-4 h-4 rounded border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-800 peer-checked:bg-sage-600 peer-checked:border-sage-600 group-hover:border-sage-400 transition-all duration-200 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-sm text-stone-600 dark:text-stone-400 select-none">Ingat Saya</span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-6 py-3
                                   bg-sage-600 dark:bg-sage-500
                                   hover:bg-sage-700 dark:hover:bg-sage-400
                                   text-white font-medium text-sm rounded-xl
                                   transition-all duration-200
                                   shadow-sm hover:shadow-md hover:shadow-sage-600/20
                                   hover:-translate-y-0.5 active:translate-y-0
                                   focus:outline-none focus:ring-2 focus:ring-sage-500 focus:ring-offset-2
                                   dark:focus:ring-offset-stone-950">
                            Masuk ke Sistem
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-stone-200 dark:border-stone-700"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-4 bg-white dark:bg-stone-950 text-xs text-stone-400 dark:text-stone-500 font-mono">
                                atau
                            </span>
                        </div>
                    </div>

                    {{-- Daftar --}}
                    <p class="text-center text-sm text-stone-500 dark:text-stone-400">
                        Belum punya akun?
                        <a href="{{ route('register') }}" wire:navigate
                            class="font-medium text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 transition-colors ml-1">
                            Daftar
                        </a>
                    </p>

                    <div class="mt-6 pt-5 border-t border-stone-100 dark:border-stone-800">
                        <a href="{{ url('/') }}"
                            class="flex items-center justify-center gap-1.5 text-xs text-stone-400 dark:text-stone-500 hover:text-stone-600 dark:hover:text-stone-400 transition-colors group">
                            <svg class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts::auth>
