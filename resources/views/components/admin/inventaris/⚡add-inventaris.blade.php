<?php

use Livewire\Component;
use App\Models\Inventaris;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Actions\RoboflowAction;

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
    public $dpt_dipinjam = false; // 2MB max

    #[Validate('nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048')]
    public $imgUpload;

    public $analyzing = false;

    public function updatedImgUpload($path)
    {
        $this->analyzeImage();
    }

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

    public function analyzeImage()
    {
        if (!$this->imgUpload) {
            return;
        }

        $this->analyzing = true;

        try {
            $imagePath = $this->imgUpload->getRealPath();
            $action = app(RoboflowAction::class);
            $result = $action->analyzeFromPath($imagePath);

            if ($result['count'] > 0) {
                $this->nama_barang = ucwords($result['first_class'] ?? 'Objek Terdeteksi');
                $this->tipe = $result['type'];
                $this->jumlah = $result['count'];
                $this->warna = 'Bawaan';
                // $this->kondisi = 'Baik';

                session()->flash('notification', [
                    'type' => 'success',
                    'message' => "Analisis berhasil! Terdeteksi {$this->jumlah} objek {$this->nama_barang}.",
                ]);
            } else {
                session()->flash('notification', [
                    'type' => 'warning',
                    'message' => 'Analisis selesai, namun tidak ada objek yang berhasil terdeteksi pada gambar.',
                ]);
            }
        } catch (Exception $e) {
            session()->flash('notification', [
                'type' => 'error',
                'message' => 'Gagal menganalisis gambar: ' . $e->getMessage(),
            ]);
        }

        $this->analyzing = false;
    }

    public function saveInventaris()
    {
        // 1. Validasi semua input (termasuk img_upload)
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('add-error', ['message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())]);
            return;
        }

        // 2. Simpan file setelah validasi berhasil
        try {
            if ($this->img_upload) {
                $this->img_path = $this->img_upload->store('inventaris', 'public');
            } else {
                // Jika tidak ada file, kita beri default atau error
                throw new \Exception('Gambar wajib diunggah.');
            }

            // 3. Simpan ke database
            Inventaris::create([
                'user_id' => Auth::user()->id_user,
                'nama_barang' => $this->nama_barang,
                'jumlah' => $this->jumlah,
                'kondisi' => $this->kondisi,
                'tipe' => $this->tipe,
                'img_path' => $this->img_path,
                'warna' => $this->warna,
                'dpt_dipinjam' => $this->dpt_dipinjam,
            ]);

            // 4. Reset form
            $this->reset(['nama_barang', 'jumlah', 'kondisi', 'tipe', 'img_path', 'warna', 'dpt_dipinjam', 'img_upload']);
            $this->dispatch('add-success', ['message' => 'Inventaris berhasil ditambahkan!']);
            session()->flash('notification', [
                'type' => 'success',
                'message' => 'Inventaris berhasil ditambahkan!',
            ]);

            // Dispatch event untuk refresh tabel (jika diperlukan)
            $this->dispatch('inventaris-updated');
        } catch (\Exception $e) {
            $this->dispatch('add-error', ['message' => 'Gagal menyimpan: ' . $e->getMessage()]);
            session()->flash('notification', [
                'type' => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ]);
        }
    }
};
?>

<div>
    {{-- Overlay Modal --}}
    <div x-data="{
        show: false,
        successMessage: null,
        errorMessage: null,
        init() {
            window.addEventListener('add-inventaris-modal', (e) => {
                this.show = true;
            });
            window.addEventListener('add-success', (e) => {
                this.successMessage = e.detail.message;
                this.show = false;
            });
            window.addEventListener('add-error', (e) => {
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
            <div wire:loading wire:target="saveInventaris"
                class="absolute inset-0 z-50 flex h-full items-center justify-center rounded-md bg-white/60 p-5 backdrop-blur-[0.5px] dark:bg-stone-900/60">
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

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-stone-200 px-5 py-3 dark:border-stone-800">
                <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-200">Tambah Inventaris</h3>
                <button @click="show = false"
                    class="rounded-md p-1 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600 dark:hover:bg-stone-800 dark:hover:text-stone-200">
                    <svg class="h-4 w-4 cursor-pointer" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Notifikasi --}}
            <div>
                @if (session()->has('notification'))
                    @php
                        $notif = session('notification');
                        $type = $notif['type'] ?? 'info';
                        $message = $notif['message'] ?? '';

                        $colors = [
                            'success' =>
                                'border-emerald-200 dark:border-emerald-900/60 text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/30',
                            'error' =>
                                'border-red-200 dark:border-red-900/60 text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/30',
                            'warning' =>
                                'border-amber-200 dark:border-amber-900/60 text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/30',
                            'info' =>
                                'border-blue-200 dark:border-blue-900/60 text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/30',
                        ];

                        $icons = [
                            'success' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                            'error' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                            'warning' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />',
                            'info' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        ];

                        $colorClass = $colors[$type] ?? $colors['info'];
                        $iconPath = $icons[$type] ?? $icons['info'];
                    @endphp

                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-4"
                        class="z-9999 fixed left-1/2 top-5 w-full max-w-sm -translate-x-1/2 px-4">

                        <div
                            class="{{ $colorClass }} flex select-none items-center gap-2.5 rounded-lg border bg-white py-2 pl-3 pr-2.5 shadow-xl shadow-stone-200/50 dark:bg-stone-900 dark:shadow-none">
                            <div class="shrink-0">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    {!! $iconPath !!}
                                </svg>
                            </div>
                            <div class="flex-1 text-[11px] font-medium leading-normal">
                                <span>{{ $message }}</span>
                            </div>
                            <button @click="show = false"
                                class="shrink-0 rounded p-1 text-stone-400 transition-colors hover:text-stone-600 dark:hover:text-stone-200">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Body Form --}}
            <form wire:submit.prevent="saveInventaris" class="relative space-y-2 p-5">
                <div class="relative space-y-2">
                    {{-- Pesan Error Global --}}
                    <div x-show="errorMessage" x-cloak
                        class="rounded-md border border-red-200 bg-red-50 px-2.5 py-1.5 text-[11px] text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300"
                        x-text="errorMessage">
                    </div>
                    <div x-show="successMessage" x-cloak
                        class="rounded-md border border-green-200 bg-green-50 px-2.5 py-1.5 text-[11px] text-green-700 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-300"
                        x-text="successMessage">
                    </div>

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
                            <input type="file" wire:model.live="imgUpload" accept="image/*" class="hidden">

                            {{-- Loading --}}
                            <div wire:loading wire:target="imgUpload"
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
                            @if (!$imgUpload)
                                <div wire:loading.remove wire:target="imgUpload" class="py-2">
                                    <svg class="mx-auto mb-1 h-6 w-6 text-stone-400" fill="none"
                                        stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <p class="text-xs text-stone-500 dark:text-stone-400">Klik untuk unggah gambar</p>
                                    <p class="text-[10px] text-stone-400 dark:text-stone-500">PNG, JPG, max 2MB</p>
                                </div>
                            @endif

                            @error('imgUpload')
                                <p class="mt-2 text-[10px] text-red-500">{{ $message }}</p>
                            @enderror
                        </label>
                        @if ($imgUpload)
                            {{-- Tombol analisis ulang (opsional) --}}
                            <div class="mt-2 text-center">
                                <button type="button" wire:click="analyzeImage" :disabled="$wire.analyzing"
                                    class="text-sage-600 cursor-pointer text-[10px] hover:underline focus:outline-none">
                                    Analisis Ulang Gambar dengan AI
                                </button>
                            </div>
                        @endif

                        {{-- Indikator sedang menganalisis --}}
                        <div wire:loading wire:target="analyzeImage"
                            class="flex items-center justify-center gap-2 py-2 text-xs text-stone-500">
                            <svg class="text-sage-500 h-4 w-4 animate-spin" ...>...</svg>
                            Menganalisis gambar dengan AI...
                        </div>
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
                            class="cursor-pointerrounded-lg border border-stone-200 px-4 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50 dark:border-stone-700 dark:text-stone-400 dark:hover:bg-stone-800">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveInventaris"
                            class="bg-sage-600 hover:bg-sage-700 focus:ring-sage-500 cursor-pointer rounded-lg px-4 py-1.5 text-xs font-medium text-white transition focus:outline-none focus:ring-2 focus:ring-offset-1">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
