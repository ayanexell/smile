<?php

use Livewire\Component;
use App\Forms\InventarisForm;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|exists:departemens,id_departemen')]
    public $departemen_id;
    #[Validate('required|string|max:255')]
    public $nama_barang;
    #[Validate('required|integer|min:0')]
    public $jumlah;
    #[Validate('required|string|max:100')]
    public $kondisi;
    #[Validate('required|string|max:100')]
    public $tipe;
    #[Validate("required|string|max:255")]
    public $img_path;
    #[Validate('required|string|max:100')]
    public $warna;
    #[Validate('required|boolean')]
    public $dpt_dipinjam;

    public $img_upload;
    public $showModal = false;
    public $analyzing = false;

    #[On('edit-inventaris')]
    public function editInventaris($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        $this->form->setInventaris($inventaris);
        $this->showModal = true;
    }

    #[On('add-inventaris-modal')]
    public function addInventaris()
    {
        $this->showModal = true;
    }

    public function submit()
    {
        $this->validate();

        Inventaris::create($this->form);

        // Reset form & tutup modal
        $this->reset('form');
        $this->dispatch('inventaris-updated'); // event opsional untuk refresh tabel
        $this->showModal = false;
        session()->flash('notification', [
            'type' => 'success',
            'message' => 'Inventaris berhasil ditambahkan!'
        ]);
    }

    public function updatedImgPath()
    {
        if ($this->img_upload) {
            $this->analyzeImage();
        }
    }

    public function analyzeImage()
    {
        $this->analyzing = true;

        // Kirim file gambar ke API YOLO
        try {
            $response = Http::attach('image', fopen($this->img_upload->getRealPath(), 'r'), $this->img_upload->getClientOriginalName())->post('http://localhost:5000/detect');

            if ($response->successful()) {
                $data = $response->json();
                $this->nama_barang = $data['nama_barang'] ?? '';
                $this->tipe = $data['tipe'] ?? '';
                $this->warna = $data['warna'] ?? '';
                $this->kondisi = $data['kondisi'] ?? 'baik';
                $this->jumlah = 1; // default jumlah
                session()->flash('notification', [
                    'type' => 'success',
                    'message' => 'Analisis berhasil! Form telah terisi otomatis.'
                ]);
            } else {
                session()->flash('notification', [
                    'type' => 'error',
                    'message' => 'Gagal menganalisis gambar: ' . $response->body()
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('notification', [
                'type' => 'error',
                'message' => 'Error koneksi ke API YOLO: ' . $e->getMessage()
            ]);
        }

        $this->analyzing = false;
    }

    public function saveInventaris()
    {
        if($this->img_upload) {
            $this->img_path = $this->img_upload->store('inventaris', 'public');
        }
        $this->validate();

        Inventaris::create([
            'departemen_id' => $this->departemen_id,
            'nama_barang' => $this->nama_barang,
            'jumlah' => $this->jumlah,
            'kondisi' => $this->kondisi,
            'tipe' => $this->tipe,
            'img_path' => $this->img_path,
            'warna' => $this->warna,
            'dpt_dipinjam' => $this->dpt_dipinjam,
        ]);

        // Reset form & tutup modal
        $this->reset(['departemen_id', 'nama_barang', 'jumlah', 'kondisi', 'tipe', 'img_path', 'warna', 'dpt_dipinjam', 'img_upload']);
        $this->showModal = false;
        session()->flash('notification', [
            'type' => 'success',
            'message' => 'Inventaris berhasil ditambahkan!'
        ]);
    }
};
?>

<div>
    {{-- Overlay Modal --}}
    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm p-4"
        @click.self="show = false">

        {{-- Kontainer Modal --}}
        <div class="bg-white dark:bg-stone-900 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden" @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-stone-200 dark:border-stone-800">
                <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-200">Tambah Inventaris</h3>
                <button @click="show = false"
                    class="p-1 rounded-md text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 hover:bg-stone-100 dark:hover:bg-stone-800 transition">
                    <svg class="cursor-pointer w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
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
                        class="fixed top-5 left-1/2 -translate-x-1/2 z-[9999] w-full max-w-sm px-4">

                        <div
                            class="flex items-center gap-2.5 pl-3 pr-2.5 py-2 bg-white dark:bg-stone-900 border rounded-lg shadow-xl shadow-stone-200/50 dark:shadow-none select-none {{ $colorClass }}">
                            <div class="flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    {!! $iconPath !!}
                                </svg>
                            </div>
                            <div class="flex-1 text-[11px] font-medium leading-normal">
                                {{ $message }}
                            </div>
                            <button @click="show = false"
                                class="flex-shrink-0 p-1 text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 rounded transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Body Form --}}
            <form wire:submit.prevent="saveInventory" class="p-5 space-y-4">
                {{-- Nama Barang --}}
                <div>
                    <label class="block text-[11px] font-medium text-stone-600 dark:text-stone-400 mb-1">Nama
                        Barang</label>
                    <input wire:model.live="nama_barang" type="text" required placeholder="Nama inventaris"
                        class="w-full px-3 py-1.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-800 dark:text-stone-100 placeholder:text-stone-400 focus:outline-none focus:ring-1 focus:ring-sage-500" />
                    @error('nama_barang')
                        <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Grid: Tipe & Jumlah --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[11px] font-medium text-stone-600 dark:text-stone-400 mb-1">Tipe</label>
                        <input wire:model.live="tipe" type="text" required placeholder="Elektronik, Furniture, dll."
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-800 dark:text-stone-100 placeholder:text-stone-400 focus:outline-none focus:ring-1 focus:ring-sage-500" />
                        @error('tipe')
                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium text-stone-600 dark:text-stone-400 mb-1">Jumlah</label>
                        <input wire:model.live="jumlah" type="number" min="0" required placeholder="0"
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-800 dark:text-stone-100 placeholder:text-stone-400 focus:outline-none focus:ring-1 focus:ring-sage-500" />
                        @error('jumlah')
                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Grid: Kondisi & Warna --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[11px] font-medium text-stone-600 dark:text-stone-400 mb-1">Kondisi</label>
                        <select wire:model.live="kondisi" required
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-800 dark:text-stone-100 focus:outline-none focus:ring-1 focus:ring-sage-500">
                            <option value="">Pilih kondisi</option>
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                        </select>
                        @error('kondisi')
                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-medium text-stone-600 dark:text-stone-400 mb-1">Warna</label>
                        <input wire:model.live="warna" type="text" required placeholder="Hitam, Putih, dll."
                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-800 dark:text-stone-100 placeholder:text-stone-400 focus:outline-none focus:ring-1 focus:ring-sage-500" />
                        @error('warna')
                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Upload Gambar -- Area Penuh --}}
                <div>
                    <label class="block text-[11px] font-medium text-stone-600 dark:text-stone-400 mb-1">Gambar</label>

                    {{-- Area Upload --}}
                    <label
                        class="block w-full cursor-pointer border-2 border-dashed border-stone-300 dark:border-stone-600 rounded-lg p-4 text-center hover:border-sage-400 dark:hover:border-sage-500 transition relative">

                        {{-- Input file asli disembunyikan --}}
                        <input type="file" wire:model.live="img_upload" accept="image/*" class="hidden">

                        {{-- Loading --}}
                        <div wire:loading wire:target="img_upload"
                            class="flex items-center justify-center gap-2 text-xs text-stone-500 py-4">
                            <svg class="animate-spin h-4 w-4 text-sage-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Mengunggah...
                        </div>

                        {{-- State Kosong --}}
                        @if (!$img_upload)
                            <div wire:loading.remove wire:target="img_upload" class="py-2">
                                <svg class="mx-auto h-6 w-6 text-stone-400 mb-1" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <p class="text-xs text-stone-500 dark:text-stone-400">Klik untuk unggah gambar</p>
                                <p class="text-[10px] text-stone-400 dark:text-stone-500">PNG, JPG, max 2MB</p>
                            </div>
                        @endif

                        {{-- Preview --}}
                        @if ($img_upload)
                            <div wire:loading.remove wire:target="img_upload" class="relative inline-block">
                                <img src="{{ $img_upload->temporaryUrl() }}"
                                    class="h-24 w-24 object-cover rounded-lg border border-stone-200 dark:border-stone-700 shadow-sm mx-auto">
                                <button type="button" wire:click="$set('img_upload', null)"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center hover:bg-red-600 focus:outline-none transition-opacity shadow">
                                    ×
                                </button>
                            </div>
                        @endif

                        @error('img_upload')
                            <p class="text-[10px] text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </label>
                    @if ($img_upload)
                        {{-- Tombol analisis ulang (opsional) --}}
                        <div class="mt-2 text-center">
                            <button type="button" wire:click="analyzeImage" :disabled="$wire.analyzing"
                                class="text-[10px] text-sage-600 hover:underline focus:outline-none">
                                Analisis Ulang Gambar dengan AI
                            </button>
                        </div>
                    @endif

                    {{-- Indikator sedang menganalisis --}}
                    <div wire:loading wire:target="analyzeImage"
                        class="flex items-center justify-center gap-2 text-xs text-stone-500 py-2">
                        <svg class="animate-spin h-4 w-4 text-sage-500" ...>...</svg>
                        Menganalisis gambar dengan AI...
                    </div>
                </div>

                {{-- Dapat Dipinjam (Toggle Switch) --}}
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-medium text-stone-600 dark:text-stone-400">Dapat Dipinjam</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="dpt_dipinjam" class="sr-only peer">
                        <div
                            class="w-9 h-5 bg-stone-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-sage-300 dark:peer-focus:ring-sage-800 rounded-full peer dark:bg-stone-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-stone-600 peer-checked:bg-sage-600">
                        </div>
                    </label>
                </div>
                @error('dpt_dipinjam')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror

                {{-- Footer --}}
                <div class="flex justify-end gap-2 pt-4 border-t border-stone-100 dark:border-stone-800">
                    <button type="button" @click="show = false"
                        class="px-4 py-1.5 text-xs font-medium rounded-lg border border-stone-200 dark:border-stone-700 text-stone-600 dark:text-stone-400 hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-1.5 text-xs font-medium rounded-lg bg-sage-600 text-white hover:bg-sage-700 transition focus:outline-none focus:ring-2 focus:ring-sage-500 focus:ring-offset-1">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
