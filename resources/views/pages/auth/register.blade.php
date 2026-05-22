<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Create an account')"
            :description="__('Enter your details below to create your account')"
        />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Nama Lengkap -->
            <flux:input
                name="nama_lengkap"
                :label="__('Nama Lengkap')"
                :value="old('nama_lengkap')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- NIK -->
            <flux:input
                name="nik"
                :label="__('NIK')"
                :value="old('nik')"
                type="text"
                inputmode="numeric"
                maxlength="16"
                minlength="16"
                required
                autocomplete="off"
                :placeholder="__('16 digit NIK')"
            />

            <!-- Tanggal Lahir -->
            <flux:input
                name="tgl_lahir"
                :label="__('Tanggal Lahir')"
                :value="old('tgl_lahir')"
                type="date"
                required
                autocomplete="bday"
                max="{{ now()->subYears(17)->format('Y-m-d') }}"
            />

            <!-- Jenis Kelamin -->
            <fieldset>
                <legend class="mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    {{ __('Jenis Kelamin') }}
                </legend>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="jenis_kelamin"
                            value="laki-laki"
                            @checked(old('jenis_kelamin') == 'laki-laki')
                            required
                        >
                        <span>{{ __('Laki-laki') }}</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="jenis_kelamin"
                            value="perempuan"
                            @checked(old('jenis_kelamin') == 'perempuan')
                        >
                        <span>{{ __('Perempuan') }}</span>
                    </label>
                </div>
                @error('jenis_kelamin')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </fieldset>

            <!-- Email -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Alamat -->
            <flux:input
                name="alamat"
                :label="__('Alamat')"
                :value="old('alamat')"
                type="text"
                required
                autocomplete="street-address"
                :placeholder="__('Alamat lengkap')"
            />

            <!-- Pekerjaan -->
            <flux:input
                name="pekerjaan"
                :label="__('Pekerjaan')"
                :value="old('pekerjaan')"
                type="text"
                required
                autocomplete="organization-title"
                :placeholder="__('Pekerjaan')"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
