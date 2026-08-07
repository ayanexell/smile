<?php

use Livewire\Component;
use App\Models\Inventaris;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Departemens;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $nama_barang;
    #[Validate('required|integer|min:0')]
    public $jumlah;
    #[Validate('required|string|max:100')]
    public $kondisi;
    #[Validate('required|string|max:100')]
    public $tipe;
    // Hapus validasi untuk img_path karena diisi otomatis
    public $img_path;
    #[Validate('required|string|max:100')]
    public $warna;
    #[Validate('required|boolean')]
    public $dpt_dipinjam = false;
    #[Validate('nullable|image|max:2048')]
    public $img_upload;

    public $inventaris;

    public function validationAttributes()
    {
        return [
            'nama_barang' => 'Nama Barang',
            'img_upload' => 'Foto Barang', // ubah dari img_path
            'dpt_dipinjam' => 'Status Ketersediaan Pinjam',
        ];
    }

    public function messages()
    {
        return [
            'departemen_id.exists' => 'Departemen yang Anda pilih tidak terdaftar.',

            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.max' => 'Nama barang tidak boleh lebih dari 255 karakter.',

            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah minimal adalah 0.',

            'kondisi.required' => 'Kondisi barang wajib diisi.',
            'kondisi.max' => 'Kondisi tidak boleh lebih dari 100 karakter.',

            'tipe.required' => 'Tipe barang wajib diisi.',
            'tipe.max' => 'Tipe tidak boleh lebih dari 100 karakter.',

            'img_upload.required' => 'Foto atau gambar barang wajib diunggah.',
            'img_upload.image' => 'File harus berupa gambar (JPEG, PNG, dll).',
            'img_upload.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',

            'warna.required' => 'Warna barang wajib diisi.',
            'warna.max' => 'Warna tidak boleh lebih dari 100 karakter.',

            'dpt_dipinjam.required' => 'Status ketersediaan pinjam wajib dipilih.',
            'dpt_dipinjam.boolean' => 'Format status pinjam tidak valid.',
        ];
    }

    public function edit(Inventaris $id)
    {
        $this->inventaris = $id;
        $this->nama_barang = $id->nama_barang;
        $this->jumlah = $id->jumlah;
        $this->kondisi = $id->kondisi;
        $this->tipe = $id->tipe;
        $this->warna = $id->warna;
        $this->dpt_dipinjam = $id->dpt_dipinjam;
    }

    public function update()
    {
        // 1. Validasi semua input (termasuk img_upload)
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('edit-error', ['message' => $e->getMessage()]);
            return;
        }

        // 2. Simpan file setelah validasi berhasil
        try {
            if ($this->img_upload) {
                Storage::delete($this->inventaris->img_path);
                $this->img_path = $this->img_upload->store('inventaris', 'public');
            } else {
                $this->img_path = $this->inventaris->img_path;
            }

            // 3. Simpan ke database
            $this->inventaris->update([
                'nama_barang' => $this->nama_barang,
                'jumlah' => $this->jumlah,
                'kondisi' => $this->kondisi,
                'tipe' => $this->tipe,
                'warna' => $this->warna,
                'dpt_dipinjam' => $this->dpt_dipinjam,
                'img_path' => $this->img_path,
            ]);

            // 4. Reset form
            $this->reset(['nama_barang', 'jumlah', 'kondisi', 'tipe', 'img_path', 'warna', 'dpt_dipinjam', 'img_upload']);
            // Jangan reset departemen_id karena sudah di-mount
            $this->dispatch('edit-success', ['message' => 'Inventaris berhasil diupdate!']);
            session()->flash('notification', [
                'type' => 'success',
                'message' => 'Inventaris berhasil diupdate!',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('edit-error', ['message' => 'Gagal mengupdate: ' . $e->getMessage()]);
        }
    }
};
?>

<div>
    {{-- Overlay Modal --}}
    <div x-data="{
        show: false,
        errorMessage: null,
        successMessage: null,
        init() {
            window.addEventListener('edit-inventaris-modal', (e) => {
                $wire.edit(e.detail.id);
                this.show = true;
            });
            window.addEventListener('edit-success', (e) => {
                this.successMessage = e.detail.message;
                this.show = false;
            });
            window.addEventListener('edit-error', (e) => {
                this.errorMessage = e.detail.message;
            });
        },
    }" x-show="show" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4 backdrop-blur-sm"
        @click.self="show = false">

        {{-- Kontainer Modal --}}
        <div class="relative w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-stone-900"
            @click.stop>
            {{-- Loading Indicator --}}
            <div wire:loading wire:target="edit"
                class="absolute inset-0 z-50 flex items-center justify-center rounded-md bg-white/60 p-5 backdrop-blur-[0.5px] dark:bg-stone-900/60">
                <div
                    class="flex items-center gap-1.5 rounded-md border border-stone-100 bg-white px-2.5 py-1.5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <svg class="text-sage-600 dark:text-sage-400 h-3.5 w-3.5 animate-spin" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V12H4z"></path>
                    </svg>
                    <span class="text-[10px] font-medium text-stone-600 dark:text-stone-300">
                        {{ __('Mengambil data...') }}
                    </span>
                </div>
            </div>

            {{-- Indikator Loading khusus saat method create berjalan --}}
            <div wire:loading wire:target="update"
                class="absolute inset-0 z-50 flex items-center justify-center rounded-md bg-white/60 p-5 backdrop-blur-[0.5px] dark:bg-stone-900/60">
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

            {{-- Body Form --}}
            <form wire:submit.prevent="update" class="p-5">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-stone-200 pb-2 dark:border-stone-800">
                    <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-200">Edit Inventaris</h3>
                    <button @click="show = false"
                        class="rounded-md p-1 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600 dark:hover:bg-stone-800 dark:hover:text-stone-200">
                        <svg class="h-4 w-4 cursor-pointer" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
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
                <div class="mt-1 space-y-2">
                    {{-- Nama Barang --}}
                    <div>
                        <label class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Nama
                            Barang</label>
                        <input wire:model.live="nama_barang" type="text" required placeholder="Nama inventaris"
                            class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                        @error('nama_barang')
                            <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid: Tipe & Jumlah --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Tipe</label>
                            <input wire:model.live="tipe" type="text" required
                                placeholder="Elektronik, Furniture, dll."
                                class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                            @error('tipe')
                                <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Jumlah</label>
                            <input wire:model.live="jumlah" type="number" min="0" required placeholder="0"
                                class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                            @error('jumlah')
                                <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Grid: Kondisi & Warna --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Kondisi</label>
                            <select wire:model.live="kondisi" required
                                class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100">
                                <option value="">Pilih kondisi</option>
                                <option value="baik">Baik</option>
                                <option value="rusak">Rusak</option>
                            </select>
                            @error('kondisi')
                                <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Warna</label>
                            <input wire:model.live="warna" type="text" required placeholder="Hitam, Putih, dll."
                                class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                            @error('warna')
                                <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Upload Gambar -- Area Penuh --}}
                    <div>
                        <label
                            class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Gambar</label>

                        {{-- Area Upload --}}
                        <label
                            class="hover:border-sage-400 dark:hover:border-sage-500 relative block w-full cursor-pointer rounded-lg border-2 border-dashed border-stone-300 p-4 text-center transition dark:border-stone-600">

                            {{-- Input file asli disembunyikan --}}
                            <input type="file" wire:model.live="img_upload" accept="image/*" class="hidden">

                            {{-- Loading --}}
                            <div wire:loading wire:target="img_upload"
                                class="flex items-center justify-center gap-2 py-4 text-xs text-stone-500">
                                <svg class="text-sage-500 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Mengunggah...
                            </div>

                            {{-- State Kosong --}}
                            @if (!$img_upload)
                                <div wire:loading.remove wire:target="img_upload" class="py-2">
                                    <svg class="mx-auto mb-1 h-6 w-6 text-stone-400" fill="none"
                                        stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <p class="text-xs text-stone-500 dark:text-stone-400">Klik untuk unggah gambar</p>
                                    <p class="text-[10px] text-stone-400 dark:text-stone-500">PNG, JPG, max 2MB</p>
                                </div>
                            @endif

                            @if ($img_upload)
                                <div wire:loading.remove wire:target="img_upload" class="relative inline-block">
                                    <img src="{{ $img_upload->temporaryUrl() }}"
                                        class="mx-auto h-24 w-24 rounded-lg border border-stone-200 object-cover shadow-sm dark:border-stone-700"
                                        onerror="this.style.display='none'">
                                    <button type="button" wire:click="$set('img_upload', null)"
                                        class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] text-white shadow transition-opacity hover:bg-red-600 focus:outline-none">
                                        ×
                                    </button>
                                </div>
                            @endif

                            @error('img_upload')
                                <p class="mt-2 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </label>
                    </div>

                    {{-- Dapat Dipinjam (Toggle Switch) --}}
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium text-stone-600 dark:text-stone-400">Dapat Dipinjam</span>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" wire:model.live="dpt_dipinjam" class="peer sr-only">
                            <div
                                class="peer-focus:ring-sage-300 dark:peer-focus:ring-sage-800 peer-checked:bg-sage-600 after:inset-s-0.5 peer h-5 w-9 rounded-full bg-stone-200 after:absolute after:top-0.5 after:h-4 after:w-4 after:rounded-full after:border after:border-stone-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 rtl:peer-checked:after:-translate-x-full dark:border-stone-600 dark:bg-stone-700">
                            </div>
                        </label>
                    </div>
                    @error('dpt_dipinjam')
                        <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                    @enderror

                    {{-- Footer --}}
                    <div class="flex justify-end gap-2 border-t border-stone-100 pt-4 dark:border-stone-800">
                        <button type="button" x-on:click="show = false"
                            class="cursor-pointer rounded-lg border border-stone-200 px-4 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50 dark:border-stone-700 dark:text-stone-400 dark:hover:bg-stone-800">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="update"
                            class="bg-sage-600 hover:bg-sage-700 focus:ring-sage-500 cursor-pointer rounded-lg px-4 py-1.5 text-xs font-medium text-white transition focus:outline-none focus:ring-2 focus:ring-offset-1">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
