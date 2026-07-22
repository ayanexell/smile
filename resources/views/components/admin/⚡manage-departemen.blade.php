<?php

use Livewire\Component;
use App\Models\Departemens;

new class extends Component
{
    public $search = '';
    public $nama_departemen;
    public $singkatan;
    public $showDeleteModal = false;
    public $departemen;


    public function confirmDelete(Departemens $departemen): void
    {
        $this->departemen = $departemen;
        $this->showDeleteModal = true;
    }

    public function deleteDepartemen(): void
    {
        $this->departemen->delete();
        $this->showDeleteModal = false;
        $this->departemen = null;
        session()->flash('success', 'Departemen berhasil dihapus.');
    }

    public function render()
    {
        $departemens = Departemens::query()->when(
                $this->search,
                fn($q) => $q
                    ->where('nama_departemen', 'like', "%{$this->search}%")
                    ->orWhere('singkatan', 'like', "%{$this->search}%"),
            )
            ->latest()
            ->paginate(10);

        return $this->view([
            'departemens' => $departemens,
        ]);
    }
};
?>

<div class="min-h-screen bg-stone-100 dark:bg-stone-950">

    {{-- ── PAGE HEADER ── --}}
    <div class="bg-white dark:bg-stone-900 border-b border-stone-200 dark:border-stone-800 px-5 py-3.5">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span
                        class="font-mono text-[9px] tracking-widest uppercase text-sage-600 dark:text-sage-400">{{ __("Manajemen") }}</span>
                </div>
                <h1 class="font-display text-xl font-semibold text-stone-800 dark:text-stone-100">{{ __("Daftar Departemen") }}</h1>
                <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">{{ __("Kelola semua departemen sistem SMILE") }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-screen-xl mt-2 space-y-2 mx-auto">

        {{-- ── FLASH MESSAGE ── --}}
        @if (session('success'))
            <div
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-sage-50 dark:bg-sage-950 border border-sage-200 dark:border-sage-800 text-sage-700 dark:text-sage-300 text-xs">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ── FILTER BAR ── --}}
        <div class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 px-3 py-3">
            <div class="flex flex-col sm:flex-row gap-2">

                {{-- Search --}}
                <div class="relative flex-1">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-stone-400 dark:text-stone-500 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari nama, NIK, atau email…"
                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-800 dark:text-stone-100 placeholder:text-stone-400 dark:placeholder:text-stone-500 focus:outline-none focus:ring-1 focus:ring-sage-500 focus:border-transparent transition" />
                </div>

                <a href="#"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Dept.
                </a>

            </div>
        </div>

        {{-- ── TABLE CARD ── --}}
        <div
            class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden px-4">

            {{-- Table meta --}}
            <div class="py-2 border-b border-stone-100 dark:border-stone-800 flex items-center justify-between">
                <span class="text-[10px] font-mono text-stone-400 dark:text-stone-500 uppercase tracking-wider">
                    {{ $departemens->total() }} {{ __("departemen ditemukan") }}
                </span>
                <div wire:loading class="flex items-center gap-1 text-[11px] text-sage-600 dark:text-sage-400">
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    {{ __("Memuat…") }}
                </div>
            </div>

            {{-- Scrollable table wrapper --}}
            <div
                class="overflow-x-auto border dark:border-stone-800 rounded-xl bg-white dark:bg-stone-900 shadow-sm">
                <table class="w-full text-[11px] text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-stone-50 dark:bg-stone-800/50 border-b border-stone-200 dark:border-stone-800 text-stone-500 dark:text-stone-400 font-semibold uppercase tracking-wider">
                            <th class="px-2.5 py-1.5 w-6 text-center">#</th>
                            <th class="px-2.5 py-1.5">{{ __("Departemen") }}</th>
                            <th class="px-2.5 py-1.5 hidden sm:table-cell">{{ __("Singkatan") }}</th>
                            <th class="px-2.5 py-1.5 hidden md:table-cell">{{ __("Deskripsi") }}</th>
                            <th class="px-2.5 py-1.5 text-right w-20">{{ __("Aksi") }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                        @forelse ($departemens as $departemen)
                            <tr class="hover:bg-stone-50 dark:hover:bg-stone-800/30 transition-colors group">

                                {{-- No --}}
                                <td class="px-2.5 py-1.5 text-stone-400 dark:text-stone-600 font-mono text-center">
                                    {{ $loop->iteration + ($departemens->currentPage() - 1) * $departemens->perPage() }}
                                </td>

                                {{-- Nama, Avatar + Email (Digabung agar hemat space) --}}
                                <td class="px-2.5 py-1.5">
                                    <div class="truncate">
                                        <div class="font-medium text-stone-800 dark:text-stone-200 truncate">
                                            {{ $departemen->nama_departemen }}</div>
                                    </div>
                                </td>

                                <td class="px-2.5 py-1.5">
                                    <div class="truncate">
                                        <div class="font-medium text-stone-800 dark:text-stone-200 truncate">
                                            {{ $departemen->singkatan }}</div>
                                    </div>
                                </td>

                                <td class="px-2.5 py-1.5">
                                    <div class="truncate">
                                        <div class="font-medium text-stone-800 dark:text-stone-200 truncate">
                                            {{ $departemen->deskripsi }}</div>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-2.5 py-1.5 text-right">
                                    <div class="flex items-center justify-end" x-data="{ open: false }">
                                        <div class="relative inline-block text-left">

                                            {{-- Tombol Titik Tiga --}}
                                            <button @click="open = !open" @click.outside="open = false"
                                                class="cursor-pointer p-1 rounded-md text-stone-400 hover:text-stone-700 dark:hover:text-stone-200 hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors focus:outline-none"
                                                title="Menu Aksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                                </svg>
                                            </button>

                                            {{-- Menu Dropdown Konten --}}
                                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 mt-1 w-32 origin-top-right rounded-md border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-30"
                                                style="display: none;">
                                                <div class="p-1 space-y-0.5">

                                                    {{-- Edit --}}
                                                    <button
                                                        x-on:click="
                                                            $flux.modal('edit-admin-modal').show();
                                                            $wire.updateAdmin({{ $departemen->id_departemen }});
                                                            open = false;"
                                                        @click="open = false"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5 text-xs text-sage-600 dark:text-sage-400 hover:bg-sage-50 dark:hover:bg-sage-950/30 rounded transition-colors text-left">
                                                        <svg class="w-3.5 h-3.5 text-sage-500" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                        </svg>
                                                        Edit
                                                    </button>

                                                    <flux:separator />

                                                    {{-- Hapus --}}
                                                    <button wire:click="confirmDelete({{ $departemen->id_departemen }})"
                                                        @click="open = false"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded transition-colors text-left">
                                                        <svg class="w-3.5 h-3.5 text-red-400" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                        Hapus
                                                    </button>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-2.5 py-10 text-center">
                                    <div class="flex flex-col items-center gap-1.5 text-stone-400">
                                        <p class="text-xs font-medium">{{ __("Tidak ada pengguna ditemukan") }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-stone-100 dark:border-stone-800">
                {{ $departemens->links() }}
            </div>
        </div>

    </div>

    <livewire:admin.departemens.edit-departemen />

    {{-- ── DELETE MODAL ── --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 dark:bg-black/60 backdrop-blur-sm"
            wire:click.self="$set('showDeleteModal', false)">
            <div
                class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 shadow-2xl w-full max-w-xs p-5">
                <div class="flex items-start gap-3">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-red-50 dark:bg-red-950 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-100 mb-0.5">Hapus Departemen</h3>
                        <p class="text-xs text-stone-500 dark:text-stone-400">Data Departemen akan dihapus permanen dari
                            sistem.</p>
                    </div>
                </div>
                <div class="flex gap-2 mt-5">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 px-3 py-2 text-xs font-medium rounded-lg border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-800 transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteDepartemen"
                        class="flex-1 px-3 py-2 text-xs font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
