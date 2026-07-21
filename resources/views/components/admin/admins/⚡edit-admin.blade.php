<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\UserForm;
use App\Models\User;

new class extends Component {
    public UserForm $form;

    #[On('edit-admin')]
    public function editAdmin($id)
    {
        $user = User::findOrFail($id);
        $this->form->setUser($user);
        Flux::modal('edit-admin-modal')->show();
    }

    public function updateAdmin()
    {
        $this->form->update();
        Flux::modal('edit-admin-modal')->close();
        session()->flash('success', 'Informasi pengguna berhasil diperbarui.');
        $this->redirectRoute('admin.admins', navigate: true);
    }
};
?>

<div>
    <flux:modal name="edit-admin-modal" class="w-full p-4">
        {{-- Form Submit diarahkan ke method updateAdmin di komponen Livewire --}}
        <form wire:submit.prevent="updateAdmin" class="space-y-4">

            {{-- Header Modal --}}
            <div>
                <flux:heading size="md">{{ __('Ubah Informasi Pengguna') }}</flux:heading>
                <flux:subheading class="text-[11px] leading-normal">
                    {{ __('Perbarui data detail akun dan profil user yang terpilih.') }}</flux:subheading>
            </div>

            <flux:separator class="my-2" />

            @island(lazy: true)
                @placeholder
                    <!-- Loading indicator -->
                    <div class="animate-pulse space-y-3">
                        {{-- Grid untuk menyamai Baris 1 (Nama & NIK) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <div class="h-3 bg-stone-200 dark:bg-stone-800 rounded w-20"></div>
                                <div class="h-7 bg-stone-100 dark:bg-stone-800/50 rounded w-full"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 bg-stone-200 dark:bg-stone-800 rounded w-12"></div>
                                <div class="h-7 bg-stone-100 dark:bg-stone-800/50 rounded w-full"></div>
                            </div>
                        </div>

                        {{-- Menyampingi Baris 2 (Email) --}}
                        <div class="space-y-1">
                            <div class="h-3 bg-stone-200 dark:bg-stone-800 rounded w-16"></div>
                            <div class="h-7 bg-stone-100 dark:bg-stone-800/50 rounded w-full"></div>
                        </div>

                        {{-- Grid untuk menyamai Baris 3 (Tanggal Lahir & Jenis Kelamin) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <div class="h-3 bg-stone-200 dark:bg-stone-800 rounded w-24"></div>
                                <div class="h-7 bg-stone-100 dark:bg-stone-800/50 rounded w-full"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 bg-stone-200 dark:bg-stone-800 rounded w-20"></div>
                                <div class="h-7 bg-stone-100 dark:bg-stone-800/50 rounded w-full"></div>
                            </div>
                        </div>

                        {{-- Menyampingi Baris 4 (Pekerjaan) --}}
                        <div class="space-y-1">
                            <div class="h-3 bg-stone-200 dark:bg-stone-800 rounded w-28"></div>
                            <div class="h-7 bg-stone-100 dark:bg-stone-800/50 rounded w-full"></div>
                        </div>

                        {{-- Menyampingi Baris 5 (Alamat Textarea) --}}
                        <div class="space-y-1">
                            <div class="h-3 bg-stone-200 dark:bg-stone-800 rounded w-32"></div>
                            <div class="h-12 bg-stone-100 dark:bg-stone-800/50 rounded w-full"></div>
                        </div>
                    </div>
                @endplaceholder
                <div class="relative">

                    {{-- Indikator Loading khusus saat method editAdmin berjalan --}}
                    <div wire:loading wire:target="editAdmin"
                        class="absolute inset-0 z-50 flex items-center justify-center bg-white/60 dark:bg-stone-900/60 rounded-lg backdrop-blur-[0.5px]">
                        <div
                            class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-stone-800 border border-stone-100 dark:border-stone-700 shadow-md rounded-lg">
                            <svg class="w-4 h-4 animate-spin text-sage-600 dark:text-sage-400" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V12H4z"></path>
                            </svg>
                            <span
                                class="text-[11px] font-medium text-stone-600 dark:text-stone-300">{{ __('Memuat data...') }}</span>
                        </div>
                    </div>

                    {{-- Grid Form Input --}}
                    <div class="space-y-3 text-xs">

                        {{-- Baris 1: Nama & NIK --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <flux:input wire:model="form.nama_lengkap" label="Nama Lengkap" placeholder="Sesuai KTP"
                                    size="sm" class="text-xs" required />
                            </div>

                            <div>
                                <flux:input wire:model="form.nik" label="NIK" placeholder="16 digit angka" maxlength="16"
                                    size="sm" class="text-xs" required />
                            </div>
                        </div>

                        {{-- Baris 2: Email --}}
                        <div>
                            <flux:input wire:model="form.email" type="email" label="Alamat Email"
                                placeholder="user@domain.com" size="sm" class="text-xs" required />
                            <flux:error name="form.email" class="text-[10px] mt-1 font-medium" />
                        </div>

                        {{-- Baris 3: Tanggal Lahir & Jenis Kelamin --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <flux:input wire:model="form.tgl_lahir" type="date" label="Tanggal Lahir" size="sm"
                                    class="text-xs" required />
                                <flux:error name="form.tgl_lahir" class="text-[10px] mt-1 font-medium" />
                            </div>

                            <div>
                                <flux:select wire:model="form.jenis_kelamin" label="Jenis Kelamin" placeholder="Pilih..."
                                    size="sm" class="text-xs" required>
                                    <flux:select.option value="laki-laki">{{ __('Laki-laki') }}</flux:select.option>
                                    <flux:select.option value="perempuan">{{ __('Perempuan') }}</flux:select.option>
                                </flux:select>
                                <flux:error name="form.jenis_kelamin" class="text-[10px] mt-1 font-medium" />
                            </div>
                        </div>

                        {{-- Baris 4: Pekerjaan --}}
                        <div>
                            <flux:input wire:model="form.pekerjaan" label="Pekerjaan / Jabatan"
                                placeholder="Contoh: Staff Administrasi" size="sm" class="text-xs" />
                            <flux:error name="form.pekerjaan" class="text-[10px] mt-1 font-medium" />
                        </div>

                        {{-- Baris 5: Alamat --}}
                        <div>
                            <flux:textarea wire:model="form.alamat" label="Alamat Lengkap Rumah"
                                placeholder="Tuliskan alamat domisili saat ini..." rows="2" class="text-xs" />
                            <flux:error name="form.alamat" class="text-[10px] mt-1 font-medium" />
                        </div>
                    </div>
                </div>
            @endisland

            {{-- Footer Modal / Tombol Aksi --}}
            <div class="flex justify-end gap-1.5 border-t border-stone-100 dark:border-stone-800/60 pt-3 mt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" class="text-xs">{{ __('Batal') }}</flux:button>
                </flux:modal.close>

                {{-- Tombol simpan otomatis disabled saat data sedang dimuat --}}
                <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled"
                    wire:target="editAdmin"
                    class="text-xs bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 font-medium">
                    {{ __('Perbarui Data') }}
                </flux:button>
            </div>

        </form>
    </flux:modal>
</div>
