<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $nama_lengkap = '';
    public string $nik = '';
    public string $tgl_lahir = '';
    public string $jenis_kelamin = '';
    public string $email = '';
    public string $alamat = '';
    public string $pekerjaan = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->nama_lengkap  = $user->nama_lengkap;
        $this->nik           = $user->nik;
        $this->tgl_lahir     = $user->tgl_lahir;
        $this->jenis_kelamin = $user->jenis_kelamin;
        $this->email         = $user->email;
        $this->alamat        = $user->alamat;
        $this->pekerjaan     = $user->pekerjaan;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id_user));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /* @chisel-email-verification */
    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
    /* @end-chisel-email-verification */
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your personal information')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <!-- Nama Lengkap -->
            <flux:input
                wire:model="nama_lengkap"
                :label="__('Nama Lengkap')"
                type="text"
                required
                autofocus
                autocomplete="name"
            />

            <!-- NIK -->
            <flux:input
                wire:model="nik"
                :label="__('NIK')"
                type="text"
                inputmode="numeric"
                maxlength="16"
                minlength="16"
                required
                autocomplete="off"
            />

            <!-- Tanggal Lahir -->
            <flux:input
                wire:model="tgl_lahir"
                :label="__('Tanggal Lahir')"
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
                        <input type="radio" wire:model="jenis_kelamin" value="laki-laki" required>
                        <span>{{ __('Laki-laki') }}</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" wire:model="jenis_kelamin" value="perempuan">
                        <span>{{ __('Perempuan') }}</span>
                    </label>
                </div>
                @error('jenis_kelamin')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </fieldset>

            <!-- Email -->
            <div>
                <flux:input
                    wire:model="email"
                    :label="__('Email')"
                    type="email"
                    required
                    autocomplete="email"
                />

                {{-- Verifikasi email (opsional) --}}
                @if ($this->hasUnverifiedEmail)
                    <div class="mt-2">
                        <flux:text>
                            {{ __('Your email address is unverified.') }}
                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>
                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Alamat -->
            <flux:input
                wire:model="alamat"
                :label="__('Alamat')"
                type="text"
                required
                autocomplete="street-address"
            />

            <!-- Pekerjaan -->
            <flux:input
                wire:model="pekerjaan"
                :label="__('Pekerjaan')"
                type="text"
                required
                autocomplete="organization-title"
            />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
