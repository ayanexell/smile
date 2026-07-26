<x-layouts::auth :title="__('Masuk — SMILE')">
    <div class="flex h-screen items-center justify-center bg-stone-100 p-4 sm:p-6 lg:p-8 dark:bg-stone-950">
        {{-- Card Utama --}}
        <div
            class="max-h-200 grid h-full w-full max-w-5xl grid-cols-1 overflow-hidden rounded-2xl shadow-2xl shadow-stone-400/20 lg:grid-cols-2 dark:shadow-black/40">

            {{-- ═══════════════════════════════ KOLOM KIRI — Branding ═══════════════════════════════ --}}
            <div
                class="relative hidden h-full flex-col justify-between overflow-hidden bg-stone-50 p-10 lg:flex dark:bg-stone-900">

                {{-- Dot grid overlay --}}
                <div class="grid-pattern pointer-events-none absolute inset-0 opacity-60"></div>

                {{-- Gradient orbs (disesuaikan untuk latar terang) --}}
                <div
                    class="bg-sage-400 animate-float pointer-events-none absolute -left-16 top-1/4 h-72 w-72 rounded-full opacity-10 blur-[100px]">
                </div>
                <div
                    class="bg-sage-300 animate-float-slow pointer-events-none absolute bottom-1/3 right-0 h-64 w-64 rounded-full opacity-10 blur-[80px]">
                </div>

                {{-- Logo --}}
                <div class="relative z-10 flex items-center gap-3">
                    <div class="bg-sage-600 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl shadow-lg">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <p
                            class="font-display text-sm font-semibold leading-tight tracking-wide text-stone-800 dark:text-stone-100">
                            SMILE
                        </p>
                        <p class="font-mono text-[10px] uppercase tracking-widest text-stone-400 dark:text-stone-500">
                            Sistem Manajemen Inventaris Latee
                        </p>
                    </div>
                </div>

                {{-- Ilustrasi SVG Card dengan animasi --}}
                <div class="animate-float relative z-10 flex items-center justify-center">
                    <img src="{{ asset('assets/svg/login-svg.svg') }}" alt="Ringkasan Inventaris SMILE"
                        class="w-full max-w-sm drop-shadow-xl">
                </div>

                {{-- Footer --}}
                <div class="relative z-10">
                    <p class="font-mono text-[11px] tracking-wider text-stone-400 dark:text-stone-500">
                        &copy; {{ date('Y') }} PP. Annuqayah Latee &middot; Guluk-Guluk, Sumenep
                    </p>
                </div>
            </div>

            {{-- ═══════════════════════════════ KOLOM KANAN — Form Login ═══════════════════════════════ --}}
            <div
                class="flex h-full items-center justify-center overflow-y-auto border-l border-stone-200 bg-white px-6 py-8 sm:px-10 dark:border-stone-800 dark:bg-stone-950">

                <div class="w-full max-w-sm">

                    {{-- Mobile logo (hanya tampil di layar kecil) --}}
                    <div class="mb-10 flex items-center gap-3 lg:hidden">
                        <div class="bg-sage-600 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-display text-sm font-semibold text-stone-900 dark:text-stone-50">SMILE</p>
                            <p class="font-mono text-[10px] uppercase tracking-widest text-stone-400">Inventaris Latee
                            </p>
                        </div>
                    </div>

                    {{-- Heading --}}
                    <div class="mb-8">
                        <h2 class="font-display mb-2 text-4xl font-bold text-stone-900 dark:text-stone-50">
                            Login
                        </h2>
                        <p class="text-sm text-stone-500 dark:text-stone-400">
                            Masuk untuk mengakses sistem inventaris
                        </p>
                    </div>

                    {{-- Session Status --}}
                    <x-auth-session-status
                        class="text-sage-700 dark:text-sage-300 bg-sage-50 dark:bg-sage-900/30 border-sage-200 dark:border-sage-800 mb-5 rounded-xl border px-4 py-3 text-sm"
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
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                    <svg class="h-4 w-4 text-stone-400 dark:text-stone-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="email" placeholder="email@example.com"
                                    class="focus:ring-sage-500/40 focus:border-sage-500 @error('email') @enderror w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-10 pr-4 text-sm text-stone-800 placeholder-stone-400 transition-all duration-200 focus:outline-none focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500" />
                            </div>
                            @error('email')
                                <p class="mt-1 flex items-center gap-1 text-xs text-rose-500">
                                    <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1.5" x-data="{ show: false }">
                            <div class="flex items-center justify-between">
                                <label for="password"
                                    class="block text-sm font-medium text-stone-700 dark:text-stone-300">
                                    Password
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" wire:navigate
                                        class="text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 text-xs font-medium transition-colors">
                                        Lupa Password?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                    <svg class="h-4 w-4 text-stone-400 dark:text-stone-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                                    autocomplete="current-password" placeholder="••••••••"
                                    class="focus:ring-sage-500/40 focus:border-sage-500 @error('password') @enderror w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-10 pr-11 text-sm text-stone-800 placeholder-stone-400 transition-all duration-200 focus:outline-none focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500" />
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-stone-400 transition-colors hover:text-stone-600 dark:text-stone-500 dark:hover:text-stone-300">
                                    <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 flex items-center gap-1 text-xs text-rose-500">
                                    <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center">
                            <label class="group flex cursor-pointer items-center gap-2.5">
                                <div class="relative">
                                    <input type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }} class="peer sr-only" />
                                    <div
                                        class="peer-checked:bg-sage-600 peer-checked:border-sage-600 group-hover:border-sage-400 flex h-4 w-4 items-center justify-center rounded border border-stone-300 bg-white transition-all duration-200 dark:border-stone-600 dark:bg-stone-800">
                                        <svg class="h-2.5 w-2.5 scale-50 text-white opacity-0 transition-all duration-150 peer-checked:scale-100 peer-checked:opacity-100"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="select-none text-sm text-stone-600 dark:text-stone-400">Ingat Saya</span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 hover:shadow-sage-600/20 focus:ring-sage-500 flex w-full items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 active:translate-y-0 dark:focus:ring-offset-stone-950">
                            Masuk ke Sistem
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-stone-200 dark:border-stone-700"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span
                                class="bg-white px-4 font-mono text-xs text-stone-400 dark:bg-stone-950 dark:text-stone-500">
                                atau
                            </span>
                        </div>
                    </div>

                    {{-- Daftar --}}
                    <p class="text-center text-sm text-stone-500 dark:text-stone-400">
                        Belum punya akun?
                        <a href="{{ route('register') }}" wire:navigate
                            class="text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 ml-1 font-medium transition-colors">
                            Daftar
                        </a>
                    </p>

                    <div class="mt-6 border-t border-stone-100 pt-5 dark:border-stone-800">
                        <a href="{{ url('/') }}"
                            class="group flex items-center justify-center gap-1.5 text-xs text-stone-400 transition-colors hover:text-stone-600 dark:text-stone-500 dark:hover:text-stone-400">
                            <svg class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts::auth>
