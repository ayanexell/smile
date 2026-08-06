<?php

use Livewire\Component;
use App\Models\Departemens;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;

new #[Title('Kelola Departemen')] class extends Component {
    #[Validate('required|string|max:255')]
    public $nama_departemen;
    #[Validate('required|string|max:255')]
    public $singkatan;
    #[Validate('required|string|max:255')]
    public $deskripsi;

    public function store()
    {
        $this->validate();
        try {
            Departemens::create([
                'nama_departemen' => $this->nama_departemen,
                'singkatan' => $this->singkatan,
                'deskripsi' => $this->deskripsi,
            ]);
            $this->dispatch('add-success', ['message' => 'Data departemen berhasil ditambahkan!']);
            session()->flash('success', 'Data departemen berhasil ditambahkan!');
            $this->reset('nama_departemen', 'singkatan', 'detail');
        } catch (\Exception $e) {
            $this->dispatch('add-error', ['message' => 'Error: ' . $e->getMessage()]);
        }
    }
};
?>

<div>
    <div x-data="{
        show: false,
        successMessage: '',
        errorMessage: '',
        init() {
            window.addEventListener('modal-add-departemen', () => {
                this.message = '';
                this.errorMessage = '';
                this.show = true;
            });
    
            // Event sukses dari Livewire
            window.addEventListener('add-success', (e) => {
                this.show = false;
                this.successMessage = e.detail.message;
            });
    
            // Event error dari Livewire
            window.addEventListener('add-error', (e) => {
                this.errorMessage = e.detail.message;
            });
        },
    }" x-show="show" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-3 text-xs sm:p-4" x-cloak
        @click.self="show = false">
        {{-- Form Submit diarahkan ke method update di komponen Livewire --}}
        <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-lg bg-white shadow-xl dark:bg-stone-900"
            @click.stop>
            <form wire:submit.prevent="store" class="relative space-y-3 p-4">

                {{-- Header Modal --}}
                <div>
                    <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-100">
                        {{ __('Tambah Departemen') }}
                    </h3>
                    <p class="mt-0.5 text-[11px] leading-normal text-stone-500 dark:text-stone-400">
                        {{ __('Tambah data detail departemen untuk sistem anda.') }}
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
                    <div wire:loading wire:target="store"
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

                    <div class="space-y-2.5">

                        <div>
                            <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                {{ __('Nama Departemen') }}
                            </label>
                            <input wire:model="nama_departemen" type="text" required
                                class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                            @error('nama_departemen')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                {{ __('Singkatan') }}
                            </label>
                            <input wire:model="singkatan" type="text" required
                                class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                            @error('singkatan')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                                {{ __('Deskripsi') }}
                            </label>
                            <textarea wire:model="deskripsi" type="text" required rows="4"
                                class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"></textarea>
                            @error('deskripsi')
                                <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Footer Modal / Tombol Aksi --}}
                <div class="mt-3 flex justify-end gap-1.5 border-t border-stone-100 pt-3 dark:border-stone-800">
                    <button type="button" x-on:click="show = false"
                        class="cursor-pointer rounded-md border border-stone-200 px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50 dark:border-stone-700 dark:text-stone-400 dark:hover:bg-stone-800">
                        {{ __('Batal') }}
                    </button>

                    {{-- Tombol simpan otomatis disabled saat data sedang dimuat --}}
                    <button type="submit" wire:loading.attr="disabled" wire:target="edit"
                        class="bg-sage-600 hover:bg-sage-700 focus:ring-sage-500 dark:bg-sage-500 dark:hover:bg-sage-600 cursor-pointer rounded-md px-3 py-1.5 text-xs font-medium text-white transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-60">
                        {{ __('Perbarui Data') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
