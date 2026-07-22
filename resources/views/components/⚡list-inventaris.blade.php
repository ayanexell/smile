<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Inventaris;

new #[Layout('layouts.guest')] #[Title('List Inventaris')] class extends Component {
    public $search = '';
    public $filterTipe = '';

    public function render()
    {
        $inventaris = Inventaris::onlyDipinjamkan()
            ->with('departemen')
            ->when($this->search, function ($query) {
                $query->where('nama_barang', 'like', '%' . $this->search . '%')->orWhere('tipe', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterTipe, function ($query) {
                $query->where('tipe', $this->filterTipe);
            })
            ->latest()
            ->get();

        return $this->view([
            'inventaris' => $inventaris,
            'daftarTipe' => Inventaris::distinct('tipe')->pluck('tipe'),
        ]);
    }
};
?>

<div class="relative min-h-screen grid-pattern overflow-hidden">
    <div class="max-w-screen-xl mx-auto px-4 py-6 space-y-6">
        {{-- Header / Judul --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold text-stone-800 dark:text-stone-100">Inventaris</h1>

            {{-- Pencarian & Filter --}}
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-stone-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari nama barang atau tipe..."
                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-800 dark:text-stone-100 placeholder:text-stone-400 focus:outline-none focus:ring-1 focus:ring-sage-500" />
                </div>
                <select wire:model.live="filterTipe"
                    class="py-1.5 px-2.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-700 dark:text-stone-300 focus:outline-none focus:ring-1 focus:ring-sage-500">
                    <option value="">Semua Tipe</option>
                    @foreach ($daftarTipe as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Grid Kartu Inventaris --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse ($inventaris as $item)
                <div
                    class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">
                    {{-- Gambar --}}
                    <div
                        class="aspect-[4/3] bg-stone-100 dark:bg-stone-800 flex items-center justify-center overflow-hidden">
                        @if ($item->img_path)
                            <img src="{{ asset('storage/' . $item->img_path) }}" alt="{{ $item->nama_barang }}"
                                class="w-full h-full object-cover">
                        @else
                            <svg class="w-10 h-10 text-stone-300 dark:text-stone-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </div>

                    {{-- Informasi --}}
                    <div class="p-3 flex-1 flex flex-col gap-1.5">
                        <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-100 truncate"
                            title="{{ $item->nama_barang }}">
                            {{ $item->nama_barang }}
                        </h3>

                        <div class="flex items-center gap-2 text-[10px] text-stone-500 dark:text-stone-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="truncate">{{ $item->departemen?->nama_departemen ?? 'Umum' }}</span>
                        </div>

                        <div class="flex flex-wrap gap-1.5 mt-1">
                            {{-- Tipe --}}
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-sage-50 dark:bg-sage-900/40 text-sage-700 dark:text-sage-300 border border-sage-200 dark:border-sage-800">
                                {{ $item->tipe }}
                            </span>
                            {{-- Jumlah --}}
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                {{ $item->jumlah }} unit
                            </span>
                            {{-- Kondisi --}}
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium
                                    @if ($item->kondisi == 'baik') bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800
                                    @elseif($item->kondisi == 'rusak') bg-red-50 dark:bg-red-900/40 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800
                                    @else bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800 @endif">
                                {{ ucfirst($item->kondisi) }}
                            </span>
                            {{-- Warna --}}
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-400 border border-stone-200 dark:border-stone-700">
                                {{ $item->warna }}
                            </span>
                        </div>

                        {{-- Tombol Ajukan Peminjaman --}}
                        <div class="mt-auto pt-3 border-t border-stone-100 dark:border-stone-800">
                            @auth
                                <button @click="$dispatch('openModalPeminjaman', { id: {{ $item->id_inventaris }} })"
                                    @if (!$item->dpt_dipinjam) disabled @endif
                                    class="cursor-pointer w-full group relative flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-sage-600 to-sage-500 hover:from-sage-700 hover:to-sage-600 disabled:from-stone-300 disabled:to-stone-300 dark:disabled:from-stone-700 dark:disabled:to-stone-700 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    Ajukan Peminjaman
                                </button>
                            @endauth
                            @guest
                                <a href="{{ route('login') }}"
                                    class="cursor-poniter w-full group flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-sage-700 dark:text-sage-300 bg-sage-50 dark:bg-sage-900/40 hover:bg-sage-100 dark:hover:bg-sage-900/60 border border-sage-200 dark:border-sage-800 rounded-xl transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Masuk untuk Meminjam
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-stone-500 dark:text-stone-400">Tidak ada inventaris yang tersedia.</p>
            @endforelse
        </div>
        <livewire:modal-peminjaman />
    </div>
</div>
