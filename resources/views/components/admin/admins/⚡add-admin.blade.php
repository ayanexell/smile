<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Validation\ValidationException;

new class extends Component {
    public UserForm $form;

    public function create()
    {
        $roleId = Roles::where('nama_role', 'Admin')->value('id_role');
        try {
            $this->form->store($roleId);
            session()->flash('success', 'Admin berhasil ditambahkan.');
            $this->dispatch('admin-added', message: 'Admin berhasil ditambahkan.');
        } catch (ValidationException $e) {
            // Tangani kesalahan jika terjadi
            session()->flash('error', 'Terjadi kesalahan saat membuat admin: ' . $e->getMessage());
            $this->dispatch('admin-error', message: 'Terjadi kesalahan saat membuat admin: ' . $e->getMessage());
        }
    }
};
?>

<div>
    <div x-data="{
        show: false,
        errorMessage: '',
        successMessage: '',
        init() {
            window.addEventListener('add-admin-modal', () => {
                this.errorMessage = '';
                this.show = true;
            });
    
            // Event error dari Livewire
            window.addEventListener('admin-error', (e) => {
                this.errorMessage = e.detail.message;
            });
            // Event sukses dari Livewire
            window.addEventListener('admin-added', (e) => {
                this.successMessage = e.detail.message;
            });
        }
    }" x-show="show" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-3 text-xs sm:p-4" x-cloak
        @click.self="show = false">

        <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-lg bg-white shadow-xl dark:bg-stone-900"
            @click.stop>

            {{-- Form Submit diarahkan ke method create di komponen Livewire --}}
            <form wire:submit.prevent="create" class="relative space-y-3 p-4">

                {{-- Header Modal --}}
                <div>
                    <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-100">
                        {{ __('Tambah Pengguna Baru') }}
                    </h3>
                    <p class="mt-0.5 text-[11px] leading-normal text-stone-500 dark:text-stone-400">
                        {{ __('Isi data detail akun dan profil untuk pengguna baru.') }}
                    </p>
                </div>

                <div class="border-t border-stone-100 dark:border-stone-800"></div>

                {{-- Pesan Error Global --}}
                <div x-show="errorMessage" x-cloak
                    class="rounded-md border border-red-200 bg-red-50 px-2.5 py-1.5 text-[11px] text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300"
                    x-text="errorMessage">
                </div>
                <div x-show="successMessage" x-cloak
                    class="rounded-md border border-green-200 bg-green-50 px-2.5 py-1.5 text-[11px] text-green-700 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-300"
                    x-text="successMessage">
                </div>

                <div class="relative">

                    {{-- Indikator Loading khusus saat method create berjalan --}}
                    <div wire:loading wire:target="create"
                        class="absolute inset-0 z-50 flex items-center justify-center rounded-md bg-white/60 backdrop-blur-[0.5px] dark:bg-stone-900/60">
                        <div
                            class="flex items-center gap-1.5 rounded-md border border-stone-100 bg-white px-2.5 py-1.5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                            <svg class="text-sage-600 dark:text-sage-400 h-3.5 w-3.5 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V12H4z"></path>
                            </svg>
                            <span class="text-[10px] font-medium text-stone-600 dark:text-stone-300">
                                {{ __('Menyimpan data...') }}
                            </span>
                        </div>
                    </div>

                    {{-- Grid Form Input --}}
                    <div class="space-y-2.5">

                        {{-- Baris 1: Nama & NIK --}}
                        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                            <div>
                                <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                    {{ __('Nama Lengkap') }}
                                </label>
                                <input wire:model="form.nama_lengkap" type="text" placeholder="Sesuai KTP" required
                                    class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                                @error('form.nama_lengkap')
                                    <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                    {{ __('NIK') }}
                                </label>
                                <input wire:model="form.nik" type="text" placeholder="16 digit angka" maxlength="16"
                                    required
                                    class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                                @error('form.nik')
                                    <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Baris 2: Email & No WhatsApp --}}
                        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                            <div>
                                <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                    {{ __('Alamat Email') }}
                                </label>
                                <input wire:model="form.email" type="email" placeholder="user@domain.com" required
                                    class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                                @error('form.email')
                                    <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                    {{ __('No WhatsApp') }}
                                </label>
                                <input wire:model="form.no_wa" type="text" placeholder="628xxxxxxxxx"
                                    class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                                @error('form.no_wa')
                                    <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Baris 3: Password --}}
                        <div>
                            <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                {{ __('Password') }}
                            </label>
                            <input wire:model="form.password" type="password" placeholder="Minimal 8 karakter" required
                                class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                            @error('form.password')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Baris 4: Tanggal Lahir & Jenis Kelamin --}}
                        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                            <div>
                                <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                    {{ __('Tanggal Lahir') }}
                                </label>
                                <input wire:model="form.tgl_lahir" type="date" required
                                    class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                                @error('form.tgl_lahir')
                                    <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                    {{ __('Jenis Kelamin') }}
                                </label>
                                <select wire:model="form.jenis_kelamin" required
                                    class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100">
                                    <option value="">{{ __('Pilih...') }}</option>
                                    <option value="laki-laki">{{ __('Laki-laki') }}</option>
                                    <option value="perempuan">{{ __('Perempuan') }}</option>
                                </select>
                                @error('form.jenis_kelamin')
                                    <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Baris 5: Pekerjaan --}}
                        <div>
                            <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                {{ __('Pekerjaan / Jabatan') }}
                            </label>
                            <input wire:model="form.pekerjaan" type="text" placeholder="Contoh: Staff Administrasi"
                                class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                            @error('form.pekerjaan')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Baris 6: Alamat --}}
                        <div>
                            <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                {{ __('Alamat Lengkap Rumah') }}
                            </label>
                            <textarea wire:model="form.alamat" rows="2" placeholder="Tuliskan alamat domisili saat ini..."
                                class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"></textarea>
                            @error('form.alamat')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Footer Modal / Tombol Aksi --}}
                <div class="mt-3 flex justify-end gap-1.5 border-t border-stone-100 pt-3 dark:border-stone-800">
                    <button type="button" @click="show = false"
                        class="rounded-md border border-stone-200 px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50 dark:border-stone-700 dark:text-stone-400 dark:hover:bg-stone-800">
                        {{ __('Batal') }}
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="create"
                        class="bg-sage-600 hover:bg-sage-700 focus:ring-sage-500 dark:bg-sage-500 dark:hover:bg-sage-600 rounded-md px-3 py-1.5 text-xs font-medium text-white transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-60">
                        {{ __('Simpan') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
