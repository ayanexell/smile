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

<div class="grid-pattern relative min-h-screen overflow-hidden">
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6">
        {{-- Header / Judul --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-stone-800 dark:text-stone-100">Inventaris</h1>

            {{-- Pencarian & Filter --}}
            <div class="flex flex-col gap-2 sm:flex-row">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-stone-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari nama barang atau tipe..."
                        class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 py-1.5 pl-8 pr-3 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                </div>
                <select wire:model.live="filterTipe"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">Semua Tipe</option>
                    @foreach ($daftarTipe as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Grid Kartu Inventaris --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($inventaris as $item)
                <div
                    class="flex flex-col overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-stone-800 dark:bg-stone-900">
                    {{-- Gambar --}}
                    <div
                        class="aspect-4/3 flex items-center justify-center overflow-hidden bg-stone-100 dark:bg-stone-800">
                        @if ($item->img_path)
                            <img src="{{ asset('storage/' . $item->img_path) }}" alt="{{ $item->nama_barang }}"
                                class="h-full w-full object-cover">
                        @else
                            <svg class="h-10 w-10 text-stone-300 dark:text-stone-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </div>

                    {{-- Informasi --}}
                    <div class="flex flex-1 flex-col gap-1.5 p-3">
                        <h3 class="truncate text-sm font-semibold text-stone-800 dark:text-stone-100"
                            title="{{ $item->nama_barang }}">
                            {{ $item->nama_barang }}
                        </h3>

                        <div class="flex items-center gap-2 text-[10px] text-stone-500 dark:text-stone-400">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="truncate">{{ $item->user->departemen?->nama_departemen ?? 'P2AL' }}</span>
                        </div>

                        <div class="mt-1 flex flex-wrap gap-1.5">
                            {{-- Tipe --}}
                            <span
                                class="bg-sage-50 dark:bg-sage-900/40 text-sage-700 dark:text-sage-300 border-sage-200 dark:border-sage-800 inline-flex items-center rounded border px-1.5 py-0.5 text-[9px] font-medium">
                                {{ $item->tipe }}
                            </span>
                            {{-- Jumlah --}}
                            <span
                                class="inline-flex items-center rounded border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[9px] font-medium text-blue-700 dark:border-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                {{ $item->jumlah }} unit
                            </span>
                            {{-- Kondisi --}}
                            <span
                                class="@if ($item->kondisi == 'baik') dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800
                                    @elseif($item->kondisi == 'rusak')
                                    @else bg-amber-50 @endif inline-flex items-center rounded px-1.5 py-0.5 text-[9px] font-medium">
                                {{ ucfirst($item->kondisi) }}
                            </span>
                            {{-- Warna --}}
                            <span
                                class="inline-flex items-center rounded border border-stone-200 bg-stone-100 px-1.5 py-0.5 text-[9px] font-medium text-stone-600 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-400">
                                {{ $item->warna }}
                            </span>
                        </div>

                        {{-- Tombol Ajukan Peminjaman --}}
                        <div class="mt-auto border-t border-stone-100 pt-3 dark:border-stone-800">
                            @auth
                                <button @click="$dispatch('openModalPeminjaman', { id: {{ $item->id_inventaris }} })"
                                    @if (!$item->dpt_dipinjam) disabled @endif
                                    class="from-sage-600 to-sage-500 hover:from-sage-700 hover:to-sage-600 bg-linear-to-r group relative flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:shadow-lg disabled:cursor-not-allowed disabled:from-stone-300 disabled:to-stone-300 disabled:opacity-70 dark:disabled:from-stone-700 dark:disabled:to-stone-700">
                                    <svg class="h-4 w-4 transition-transform group-hover:scale-110" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    Ajukan Peminjaman
                                </button>
                            @endauth
                            @guest
                                <a href="{{ route('login') }}"
                                    class="cursor-poniter text-sage-700 dark:text-sage-300 bg-sage-50 dark:bg-sage-900/40 hover:bg-sage-100 dark:hover:bg-sage-900/60 border-sage-200 dark:border-sage-800 group flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-2 text-xs font-semibold transition-all duration-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
