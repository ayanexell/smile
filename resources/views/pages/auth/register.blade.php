<x-layouts::auth :title="__('Register')">

    {{-- KOLOM KIRI: FORM --}}
    <div class="flex min-h-screen items-center justify-center bg-stone-100 dark:bg-stone-950 py-6">
        <div class="flex w-full max-w-5xl overflow-hidden rounded-2xl border border-stone-200 dark:border-stone-800 shadow-xl bg-white dark:bg-stone-900">

            {{-- Form --}}
            <div class="flex px-20 flex-col justify-center py-7 overflow-y-auto">
                <div class="w-full max-w-md mx-auto">

                    <div class="mb-6">
                        <h1 class="font-display text-3xl font-normal text-stone-800 dark:text-stone-100 mb-1">{{ __('Create an account') }}</h1>
                        <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Enter your details below to create your account') }}</p>
                    </div>

                    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4">
                        @csrf

                        <flux:input name="nama_lengkap" :label="__('Nama Lengkap')" :value="old('nama_lengkap')" type="text" required autofocus autocomplete="name" :placeholder="__('Nama lengkap sesuai KTP')" />

                        <flux:input name="nik" :label="__('NIK')" :value="old('nik')" type="text" inputmode="numeric" maxlength="16" minlength="16" required autocomplete="off" :placeholder="__('16 digit NIK')" />

                        <div class="grid grid-cols-2 gap-4">
                            <flux:input name="tgl_lahir" :label="__('Tanggal Lahir')" :value="old('tgl_lahir')" type="date" required autocomplete="bday" max="{{ now()->subYears(17)->format('Y-m-d') }}" />

                            <fieldset class="border-none p-0">
                                <legend class="text-sm font-medium text-stone-600 dark:text-stone-400 mb-2">{{ __('Jenis Kelamin') }}</legend>
                                <div class="flex gap-2 h-10">
                                    <label class="flex flex-1 items-center justify-center gap-2 text-sm border border-stone-200 dark:border-stone-700 rounded-lg cursor-pointer px-3 has-[:checked]:border-sage-600 has-[:checked]:bg-sage-50 has-[:checked]:text-sage-700 dark:has-[:checked]:border-sage-400 dark:has-[:checked]:bg-sage-950 dark:has-[:checked]:text-sage-400 transition-colors text-stone-500 dark:text-stone-400">
                                        <input type="radio" name="jenis_kelamin" value="laki-laki" @checked(old('jenis_kelamin') == 'laki-laki') required class="sr-only" />
                                        {{ __('Laki-laki') }}
                                    </label>
                                    <label class="flex flex-1 items-center justify-center gap-2 text-sm border border-stone-200 dark:border-stone-700 rounded-lg cursor-pointer px-3 has-[:checked]:border-sage-600 has-[:checked]:bg-sage-50 has-[:checked]:text-sage-700 dark:has-[:checked]:border-sage-400 dark:has-[:checked]:bg-sage-950 dark:has-[:checked]:text-sage-400 transition-colors text-stone-500 dark:text-stone-400">
                                        <input type="radio" name="jenis_kelamin" value="perempuan" @checked(old('jenis_kelamin') == 'perempuan') class="sr-only" />
                                        {{ __('Perempuan') }}
                                    </label>
                                </div>
                                @error('jenis_kelamin')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>

                        <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required autocomplete="email" placeholder="email@example.com" />

                        <flux:input name="alamat" :label="__('Alamat')" :value="old('alamat')" type="text" required autocomplete="street-address" :placeholder="__('Alamat lengkap')" />

                        <flux:input name="pekerjaan" :label="__('Pekerjaan')" :value="old('pekerjaan')" type="text" required autocomplete="organization-title" :placeholder="__('Pekerjaan')" />

                        <div class="grid grid-cols-2 gap-4">
                            <flux:input name="password" :label="__('Password')" type="password" required autocomplete="new-password" :placeholder="__('Password')" viewable />
                            <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required autocomplete="new-password" :placeholder="__('Confirm password')" viewable />
                        </div>

                        <flux:button type="submit" variant="primary" class="w-full mt-1">
                            {{ __('Create account') }}
                        </flux:button>

                    </form>

                    <p class="text-center text-sm text-stone-500 dark:text-stone-400 mt-5">
                        {{ __('Already have an account?') }}
                        <flux:link :href="route('login')" wire:navigate class="ml-1 font-medium text-sage-600 dark:text-sage-400">
                            {{ __('Log in') }}
                        </flux:link>
                    </p>

                </div>
            </div>

            {{-- Branding --}}
            <div class="hidden lg:flex flex-col justify-between w-80 bg-stone-50 dark:bg-stone-950 border-l border-stone-200 dark:border-stone-800 px-8 py-10 relative overflow-hidden flex-shrink-0">

                <div class="absolute inset-0 grid-pattern pointer-events-none opacity-50"></div>

                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sage-600 dark:bg-sage-500 flex items-center justify-center shadow-md flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-display text-lg text-stone-800 dark:text-stone-100">SMILE</div>
                        <div class="font-mono text-[9px] uppercase tracking-widest text-stone-500 dark:text-stone-500">Sistem Manajemen Inventaris Latee</div>
                    </div>
                </div>

                <div class="relative z-10">
                    <p class="font-display text-2xl italic leading-snug text-stone-800 dark:text-stone-100 mb-3">
                        Kelola inventaris<br/>dengan <span class="text-sage-600 dark:text-sage-400 not-italic">cerdas</span><br/>dan efisien.
                    </p>
                    <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed">
                        Platform manajemen inventaris terpadu untuk Pondok Pesantren Annuqayah Latee.
                    </p>
                </div>

                <div class="relative z-10 flex justify-center">
                    <img src="{{ asset('assets/svg/register-svg.svg') }}" alt="Ilustrasi Registrasi" class="w-40 opacity-85 drop-shadow-md" />
                </div>

                <div class="relative z-10 flex flex-col gap-2">
                    @foreach(['Manajemen stok real-time', 'Laporan otomatis & akurat', 'Akses multi-pengguna aman'] as $item)
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 text-xs text-stone-500 dark:text-stone-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-sage-600 dark:bg-sage-400 flex-shrink-0"></span>
                        {{ $item }}
                    </div>
                    @endforeach
                </div>

                <p class="relative z-10 font-mono text-[10px] tracking-wide text-stone-400 dark:text-stone-600">
                    &copy; {{ date('Y') }} PP. Annuqayah Latee &middot; Guluk-Guluk, Sumenep
                </p>

            </div>
        </div>
    </div>

</x-layouts::auth>
