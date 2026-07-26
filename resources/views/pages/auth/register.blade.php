<x-layouts::auth :title="__('Daftar — SMILE')">
    <div class="flex min-h-screen items-center justify-center bg-stone-100 py-6 dark:bg-stone-950">
        <div
            class="flex w-full max-w-5xl overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xl dark:border-stone-800 dark:bg-stone-900">
            {{-- ═══════════════ KOLOM KIRI — Branding ═══════════════ --}}
            <div
                class="w-md relative hidden shrink-0 flex-col justify-between overflow-hidden border-l border-stone-200 bg-stone-50 px-8 py-10 lg:flex dark:border-stone-800 dark:bg-stone-950">

                <div class="grid-pattern pointer-events-none absolute inset-0 opacity-50"></div>

                <div class="relative z-10 flex items-center gap-3">
                    <div
                        class="bg-sage-600 dark:bg-sage-500 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl shadow-md">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-display text-lg text-stone-800 dark:text-stone-100">SMILE</div>
                        <div class="font-mono text-[9px] uppercase tracking-widest text-stone-500 dark:text-stone-500">
                            Sistem Manajemen Inventaris Latee</div>
                    </div>
                </div>

                <div class="relative z-10">
                    <p class="font-display mb-3 text-2xl italic leading-snug text-stone-800 dark:text-stone-100">
                        Kelola inventaris<br />dengan <span
                            class="text-sage-600 dark:text-sage-400 not-italic">cerdas</span><br />dan efisien.
                    </p>
                    <p class="text-sm leading-relaxed text-stone-500 dark:text-stone-400">
                        Platform manajemen inventaris terpadu untuk Pondok Pesantren Annuqayah Latee.
                    </p>
                </div>

                <div class="relative z-10 flex justify-center">
                    <img src="{{ asset('assets/svg/register-svg.svg') }}" alt="Ilustrasi Registrasi"
                        class="w-40 opacity-85 drop-shadow-md" />
                </div>

                <div class="relative z-10 flex flex-col gap-2">
                    @foreach (['Manajemen stok real-time', 'Laporan otomatis & akurat', 'Akses multi-pengguna aman'] as $item)
                        <div
                            class="flex items-center gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs text-stone-500 dark:border-stone-800 dark:bg-stone-900 dark:text-stone-400">
                            <span class="bg-sage-600 dark:bg-sage-400 h-1.5 w-1.5 shrink-0 rounded-full"></span>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>

                <p class="relative z-10 font-mono text-[10px] tracking-wide text-stone-400 dark:text-stone-600">
                    &copy; {{ date('Y') }} PP. Annuqayah Latee &middot; Guluk-Guluk, Sumenep
                </p>

            </div>


            {{-- ═══════════════ KOLOM KANAN — Form ═══════════════ --}}
            <div class="flex items-start justify-center overflow-y-auto bg-white px-6 py-8 sm:px-10 dark:bg-stone-950">
                <div class="w-full">

                    {{-- Mobile logo --}}
                    <div class="mb-7 flex items-center gap-3 lg:hidden">
                        <div class="bg-sage-600 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-display text-sm font-semibold text-stone-900 dark:text-stone-50">SMILE</p>
                            <p class="font-mono text-[9px] uppercase tracking-widest text-stone-400">Inventaris Latee
                            </p>
                        </div>
                    </div>

                    {{-- Heading --}}
                    <div class="mb-6">
                        <h2 class="font-display mb-1 text-3xl font-bold text-stone-900 dark:text-stone-50">
                            Buat Akun
                        </h2>
                        <p class="text-xs text-stone-500 dark:text-stone-400">
                            Isi data di bawah untuk membuat akun baru
                        </p>
                    </div>

                    <x-auth-session-status
                        class="text-sage-700 dark:text-sage-300 bg-sage-50 dark:bg-sage-900/20 border-sage-200 dark:border-sage-800 mb-4 rounded-xl border px-3 py-2.5 text-xs"
                        :status="session('status')" />

                    <form method="POST" action="{{ route('register.store') }}" class="space-y-3">
                        @csrf

                        {{-- ── Nama Lengkap ── --}}
                        <div class="space-y-1">
                            <label for="nama_lengkap"
                                class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                Nama Lengkap
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input id="nama_lengkap" name="nama_lengkap" type="text"
                                    value="{{ old('nama_lengkap') }}" required autofocus autocomplete="name"
                                    placeholder="Nama lengkap sesuai KTP"
                                    class="focus:ring-sage-500/50 focus:border-sage-500 @error('nama_lengkap') border-rose-400 @enderror w-full rounded-lg border bg-white py-2 pl-9 pr-3 text-xs text-stone-800 placeholder-stone-400 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder-stone-600" />
                            </div>
                            @error('nama_lengkap')
                                <p class="flex items-center gap-1 text-[10px] text-rose-500">
                                    <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── NIK ── --}}
                        <div class="space-y-1">
                            <label for="nik" class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                NIK
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <input id="nik" name="nik" type="text" inputmode="numeric" maxlength="16"
                                    minlength="16" value="{{ old('nik') }}" required autocomplete="off"
                                    placeholder="16 digit NIK"
                                    class="focus:ring-sage-500/50 focus:border-sage-500 @error('nik') border-rose-400 @enderror w-full rounded-lg border bg-white py-2 pl-9 pr-3 font-mono text-xs text-stone-800 placeholder-stone-400 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder-stone-600" />
                            </div>
                            @error('nik')
                                <p class="flex items-center gap-1 text-[10px] text-rose-500">
                                    <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── Tgl Lahir + Jenis Kelamin ── --}}
                        <div class="grid grid-cols-2 gap-3">

                            {{-- Tanggal Lahir --}}
                            <div class="space-y-1">
                                <label for="tgl_lahir"
                                    class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                    Tanggal Lahir
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input id="tgl_lahir" name="tgl_lahir" type="date"
                                        value="{{ old('tgl_lahir') }}" required autocomplete="bday"
                                        max="{{ now()->subYears(17)->format('Y-m-d') }}"
                                        class="focus:ring-sage-500/50 focus:border-sage-500 @error('tgl_lahir') border-rose-400 @enderror w-full rounded-lg border bg-white py-2 pl-9 pr-2 text-xs text-stone-800 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100" />
                                </div>
                                @error('tgl_lahir')
                                    <p class="text-[10px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div class="space-y-1">
                                <span class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                    Jenis Kelamin
                                </span>
                                <div class="h-8.5 flex gap-1.5">
                                    <label
                                        class="has-checked:border-sage-500 has-checked:bg-sage-50 has-checked:text-sage-700 dark:has-checked:border-sage-500 dark:has-checked:bg-sage-950/60 dark:has-checked:text-sage-300 flex flex-1 cursor-pointer items-center justify-center rounded-lg border border-stone-200 text-[11px] text-stone-500 transition-all duration-150 dark:border-stone-700 dark:text-stone-400">
                                        <input type="radio" name="jenis_kelamin" value="laki-laki"
                                            @checked(old('jenis_kelamin') == 'laki-laki') required class="sr-only" />
                                        L
                                    </label>
                                    <label
                                        class="has-checked:border-sage-500 has-checked:bg-sage-50 has-checked:text-sage-700 dark:has-checked:border-sage-500 dark:has-checked:bg-sage-950/60 dark:has-checked:text-sage-300 flex flex-1 cursor-pointer items-center justify-center rounded-lg border border-stone-200 text-[11px] text-stone-500 transition-all duration-150 dark:border-stone-700 dark:text-stone-400">
                                        <input type="radio" name="jenis_kelamin" value="perempuan"
                                            @checked(old('jenis_kelamin') == 'perempuan') class="sr-only" />
                                        P
                                    </label>
                                </div>
                                @error('jenis_kelamin')
                                    <p class="text-[10px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        {{-- ── Email ── --}}
                        <div class="space-y-1">
                            <label for="email"
                                class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                Email
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" value="{{ old('email') }}"
                                    required autocomplete="email" placeholder="email@example.com"
                                    class="focus:ring-sage-500/50 focus:border-sage-500 @error('email') border-rose-400 @enderror w-full rounded-lg border bg-white py-2 pl-9 pr-3 text-xs text-stone-800 placeholder-stone-400 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder-stone-600" />
                            </div>
                            @error('email')
                                <p class="flex items-center gap-1 text-[10px] text-rose-500">
                                    <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── No. WhatsApp ── --}}
                        <div class="space-y-1">
                            <label for="no_wa"
                                class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                No. WhatsApp
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <input id="no_wa" name="no_wa" type="tel" value="{{ old('no_wa') }}"
                                    required autocomplete="tel" placeholder="08xxxxxxxxxx"
                                    class="focus:ring-sage-500/50 focus:border-sage-500 @error('no_wa') border-rose-400 @enderror w-full rounded-lg border bg-white py-2 pl-9 pr-3 font-mono text-xs text-stone-800 placeholder-stone-400 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder-stone-600" />
                            </div>
                            @error('no_wa')
                                <p class="flex items-center gap-1 text-[10px] text-rose-500">
                                    <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── Alamat ── --}}
                        <div class="space-y-1">
                            <label for="alamat"
                                class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                Alamat
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute left-3 top-2.5">
                                    <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <textarea id="alamat" name="alamat" rows="2" required autocomplete="street-address"
                                    placeholder="Alamat lengkap"
                                    class="focus:ring-sage-500/50 focus:border-sage-500 @error('alamat') border-rose-400 @enderror w-full resize-none rounded-lg border bg-white py-2 pl-9 pr-3 text-xs text-stone-800 placeholder-stone-400 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder-stone-600">{{ old('alamat') }}</textarea>
                            </div>
                            @error('alamat')
                                <p class="flex items-center gap-1 text-[10px] text-rose-500">
                                    <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── Pekerjaan ── --}}
                        <div class="space-y-1">
                            <label for="pekerjaan"
                                class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                Pekerjaan
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input id="pekerjaan" name="pekerjaan" type="text"
                                    value="{{ old('pekerjaan') }}" required autocomplete="organization-title"
                                    placeholder="Pekerjaan"
                                    class="focus:ring-sage-500/50 focus:border-sage-500 @error('pekerjaan') border-rose-400 @enderror w-full rounded-lg border bg-white py-2 pl-9 pr-3 text-xs text-stone-800 placeholder-stone-400 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder-stone-600" />
                            </div>
                            @error('pekerjaan')
                                <p class="flex items-center gap-1 text-[10px] text-rose-500">
                                    <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── Password + Konfirmasi ── --}}
                        <div class="grid grid-cols-2 gap-3" x-data="{ showP: false, showC: false }">

                            {{-- Password --}}
                            <div class="space-y-1">
                                <label for="password"
                                    class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                    Password
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input id="password" name="password" :type="showP ? 'text' : 'password'" required
                                        autocomplete="new-password" placeholder="••••••••"
                                        class="focus:ring-sage-500/50 focus:border-sage-500 @error('password') border-rose-400 @enderror w-full rounded-lg border bg-white py-2 pl-9 pr-7 text-xs text-stone-800 placeholder-stone-400 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder-stone-600" />
                                    <button type="button" @click="showP = !showP"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-stone-400 transition-colors hover:text-stone-600 dark:hover:text-stone-300">
                                        <svg x-show="!showP" class="h-3.5 w-3.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showP" class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-[10px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="space-y-1">
                                <label for="password_confirmation"
                                    class="block text-xs font-medium text-stone-600 dark:text-stone-400">
                                    Konfirmasi
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-3.5 w-3.5 text-stone-400 dark:text-stone-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <input id="password_confirmation" name="password_confirmation"
                                        :type="showC ? 'text' : 'password'" required autocomplete="new-password"
                                        placeholder="••••••••"
                                        class="focus:ring-sage-500/50 focus:border-sage-500 w-full rounded-lg border border-stone-200 bg-white py-2 pl-9 pr-7 text-xs text-stone-800 placeholder-stone-400 transition-all focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder-stone-600" />
                                    <button type="button" @click="showC = !showC"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-stone-400 transition-colors hover:text-stone-600 dark:hover:text-stone-300">
                                        <svg x-show="!showC" class="h-3.5 w-3.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showC" class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        </div>

                        {{-- ── Submit ── --}}
                        <button type="submit"
                            class="bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 hover:shadow-sage-600/20 focus:ring-sage-500 mt-1 flex w-full items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-xs font-medium text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 active:translate-y-0 dark:focus:ring-offset-stone-950">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Buat Akun
                        </button>

                    </form>

                    {{-- Login link --}}
                    <p class="mt-5 text-center text-xs text-stone-500 dark:text-stone-400">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" wire:navigate
                            class="text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 ml-1 font-medium transition-colors">
                            Masuk
                        </a>
                    </p>

                    {{-- Back to home --}}
                    <div class="mt-4 border-t border-stone-100 pt-4 dark:border-stone-800/60">
                        <a href="{{ url('/') }}"
                            class="group flex items-center justify-center gap-1.5 text-[10px] text-stone-400 transition-colors hover:text-stone-600 dark:text-stone-600 dark:hover:text-stone-400">
                            <svg class="h-3 w-3 transition-transform group-hover:-translate-x-0.5" fill="none"
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
