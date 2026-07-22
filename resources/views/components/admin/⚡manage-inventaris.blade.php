<?php

use Livewire\Component;
use App\Models\Inventaris;

new class extends Component {

    use Livewire\WithPagination;

    public $search = '';
    public $filterKondisi = '';
    public $filterTipe = '';
    public $filterDptDipinjam = '';

    public function editInventaris($id)
    {
        $this->dispatch('edit-inventaris', $id);
    }

    public function render()
    {
        $inventaris = Inventaris::query()
            ->when(
                $this->search,
                fn($q) => $q
                    ->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('tipe', 'like', "%{$this->search}%")
            )
            ->when($this->filterKondisi, fn($q) => $q->where('kondisi', $this->filterKondisi))
            ->when($this->filterTipe, fn($q) => $q->where('tipe', $this->filterTipe))
            ->when($this->filterDptDipinjam, fn($q) => $q->where('dpt_dipinjam', $this->filterDptDipinjam))
            ->latest()
            ->paginate(10);
        // dd($inventaris);
        return $this->view([
            'inventaris' => $inventaris,
        ]);
    }
};
?>

<div>
    {{-- ── PAGE HEADER ── --}}
    <div class="bg-white dark:bg-stone-900 border-b border-stone-200 dark:border-stone-800 px-5 py-3.5">
        <div class="max-w-screen-xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span
                        class="font-mono text-[9px] tracking-widest uppercase text-sage-600 dark:text-sage-400">Manajemen</span>
                </div>
                <h1 class="font-display text-xl font-semibold text-stone-800 dark:text-stone-100">Daftar Inventaris</h1>
                <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">Kelola semua inventaris sistem SMILE</p>
            </div>
        </div>
    </div>

    <div class="max-w-screen-xl mt-2 space-y-2 mx-auto">

        {{-- ── FLASH MESSAGE ── --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="fixed top-5 left-1/2 -translate-x-1/2 z-[9999] w-full max-w-sm px-4" style="display: none;">
                <div
                    class="flex items-center gap-2.5 pl-3 pr-2.5 py-2
                        bg-white dark:bg-stone-900
                        border border-emerald-100 dark:border-emerald-950/60
                        rounded-lg shadow-xl shadow-stone-200/50 dark:shadow-none select-none">
                    <div class="flex-shrink-0 text-emerald-500 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 text-[11px] font-medium text-stone-700 dark:text-stone-300 leading-normal">
                        {{ session('success') }}
                    </div>
                    <button @click="show = false"
                        class="flex-shrink-0 p-1 text-stone-400 hover:text-stone-600 dark:hover:text-stone-200
                               rounded transition-colors focus:outline-none">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif


        {{-- ── FILTER BAR ── --}}
        <div class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 px-3 py-3">
            <div class="flex flex-col sm:flex-row gap-2">

                {{-- Search --}}
                <div class="relative flex-1">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5
                            text-stone-400 dark:text-stone-500 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari nama barang, tipe, atau warna…"
                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg
                              border border-stone-200 dark:border-stone-700
                              bg-stone-50 dark:bg-stone-800
                              text-stone-800 dark:text-stone-100
                              placeholder:text-stone-400 dark:placeholder:text-stone-500
                              focus:outline-none focus:ring-1 focus:ring-sage-500 focus:border-transparent
                              transition" />
                </div>

                {{-- Filter Kondisi --}}
                <select wire:model.live="filterKondisi"
                    class="py-1.5 px-2.5 text-xs rounded-lg
                           border border-stone-200 dark:border-stone-700
                           bg-stone-50 dark:bg-stone-800
                           text-stone-700 dark:text-stone-300
                           focus:outline-none focus:ring-1 focus:ring-sage-500 transition">
                    <option value="">Semua Kondisi</option>
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="servis">Servis</option>
                </select>

                {{-- Filter Tipe --}}
                <select wire:model.live="filterTipe"
                    class="py-1.5 px-2.5 text-xs rounded-lg
                           border border-stone-200 dark:border-stone-700
                           bg-stone-50 dark:bg-stone-800
                           text-stone-700 dark:text-stone-300
                           focus:outline-none focus:ring-1 focus:ring-sage-500 transition">
                    <option value="">Semua Tipe</option>
                    @foreach ($tipeList ?? [] as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>

                {{-- Filter Dapat Dipinjam --}}
                <select wire:model.live="filterDptDipinjam"
                    class="py-1.5 px-2.5 text-xs rounded-lg
                           border border-stone-200 dark:border-stone-700
                           bg-stone-50 dark:bg-stone-800
                           text-stone-700 dark:text-stone-300
                           focus:outline-none focus:ring-1 focus:ring-sage-500 transition">
                    <option value="">Semua</option>
                    <option value="1">Dapat Dipinjam</option>
                    <option value="0">Tidak Dapat Dipinjam</option>
                </select>

                {{-- Tombol Tambah --}}
                <button @click="$dispatch('add-inventaris-modal')"
                    class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg
                           bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400
                           text-white transition-all shadow-sm hover:shadow hover:shadow-sage-600/20
                           whitespace-nowrap flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Barang
                </button>

            </div>
        </div>


        {{-- ── TABLE CARD ── --}}
        <div
            class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden px-4">

            {{-- Meta --}}
            <div class="py-2 border-b border-stone-100 dark:border-stone-800 flex items-center justify-between">
                <span class="text-[10px] font-mono text-stone-400 dark:text-stone-500 uppercase tracking-wider">
                    {{ $inventaris->count() }} barang ditemukan
                </span>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto border dark:border-stone-800 rounded-xl bg-white dark:bg-stone-900 shadow-sm">
                <table class="w-full text-[11px] text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-stone-50 dark:bg-stone-800/50
                               border-b border-stone-200 dark:border-stone-800
                               text-stone-500 dark:text-stone-400
                               font-semibold uppercase tracking-wider">
                            <th class="px-2.5 py-1.5 w-6 text-center">#</th>
                            <th class="px-2.5 py-1.5">Barang</th>
                            <th class="px-2.5 py-1.5 hidden sm:table-cell">Tipe</th>
                            <th class="px-2.5 py-1.5 hidden md:table-cell w-16 text-center">Jumlah</th>
                            <th class="px-2.5 py-1.5 hidden sm:table-cell">Kondisi</th>
                            <th class="px-2.5 py-1.5 hidden lg:table-cell">Warna</th>
                            <th class="px-2.5 py-1.5 hidden md:table-cell text-center">Dipinjam</th>
                            <th class="px-2.5 py-1.5 text-right w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                        @forelse ($inventaris as $item)
                            <tr wire:key="inventaris-{{ $item->id }}"
                                class="hover:bg-stone-50 dark:hover:bg-stone-800/30 transition-colors group">

                                {{-- No --}}
                                <td class="px-2.5 py-1.5 text-stone-400 dark:text-stone-600 font-mono text-center">
                                    {{ $loop->iteration + ($inventaris->currentPage() - 1) * $inventaris->perPage() }}
                                </td>

                                {{-- Gambar + Nama Barang --}}
                                <td class="px-2.5 py-1.5">
                                    <div class="flex items-center gap-2 max-w-[180px] sm:max-w-xs">
                                        {{-- Thumbnail gambar atau placeholder --}}
                                        @if ($item->img_path)
                                            <img src="{{ Storage::url($item->img_path) }}"
                                                alt="{{ $item->nama_barang }}"
                                                class="w-7 h-7 rounded-lg object-cover flex-shrink-0
                                                    border border-stone-200 dark:border-stone-700" />
                                        @else
                                            <div
                                                class="w-7 h-7 rounded-lg flex-shrink-0
                                                    bg-sage-100 dark:bg-sage-900/40
                                                    border border-stone-200 dark:border-stone-700
                                                    flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5 text-sage-500 dark:text-sage-400"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="truncate">
                                            <div class="font-medium text-stone-800 dark:text-stone-200 truncate">
                                                {{ $item->nama_barang }}
                                            </div>
                                            <div class="text-[10px] text-stone-400 dark:text-stone-500 truncate">
                                                Oleh: {{ $item->departemen->nama_departemen ?? '-'}}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Tipe --}}
                                <td
                                    class="px-2.5 py-1.5 text-stone-500 dark:text-stone-400 truncate hidden sm:table-cell">
                                    {{ $item->tipe ?? '-' }}
                                </td>

                                {{-- Jumlah --}}
                                <td
                                    class="px-2.5 py-1.5 text-center font-mono text-stone-600 dark:text-stone-400 hidden md:table-cell">
                                    {{ $item->jumlah }}
                                </td>

                                {{-- Kondisi Badge --}}
                                <td class="px-2.5 py-1.5 hidden sm:table-cell">
                                    @php
                                        [$lbl, $cls] = match ($item->kondisi ?? '') {
                                            'baik' => [
                                                'Baik',
                                                'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400',
                                            ],
                                            'rusak' => [
                                                'Rusak',
                                                'bg-rose-100 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400',
                                            ],
                                            'servis' => [
                                                'Servis',
                                                'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400',
                                            ],
                                            default => ['—', 'bg-stone-100 dark:bg-stone-800 text-stone-400'],
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $cls }}">
                                        {{ $lbl }}
                                    </span>
                                </td>

                                {{-- Warna --}}
                                <td class="px-2.5 py-1.5 hidden lg:table-cell">
                                    @if ($item->warna)
                                        <div class="flex items-center gap-1.5">
                                            {{-- Swatch warna (best-effort CSS color name / hex) --}}
                                            <span
                                                class="w-3 h-3 rounded-full border border-stone-300 dark:border-stone-600 flex-shrink-0"
                                                style="background-color: {{ $item->warna }};"></span>
                                            <span
                                                class="text-stone-500 dark:text-stone-400 capitalize truncate max-w-[80px]">
                                                {{ $item->warna }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-stone-300 dark:text-stone-600">—</span>
                                    @endif
                                </td>

                                {{-- Dapat Dipinjam --}}
                                <td class="px-2.5 py-1.5 text-center hidden md:table-cell">
                                    @if ($item->dpt_dipinjam)
                                        <span
                                            class="inline-flex items-center gap-1 text-[10px] font-medium
                                                 text-emerald-600 dark:text-emerald-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Ya
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-[10px] font-medium
                                                 text-stone-400 dark:text-stone-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Tidak
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-2.5 py-1.5 text-right">
                                    <div class="flex items-center justify-end" x-data="{ open: false }">
                                        <div class="relative inline-block text-left">

                                            {{-- Tombol titik tiga --}}
                                            <button @click="open = !open" @click.outside="open = false"
                                                class="cursor-pointer p-1 rounded-md
                                                       text-stone-400 hover:text-stone-700 dark:hover:text-stone-200
                                                       hover:bg-stone-100 dark:hover:bg-stone-800
                                                       transition-colors focus:outline-none"
                                                title="Menu Aksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                                </svg>
                                            </button>

                                            {{-- Dropdown menu --}}
                                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 mt-1 w-36 origin-top-right rounded-md
                                                    border border-stone-200 dark:border-stone-800
                                                    bg-white dark:bg-stone-900
                                                    shadow-lg ring-1 ring-black ring-opacity-5
                                                    focus:outline-none z-30"
                                                style="display: none;">
                                                <div class="p-1 space-y-0.5">

                                                    {{-- Detail --}}
                                                    <button
                                                        x-on:click="
                                                        $flux.modal('detail-inventaris-modal').show();
                                                        $wire.lihatDetail({{ $item->id }});
                                                        open = false;"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5
                                                           text-xs text-stone-600 dark:text-stone-400
                                                           hover:bg-stone-50 dark:hover:bg-stone-800/60
                                                           rounded transition-colors text-left">
                                                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        Detail
                                                    </button>

                                                    {{-- Edit --}}
                                                    <button
                                                        x-on:click="
                                                        $flux.modal('edit-inventaris-modal').show();
                                                        $wire.editInventaris({{ $item->id_inventaris }});
                                                        open = false;"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5
                                                           text-xs text-sage-600 dark:text-sage-400
                                                           hover:bg-sage-50 dark:hover:bg-sage-950/30
                                                           rounded transition-colors text-left">
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
                                                    <button wire:click="confirmDelete({{ $item->id }})"
                                                        @click="open = false"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5
                                                               text-xs text-rose-600 dark:text-rose-400
                                                               hover:bg-rose-50 dark:hover:bg-rose-950/40
                                                               rounded transition-colors text-left">
                                                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none"
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
                                <td colspan="8" class="px-2.5 py-10 text-center">
                                    <div class="flex flex-col items-center gap-2 text-stone-400">
                                        <svg class="w-8 h-8 text-stone-300 dark:text-stone-700" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <p class="text-xs font-medium">Tidak ada barang ditemukan</p>
                                        <p class="text-[10px] text-stone-300 dark:text-stone-600">
                                            Coba ubah filter atau tambah barang baru
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-stone-100 dark:border-stone-800">
                {{ $inventaris->links('pagination::tailwind') }}
            </div>

        </div>
        <livewire:admin.inventaris.add-inventaris  />
    </div>
</div>
