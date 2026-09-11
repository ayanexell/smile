<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    #[Validate('image|mimes:jpeg,png,jpg|max:2048')]
    public $upload_avatar;
    #[Validate('image|mimes:jpeg,png,jpg|max:5120')]
    public $upload_ktp;

    public string $nama_lengkap = '';
    public $avatar;
    public string $nik = '';
    public string $tgl_lahir = '';
    public string $jenis_kelamin = '';
    public string $email = '';
    public $ktp_path;
    public string $no_wa = '';
    public string $alamat = '';
    public string $pekerjaan = '';
    public $profile_status;

    public function mount(): void
    {
        $user = Auth::user();
        $status = $user->profile_status;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->nik = $user->nik;
        $this->tgl_lahir = $user->tgl_lahir;
        $this->jenis_kelamin = $user->jenis_kelamin;
        $this->email = $user->email;
        $this->no_wa = $user->no_wa;
        $this->alamat = $user->alamat;
        $this->pekerjaan = $user->pekerjaan;
        $this->profile_status = $status;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        // Validasi semua field termasuk file
        $validated = $this->validate($this->profileRules($user->id_user));

        // Ambil hanya data teks (bukan file) untuk di-fill ke model
        $userData = collect($validated)
            ->except(['avatar', 'ktp'])
            ->toArray();

        $user->fill($userData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // ─── Upload Avatar ──────────────────────────────
        if ($this->upload_avatar instanceof \Illuminate\Http\UploadedFile) {
            // Hapus avatar lama jika ada
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Simpan file baru
            $path = $this->upload_avatar->store('avatars');
            $user->avatar = $path;
        }

        // ─── Upload KTP ─────────────────────────────────
        if ($this->upload_ktp instanceof \Illuminate\Http\UploadedFile) {
            // Hapus KTP lama jika ada
            if ($user->ktp_path) {
                Storage::disk('public')->delete($user->ktp_path);
            }
            // Simpan file baru
            $path = $this->upload_ktp->store('ktp');
            $user->ktp_path = $path;
        }
        $user->profile_status = $user->avatar && $user->ktp_path ? true : false;
        $user->save();

        Flux::toast(variant: 'success', text: __('Profil berhasil diperbarui.'));
    }

    /* @chisel-email-verification */
    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $user->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
    /* @end-chisel-email-verification */
}; ?>

<section class="min-h-full w-full bg-stone-100 dark:bg-stone-950">
    <div class="mb-3 max-w-full">
        <div>
            <div
                class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm sm:grid-cols-2 dark:border-stone-800 dark:bg-stone-900">
                {{-- Header --}}
                <div class="flex justify-between border-b border-stone-100 px-4 py-3.5 dark:border-stone-800">
                    <div>
                        <h2 class="font-display text-sm font-semibold text-stone-800 dark:text-stone-100">
                            {{ __('Informasi Pribadi') }}
                        </h2>
                        <p class="mt-0.5 text-[11px] text-stone-500 dark:text-stone-400">
                            {{ __('Perbarui data diri dan foto profil Anda') }}
                        </p>
                    </div>
                    <div>
                        @if ($this->profile_status)
                            <span class="text-[10px] font-medium text-green-600 dark:text-green-400">
                                Profil Valid
                            </span>
                        @else
                            <span class="text-[10px] font-medium text-amber-600 dark:text-amber-400">
                                Lengkapi Dokumen (Avatar & KTP)
                            </span>
                        @endif
                    </div>
                </div>

                <form wire:submit="updateProfileInformation" class="space-y-3.5 p-4">

                    {{-- Avatar + KTP --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                        {{-- Avatar --}}
                        <div
                            class="{{ auth()->user()->profile_status ? 'border-green-500!' : 'border-red-500!' }} flex flex-col items-center gap-2 rounded-xl border border-dashed border-stone-200 bg-stone-50 p-3 dark:border-stone-700 dark:bg-stone-800/50">
                            <div class="group relative">
                                @if ($this->upload_avatar)
                                    <img src="{{ $this->upload_avatar->temporaryUrl() }}" alt="Avatar"
                                        class="{{ $this->profile_status ? '' : 'ring-red-600!' }} ring-sage-200 dark:ring-sage-800 h-14 w-14 rounded-full object-cover ring-2 ring-offset-2 ring-offset-white dark:ring-offset-stone-900" />
                                @elseif (auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar"
                                        class="ring-sage-200 dark:ring-sage-800 h-14 w-14 rounded-full object-cover ring-2 ring-offset-2 ring-offset-white dark:ring-offset-stone-900" />
                                @else
                                    <div
                                        class="bg-sage-100 ring-sage-200 dark:bg-sage-900 dark:ring-sage-800 flex h-14 w-14 items-center justify-center rounded-full ring-2 ring-offset-2 ring-offset-white dark:ring-offset-stone-900">
                                        <span class="text-sage-600 dark:text-sage-400 text-lg font-bold uppercase">
                                            {{ mb_substr(auth()->user()->nama_lengkap ?? 'U', 0, 2) }}
                                        </span>
                                    </div>
                                @endif
                                <label for="avatar-upload"
                                    class="absolute inset-0 flex cursor-pointer items-center justify-center rounded-full bg-black/0 transition-all group-hover:bg-black/40">
                                    <svg class="h-4 w-4 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </label>
                                <input id="avatar-upload" wire:model="upload_avatar" type="file" accept="image/*"
                                    class="sr-only" />
                            </div>
                            <div class="text-center">
                                <p class="text-[11px] font-semibold text-stone-700 dark:text-stone-300">
                                    {{ __('Foto Profil') }}</p>
                                <p class="text-[10px] text-stone-400 dark:text-stone-500">JPG, PNG · maks. 2MB</p>
                            </div>
                            @error('avatar')
                                <p class="text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- KTP --}}
                        <div
                            class="{{ auth()->user()->profile_status ? 'border-green-500!' : 'border-red-500!' }} flex flex-col items-center gap-2 rounded-xl border border-dashed border-stone-200 bg-stone-50 p-3 dark:border-stone-700 dark:bg-stone-800/50">
                            @if ($this->upload_ktp)
                                <img src="{{ $this->upload_ktp->temporaryUrl() }}" alt="KTP"
                                    class="h-12 w-full rounded-lg border border-stone-200 object-cover dark:border-stone-700" />
                            @elseif (auth()->user()->ktp_path)
                                <img src="{{ Storage::url(auth()->user()->ktp_path) }}" alt="KTP"
                                    class="h-12 w-full rounded-lg border border-stone-200 object-cover dark:border-stone-700" />
                            @else
                                <div
                                    class="{{ $this->profile_status ? '' : 'bg-red-100!' }} flex w-full items-center justify-center rounded-lg border border-stone-200 bg-stone-100 py-1 dark:border-stone-700 dark:bg-stone-800">
                                    <div class="flex flex-col items-center text-stone-400 dark:text-stone-600">
                                        <svg class="h-8 w-8" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                            <defs>
                                                <style>
                                                    .a {
                                                        fill: none;
                                                        stroke: currentColor;
                                                        stroke-linecap: round;
                                                        stroke-linejoin: round;
                                                    }
                                                </style>
                                            </defs>
                                            <rect class="a" x="5.6751" y="10.9786" width="36.6498" height="26.0429"
                                                rx="3" />
                                            <circle class="a" cx="14.8376" cy="21.4867" r="3.5632" />
                                            <path class="a"
                                                d="M10.3276,31.0945h9.7835a.92.92,0,0,0,.6994-1.5192,7.1719,7.1719,0,0,0-11.1823,0,.92.92,0,0,0,.6994,1.5192Z" />
                                            <line class="a" x1="28.7085" y1="20.8504" x2="35.7076"
                                                y2="20.8504" />
                                            <line class="a" x1="28.7085" y1="27.7222" x2="35.7076"
                                                y2="27.7222" />
                                            <line class="a" x1="28.7085" y1="24.2863"
                                                x2="38.38"y2="24.2863" />
                                        </svg>
                                        <span class="text-[10px]">Belum ada KTP</span>
                                    </div>
                                </div>
                            @endif
                            <label for="ktp-upload"
                                class="inline-flex cursor-pointer items-center gap-1 rounded-lg border border-stone-200 px-2 py-1 text-[10px] font-semibold text-stone-600 transition-colors hover:bg-stone-100 dark:border-stone-700 dark:text-stone-400 dark:hover:bg-stone-800">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{ __('Unggah KTP') }}
                            </label>
                            <input id="ktp-upload" wire:model="upload_ktp" type="file" accept="image/*,.pdf"
                                class="sr-only" />
                            <p class="text-[10px] text-stone-400 dark:text-stone-500">JPG, PNG, PDF · maks. 5MB</p>
                            @error('ktp')
                                <p class="text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t border-stone-100 dark:border-stone-800"></div>

                    {{-- Nama Lengkap + NIK --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-700 dark:text-stone-300">{{ __('Nama Lengkap') }}</label>
                            <input wire:model="nama_lengkap" type="text" required autofocus autocomplete="name"
                                placeholder="{{ __('Nama sesuai KTP') }}"
                                class="focus:border-sage-500 focus:ring-sage-500/20 focus:outline-hidden w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs transition-colors placeholder:text-stone-400 focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-200 dark:placeholder:text-stone-500" />
                            @error('nama_lengkap')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-700 dark:text-stone-300">{{ __('NIK') }}</label>
                            <input wire:model="nik" type="text" inputmode="numeric" maxlength="16"
                                minlength="16" required autocomplete="off" placeholder="16 digit NIK"
                                class="focus:border-sage-500 focus:ring-sage-500/20 focus:outline-hidden w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs transition-colors placeholder:text-stone-400 focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-200 dark:placeholder:text-stone-500" />
                            @error('nik')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tgl Lahir + Jenis Kelamin --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-700 dark:text-stone-300">{{ __('Tanggal Lahir') }}</label>
                            <input wire:model="tgl_lahir" type="date" required autocomplete="bday"
                                max="{{ now()->subYears(17)->format('Y-m-d') }}"
                                class="focus:border-sage-500 focus:ring-sage-500/20 focus:outline-hidden scheme-light-dark w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs transition-colors focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-200" />
                            @error('tgl_lahir')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <fieldset class="border-none p-0">
                            <legend class="mb-1.5 text-[11px] font-medium text-stone-700 dark:text-stone-300">
                                {{ __('Jenis Kelamin') }}</legend>
                            <div class="flex h-8 gap-1.5">
                                <label
                                    class="has-checked:border-sage-600 has-checked:bg-sage-50 has-checked:text-sage-700 dark:has-checked:border-sage-400 dark:has-checked:bg-sage-950 dark:has-checked:text-sage-400 flex flex-1 cursor-pointer items-center justify-center rounded-lg border border-stone-200 px-2 text-[11px] text-stone-500 transition-colors dark:border-stone-700 dark:text-stone-400">
                                    <input type="radio" wire:model="jenis_kelamin" value="laki-laki" required
                                        class="sr-only" />
                                    {{ __('Laki-laki') }}
                                </label>
                                <label
                                    class="has-checked:border-sage-600 has-checked:bg-sage-50 has-checked:text-sage-700 dark:has-checked:border-sage-400 dark:has-checked:bg-sage-950 dark:has-checked:text-sage-400 flex flex-1 cursor-pointer items-center justify-center rounded-lg border border-stone-200 px-2 text-[11px] text-stone-500 transition-colors dark:border-stone-700 dark:text-stone-400">
                                    <input type="radio" wire:model="jenis_kelamin" value="perempuan"
                                        class="sr-only" />
                                    {{ __('Perempuan') }}
                                </label>
                            </div>
                            @error('jenis_kelamin')
                                <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </fieldset>
                    </div>

                    {{-- Email + No. WA --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-700 dark:text-stone-300">{{ __('Email') }}</label>
                            <input wire:model="email" type="email" required autocomplete="email"
                                class="focus:border-sage-500 focus:ring-sage-500/20 focus:outline-hidden w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs transition-colors placeholder:text-stone-400 focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-200" />
                            @error('email')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                            @if ($this->hasUnverifiedEmail)
                                <div
                                    class="mt-1.5 flex items-start gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-2 py-1.5 text-[10px] dark:border-amber-800 dark:bg-amber-950">
                                    <svg class="mt-0.5 h-3.5 w-3.5 flex-shrink-0 text-amber-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                    </svg>
                                    <div class="text-amber-700 dark:text-amber-300">
                                        {{ __('Email belum terverifikasi.') }}
                                        <button type="button" wire:click.prevent="resendVerificationNotification"
                                            class="ml-1 font-semibold underline underline-offset-2 transition-colors hover:text-amber-900 dark:hover:text-amber-100">
                                            {{ __('Kirim ulang verifikasi.') }}
                                        </button>
                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-0.5 font-medium text-green-600 dark:text-green-400">
                                                {{ __('Link verifikasi telah dikirim ke email Anda.') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-700 dark:text-stone-300">{{ __('No. Whatsapp') }}</label>
                            <input wire:model="no_wa" type="text" required autocomplete="tel"
                                class="focus:border-sage-500 focus:ring-sage-500/20 focus:outline-hidden w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs transition-colors placeholder:text-stone-400 focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-200" />
                            @error('no_wa')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat + Pekerjaan --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-700 dark:text-stone-300">{{ __('Alamat') }}</label>
                            <input wire:model="alamat" type="text" required autocomplete="street-address"
                                placeholder="{{ __('Alamat lengkap') }}"
                                class="focus:border-sage-500 focus:ring-sage-500/20 focus:outline-hidden w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs transition-colors placeholder:text-stone-400 focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-200 dark:placeholder:text-stone-500" />
                            @error('alamat')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-700 dark:text-stone-300">{{ __('Pekerjaan') }}</label>
                            <input wire:model="pekerjaan" type="text" required autocomplete="organization-title"
                                placeholder="{{ __('Pekerjaan') }}"
                                class="focus:border-sage-500 focus:ring-sage-500/20 focus:outline-hidden w-full rounded-xl border border-stone-200 bg-white px-2.5 py-1.5 text-xs transition-colors placeholder:text-stone-400 focus:ring-2 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-200 dark:placeholder:text-stone-500" />
                            @error('pekerjaan')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div
                        class="flex items-center justify-between border-t border-stone-100 pt-3 dark:border-stone-800">
                        <div wire:loading wire:target="updateProfileInformation"
                            class="flex items-center gap-1 text-[10px] text-stone-400 dark:text-stone-500">
                            <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                            </svg>
                            Menyimpan…
                        </div>

                        @if (session('status') === 'profile-updated')
                            <span
                                class="text-sage-600 dark:text-sage-400 flex items-center gap-1 text-[10px] font-medium">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Tersimpan!') }}
                            </span>
                        @endif

                        <button type="submit" data-test="update-profile-button"
                            class="bg-sage-600 shadow-xs hover:bg-sage-700 focus:ring-sage-500/50 focus:outline-hidden dark:bg-sage-500 dark:hover:bg-sage-600 ml-auto inline-flex items-center justify-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-semibold text-white transition-all focus:ring-2">
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div
        class="overflow-hidden rounded-2xl border border-stone-200 bg-white p-3 shadow-sm sm:grid-cols-2 dark:border-stone-800 dark:bg-stone-900">
        {{-- Header lebih kecil --}}
        <div class="border-b border-stone-100 px-4 py-3.5 dark:border-stone-800">
            <h2 class="font-display text-sm font-semibold text-stone-800 dark:text-stone-100">
                {{ __('Password') }}
            </h2>
            <p class="mt-0.5 text-[11px] text-stone-500 dark:text-stone-400">
                {{ __('Perbarui Password Anda') }}
            </p>
        </div>
        <form method="POST" wire:submit="updatePassword" class="space-y-3.5 p-4">
            <flux:input wire:model="current_password" :label="__('Current password')" type="password" required
                autocomplete="current-password" viewable />
            <flux:input wire:model="password" :label="__('New password')" type="password" required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable />
            <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable />

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" data-test="update-password-button">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </div>
</section>
