<?php

use Livewire\Component;
use App\Models\Inventaris;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

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
    #[Validate('required|string|max:100')]
    public $warna;
    #[Validate('required|boolean')]
    public $dpt_dipinjam = false;
    #[Validate('required|mimes:png,jpg,jpeg|mimetypes:image/png,image/jpeg|max:2340')]
    public $img_upload;

    public $img_path;
    public $analyzing = false;

    public function messages(): array
    {
        return [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.max' => 'Nama barang maksimal 255 karakter.',
            'jumlah.required' => 'Jumlah harus diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah tidak boleh negatif.',
            'kondisi.required' => 'Kondisi barang wajib dipilih.',
            'tipe.required' => 'Tipe barang wajib diisi.',
            'warna.required' => 'Warna wajib diisi.',
            'dpt_dipinjam.required' => 'Status peminjaman harus dipilih.',
            'dpt_dipinjam.boolean' => 'Status peminjaman tidak valid.',
            'img_upload.required' => 'File gambar wajib diunggah.',
            'img_upload.mimes' => 'Format file harus PNG, JPG, atau JPEG.',
            'img_upload.mimetypes' => 'File yang diunggah bukan gambar yang valid.',
            'img_upload.max' => 'Ukuran file maksimal 2,28 MB.',
        ];
    }

    #[On('edit-inventaris')]
    public function editInventaris($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        // TODO: isi properti form dari $inventaris sesuai kebutuhan Anda
        $this->dispatch('open-inventaris-modal');
    }

    #[On('add-inventaris-modal')]
    public function addInventaris()
    {
        $this->resetForm();
        $this->dispatch('open-inventaris-modal');
    }

    public function updatedImgUpload()
    {
        if ($this->img_upload) {
            $this->analyzeImage();
        }
    }

    public function analyzeImage()
    {
        $this->analyzing = true;

        try {
            $response = Http::attach('image', fopen($this->img_upload->getRealPath(), 'r'), $this->img_upload->getClientOriginalName())->post('http://localhost:5000/detect');

            if ($response->successful()) {
                $data = $response->json();
                $this->nama_barang = $data['nama_barang'] ?? '';
                $this->tipe = $data['tipe'] ?? '';
                $this->warna = $data['warna'] ?? '';
                $this->kondisi = $data['kondisi'] ?? 'baik';
                $this->jumlah = 1;

                $this->dispatch('inventaris-toast', type: 'success', message: 'Analisis berhasil! Form telah terisi otomatis.');
            } else {
                $this->dispatch('inventaris-toast', type: 'error', message: 'Gagal menganalisis gambar: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->dispatch('inventaris-toast', type: 'error', message: 'Error koneksi ke API YOLO: ' . $e->getMessage());
        }

        $this->analyzing = false;
    }

    public function submit()
    {
        $this->validate();

        if ($this->img_upload) {
            $this->img_path = $this->img_upload->store('inventaris', 'public');
        }

        try {
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

            $this->resetForm();
            $this->dispatch('close-inventaris-modal');
            $this->dispatch('inventaris-toast', type: 'success', message: 'Inventaris berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('inventaris-toast', type: 'error', message: 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function resetForm()
    {
        $this->reset(['nama_barang', 'jumlah', 'kondisi', 'tipe', 'img_path', 'warna', 'dpt_dipinjam', 'img_upload']);
    }
};
?>

<div>
    {{-- Overlay Modal --}}
    <div x-data="{
        show: false,
        init() {
            window.addEventListener('open-inventaris-modal', () => {
                this.show = true;
            });
            window.addEventListener('close-inventaris-modal', () => {
                this.show = false;
            });
        }
    }" x-show="show" x-cloak x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4 backdrop-blur-sm">

        {{-- Kontainer Modal --}}
        <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-stone-900" @click.stop>

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

            {{-- Toast Notifikasi (client-side via Alpine, dipicu event dari Livewire) --}}
            <div x-data="{
                show: false,
                type: 'info',
                message: '',
                colorClass: '',
                iconPath: '',
                colors: {
                    success: 'border-emerald-200 dark:border-emerald-900/60 text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/30',
                    error: 'border-red-200 dark:border-red-900/60 text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/30',
                    warning: 'border-amber-200 dark:border-amber-900/60 text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/30',
                    info: 'border-blue-200 dark:border-blue-900/60 text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/30'
                },
                icons: {
                    success: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\' />',
                    error: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\' />',
                    warning: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z\' />',
                    info: '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\' />'
                },
                init() {
                    window.addEventListener('inventaris-toast', (e) => {
                        this.type = e.detail.type || 'info';
                        this.message = e.detail.message || '';
                        this.colorClass = this.colors[this.type] ?? this.colors.info;
                        this.iconPath = this.icons[this.type] ?? this.icons.info;
                        this.show = true;
                        setTimeout(() => this.show = false, 4000);
                    });
                }
            }" x-show="show" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="z-9999 fixed left-1/2 top-5 w-full max-w-sm -translate-x-1/2 px-4">

                <div :class="colorClass"
                    class="flex select-none items-center gap-2.5 rounded-lg border bg-white py-2 pl-3 pr-2.5 shadow-xl shadow-stone-200/50 dark:bg-stone-900 dark:shadow-none">
                    <div class="shrink-0">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                            x-html="iconPath"></svg>
                    </div>
                    <div class="flex-1 text-[11px] font-medium leading-normal" x-text="message"></div>
                    <button @click="show = false"
                        class="shrink-0 rounded p-1 text-stone-400 transition-colors hover:text-stone-600 dark:hover:text-stone-200">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body Form --}}
            <form wire:submit.prevent="submit" class="space-y-4 p-5">
                {{-- Nama Barang --}}
                <div>
                    <label class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Nama
                        Barang</label>
                    <input wire:model.debounce.250ms="nama_barang" type="text" required placeholder="Nama inventaris"
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
                        <input wire:model.live.debounce.250ms="tipe" type="text" required
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
                        <select wire:model.live.debounce.250ms="kondisi" required
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
                        <input wire:model.live.debounce.250ms="warna" type="text" required
                            placeholder="Hitam, Putih, dll."
                            class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                        @error('warna')
                            <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Upload Gambar --}}
                <div>
                    <label class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Gambar</label>

                    <label
                        class="hover:border-sage-400 dark:hover:border-sage-500 relative block w-full cursor-pointer rounded-lg border-2 border-dashed border-stone-300 p-4 text-center transition dark:border-stone-600">

                        <input type="file" wire:model.live.debounce.250ms="img_upload" accept="image/*"
                            class="hidden">

                        <div wire:loading wire:target="img_upload"
                            class="flex items-center justify-center gap-2 py-4 text-xs text-stone-500">
                            <svg class="text-sage-500 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Mengunggah...
                        </div>

                        @if (!$img_upload)
                            <div wire:loading.remove wire:target="img_upload" class="py-2">
                                <svg class="mx-auto mb-1 h-6 w-6 text-stone-400" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <p class="text-xs text-stone-500 dark:text-stone-400">Klik untuk unggah gambar</p>
                                <p class="text-[10px] text-stone-400 dark:text-stone-500">PNG, JPG, max 2MB</p>
                            </div>
                        @endif

                        @if ($img_upload)
                            <div wire:loading.remove wire:target="img_upload" class="relative inline-block">
                                <img src="{{ $img_upload->temporaryUrl() }}"
                                    class="mx-auto h-24 w-24 rounded-lg border border-stone-200 object-cover shadow-sm dark:border-stone-700">
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

                    @if ($img_upload)
                        <div class="mt-2 text-center">
                            <button type="button" wire:click="analyzeImage" :disabled="$wire.analyzing"
                                class="text-sage-600 text-[10px] hover:underline focus:outline-none">
                                Analisis Ulang Gambar dengan AI
                            </button>
                        </div>
                    @endif

                    <div wire:loading wire:target="analyzeImage"
                        class="flex items-center justify-center gap-2 py-2 text-xs text-stone-500">
                        <svg class="text-sage-500 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
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
                    <button type="button" @click="show = false"
                        class="rounded-lg border border-stone-200 px-4 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50 dark:border-stone-700 dark:text-stone-400 dark:hover:bg-stone-800">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-sage-600 hover:bg-sage-700 focus:ring-sage-500 cursor-pointer rounded-lg px-4 py-1.5 text-xs font-medium text-white transition focus:outline-none focus:ring-2 focus:ring-offset-1">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
