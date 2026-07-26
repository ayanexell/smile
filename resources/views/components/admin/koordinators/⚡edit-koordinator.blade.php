<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use Flux\Flux;

new class extends Component {
    public UserForm $form;
    public $departemens;

    #[On('edit-koordinator')]
    public function editKoordinator($id)
    {
        $user = User::findOrFail($id);
        $this->form->setUser($user);
        Flux::modal('edit-koordinator-modal')->show();
    }

    public function updateKoordinator()
    {
        $this->form->update();
        Flux::modal('edit-koordinator-modal')->close();
        session()->flash('success', 'Informasi pengguna berhasil diperbarui.');
        $this->redirectRoute('admin.koordinators', navigate: true);
    }

    public function mount()
    {
        $this->departemens = \App\Models\Departemens::all();
    }
};
?>

<div>
    <flux:modal name="edit-koordinator-modal" class="w-full p-4">
        {{-- Form Submit diarahkan ke method updateKoordinator di komponen Livewire --}}
        <form wire:submit.prevent="updateKoordinator" class="space-y-4">

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
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="space-y-1">
                                <div class="h-3 w-20 rounded bg-stone-200 dark:bg-stone-800"></div>
                                <div class="h-7 w-full rounded bg-stone-100 dark:bg-stone-800/50"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 w-12 rounded bg-stone-200 dark:bg-stone-800"></div>
                                <div class="h-7 w-full rounded bg-stone-100 dark:bg-stone-800/50"></div>
                            </div>
                        </div>

                        {{-- Menyampingi Baris 2 (Email) --}}
                        <div class="space-y-1">
                            <div class="h-3 w-16 rounded bg-stone-200 dark:bg-stone-800"></div>
                            <div class="h-7 w-full rounded bg-stone-100 dark:bg-stone-800/50"></div>
                        </div>

                        {{-- Grid untuk menyamai Baris 3 (Tanggal Lahir & Jenis Kelamin) --}}
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="space-y-1">
                                <div class="h-3 w-24 rounded bg-stone-200 dark:bg-stone-800"></div>
                                <div class="h-7 w-full rounded bg-stone-100 dark:bg-stone-800/50"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 w-20 rounded bg-stone-200 dark:bg-stone-800"></div>
                                <div class="h-7 w-full rounded bg-stone-100 dark:bg-stone-800/50"></div>
                            </div>
                        </div>

                        {{-- Menyampingi Baris 4 (Pekerjaan) --}}
                        <div class="space-y-1">
                            <div class="h-3 w-28 rounded bg-stone-200 dark:bg-stone-800"></div>
                            <div class="h-7 w-full rounded bg-stone-100 dark:bg-stone-800/50"></div>
                        </div>

                        {{-- Menyampingi Baris 5 (Alamat Textarea) --}}
                        <div class="space-y-1">
                            <div class="h-3 w-32 rounded bg-stone-200 dark:bg-stone-800"></div>
                            <div class="h-12 w-full rounded bg-stone-100 dark:bg-stone-800/50"></div>
                        </div>
                    </div>
                @endplaceholder
                <div class="relative">

                    {{-- Indikator Loading khusus saat method editKoordinator berjalan --}}
                    <div wire:loading wire:target="editKoordinator"
                        class="absolute inset-0 z-50 flex items-center justify-center rounded-lg bg-white/60 backdrop-blur-[0.5px] dark:bg-stone-900/60">
                        <div
                            class="flex items-center gap-2 rounded-lg border border-stone-100 bg-white px-3 py-2 shadow-md dark:border-stone-700 dark:bg-stone-800">
                            <svg class="text-sage-600 dark:text-sage-400 h-4 w-4 animate-spin" fill="none"
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
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
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
                            <flux:error name="form.email" class="mt-1 text-[10px] font-medium" />
                        </div>

                        {{-- Baris 3: Tanggal Lahir & Jenis Kelamin --}}
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <flux:input wire:model="form.tgl_lahir" type="date" label="Tanggal Lahir" size="sm"
                                    class="text-xs" required />
                                <flux:error name="form.tgl_lahir" class="mt-1 text-[10px] font-medium" />
                            </div>

                            <div>
                                <flux:select wire:model="form.jenis_kelamin" label="Jenis Kelamin" placeholder="Pilih..."
                                    size="sm" class="text-xs" required>
                                    <flux:select.option value="laki-laki">{{ __('Laki-laki') }}</flux:select.option>
                                    <flux:select.option value="perempuan">{{ __('Perempuan') }}</flux:select.option>
                                </flux:select>
                                <flux:error name="form.jenis_kelamin" class="mt-1 text-[10px] font-medium" />
                            </div>
                        </div>

                        {{-- Baris 4: Pekerjaan --}}
                        <div>
                            <flux:input wire:model="form.pekerjaan" label="Pekerjaan"
                                placeholder="Contoh: Staff Administrasi" size="sm" class="text-xs" />
                            <flux:error name="form.pekerjaan" class="mt-1 text-[10px] font-medium" />
                        </div>

                        {{-- Baris 5: Departemen --}}
                        <div>
                            <div>
                                <flux:select wire:model="form.departemen_id" label="Departemen" placeholder="Pilih..."
                                    size="sm" class="text-xs" required>
                                    @foreach ($departemens as $departemen)
                                        <flux:select.option value="{{ $departemen->id_departemen }}">
                                            {{ $departemen->nama_departemen }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="form.departemen_id" class="mt-1 text-[10px] font-medium" />
                            </div>
                        </div>

                        {{-- Baris 5: Alamat --}}
                        <div>
                            <flux:textarea wire:model="form.alamat" label="Alamat Lengkap Rumah"
                                placeholder="Tuliskan alamat domisili saat ini..." rows="2" class="text-xs" />
                            <flux:error name="form.alamat" class="mt-1 text-[10px] font-medium" />
                        </div>
                    </div>
                </div>
            @endisland

            {{-- Footer Modal / Tombol Aksi --}}
            <div class="mt-4 flex justify-end gap-1.5 border-t border-stone-100 pt-3 dark:border-stone-800/60">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" class="text-xs">{{ __('Batal') }}</flux:button>
                </flux:modal.close>

                {{-- Tombol simpan otomatis disabled saat data sedang dimuat --}}
                <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled"
                    wire:target="editKoordinator"
                    class="bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-xs font-medium">
                    {{ __('Perbarui Data') }}
                </flux:button>
            </div>

        </form>
    </flux:modal>
</div>
