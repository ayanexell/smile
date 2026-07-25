<?php

use Livewire\Component;
use App\Models\Peminjaman;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;

new #[Title('Manajemen Peminjaman')] class extends Component {
    use WithPagination;
    public $search = '';
    public $peminjaman;
    public $showDeleteModal = false;

    public function acceptPeminjaman(Peminjaman $peminjaman)
    {
        $peminjaman->update([
            'status' => 'dipinjam',
        ]);
        session()->flash('success', 'Peminjaman berhasil diterima.');
    }

    public function declinePeminjaman(Peminjaman $peminjaman)
    {
        $peminjaman->update([
            'status' => 'declined',
        ]);
        session()->flash('success', 'Peminjaman berhasil ditolak.');
    }

    public function returnPeminjaman(Peminjaman $peminjaman)
    {
        $peminjaman->update([
            'status' => 'dikembalikan',
        ]);
        session()->flash('success', 'Peminjaman berhasil dikembalikan.');
    }

    public function pendingPeminjaman(Peminjaman $peminjaman)
    {
        $peminjaman->update([
            'status' => 'pending',
        ]);
        session()->flash('success', 'Peminjaman berhasil dikembalikan ke status pending.');
    }

    public function confirmDelete(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
        $this->showDeleteModal = true;
    }

    public function deletePeminjaman()
    {
        $this->peminjaman->delete();
        $this->reset('peminjaman', 'showDeleteModal');
        session()->flash('success', 'Peminjaman berhasil dihapus.');
    }

    public function render()
    {
        $peminjamans = Peminjaman::with(['user', 'inventaris'])
            ->when($this->search, function ($query) {
                $query
                    ->whereHas('user', function ($q) {
                        $q->where('nama_lengkap', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('inventaris', function ($q) {
                        $q->where('nama_barang', 'like', '%' . $this->search . '%');
                    });
            })
            ->paginate(10);
        return $this->view([
            'peminjamans' => $peminjamans,
        ]);
    }
};
?>

<div>
    {{-- ── PAGE HEADER ── --}}
    <div class="bg-white dark:bg-stone-900 border-b border-stone-200 dark:border-stone-800 px-5 py-3.5">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span
                        class="font-mono text-[9px] tracking-widest uppercase text-sage-600 dark:text-sage-400">Manajemen</span>
                </div>
                <h1 class="font-display text-xl font-semibold text-stone-800 dark:text-stone-100">Daftar Peminjaman</h1>
                <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">Kelola semua peminjaman sistem SMILE</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mt-2 space-y-2 mx-auto">

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
                    <input wire:model.live="search" type="text" placeholder="Cari nama peminjam, nama barang..."
                        class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg
                              border border-stone-200 dark:border-stone-700
                              bg-stone-50 dark:bg-stone-800
                              text-stone-800 dark:text-stone-100
                              placeholder:text-stone-400 dark:placeholder:text-stone-500
                              focus:outline-none focus:ring-1 focus:ring-sage-500 focus:border-transparent
                              transition" />
                </div>

            </div>
        </div>


        {{-- ── TABLE CARD ── --}}
        <div
            class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden px-4">

            {{-- Meta --}}
            <div class="py-2 border-b border-stone-100 dark:border-stone-800 flex items-center justify-between">
                <span class="text-[10px] font-mono text-stone-400 dark:text-stone-500 uppercase tracking-wider">
                    {{ $peminjamans->count() }} peminjaman ditemukan
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
                            <th class="px-2.5 py-1.5">Peminjam</th>
                            <th class="px-2.5 py-1.5">Tgl. Peminjaman</th>
                            <th class="px-2.5 py-1.5">Tgl. Pengembalian</th>
                            <th class="px-2.5 py-1.5 text-center">Jumlah</th>
                            <th class="px-2.5 py-1.5 text-center">Status</th>
                            <th class="px-2.5 py-1.5 text-right w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                        @forelse ($peminjamans as $peminjaman)
                            <tr wire:key="peminjaman-{{ $peminjaman->id_peminjaman }}"
                                class="hover:bg-stone-50 dark:hover:bg-stone-800/30 transition-colors group">

                                {{-- No --}}
                                <td class="px-2.5 py-1.5 text-stone-400 dark:text-stone-600 font-mono text-center">
                                    {{ $loop->iteration + ($peminjamans->currentPage() - 1) * $peminjamans->perPage() }}
                                </td>

                                {{-- Gambar + Nama Barang + Departemen --}}
                                <td class="px-2.5 py-1.5">
                                    <div class="flex items-center gap-2 max-w-[180px] sm:max-w-xs">
                                        {{-- Thumbnail gambar atau placeholder --}}
                                        @if ($peminjaman->inventaris->img_path)
                                            <img src="{{ Storage::url($peminjaman->inventaris->img_path) }}"
                                                alt="{{ $peminjaman->inventaris->nama_barang }}"
                                                class="w-7 h-7 rounded-lg object-cover flex-shrink-0
                                                    border border-stone-200 dark:border-stone-700" />
                                        @else
                                            <div
                                                class="w-7 h-7 rounded-lg flex-shrink-0
                                                    bg-sage-100 dark:bg-sage-900/40
                                                    border border-stone-200 dark:border-stone-700
                                                    flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5 text-sage-500 dark:text-sage-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="truncate">
                                            <div class="font-medium text-stone-800 dark:text-stone-200 truncate">
                                                {{ $peminjaman->inventaris->nama_barang }}
                                            </div>
                                            <div class="text-[10px] text-stone-400 dark:text-stone-500 truncate">
                                                {{ $peminjaman->inventaris->departemen->nama_departemen ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nama Peminjam --}}
                                <td class="px-2.5 py-1.5 text-stone-500 dark:text-stone-400 truncate">
                                    {{ $peminjaman->user->nama_lengkap ?? '-' }}
                                </td>

                                {{-- Tgl. Peminjaman --}}
                                <td class="px-2.5 py-1.5 text-center font-mono text-stone-600 dark:text-stone-400">
                                    {{ Carbon::parse($peminjaman->tgl_peminjaman)->format('d M Y') }}
                                </td>

                                {{-- Tgl. Pengembalian --}}
                                <td class="px-2.5 py-1.5 text-center font-mono text-stone-600 dark:text-stone-400">
                                    {{ Carbon::parse($peminjaman->tgl_pengembalian)->format('d M Y') }}
                                </td>

                                {{-- Jumlah --}}
                                <td class="px-2.5 py-1.5 text-center font-mono text-stone-600 dark:text-stone-400">
                                    {{ $peminjaman->jumlah ? $peminjaman->jumlah : '-' }}
                                </td>

                                {{-- Status --}}
                                <td class="px-2.5 py-1.5">
                                    @php
                                        [$lbl, $cls] = match ($peminjaman->status ?? '') {
                                            'pending' => [
                                                'Pending',
                                                'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400',
                                            ],
                                            'declined' => [
                                                'Ditolak',
                                                'bg-rose-100 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400',
                                            ],
                                            'dipinjam' => [
                                                'Dipinjam',
                                                'bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400',
                                            ],
                                            'dikembalikan' => [
                                                'Dikembalikan',
                                                'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400',
                                            ],
                                            'terlambat' => [
                                                'Terlambat',
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

                                                    {{-- Accept --}}
                                                    <button
                                                        x-on:click="
                                                        $wire.acceptPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5
                                                        text-xs text-green-500 dark:text-green-400
                                                        hover:bg-green-50 dark:hover:bg-green-950/30
                                                        rounded transition-colors text-left">
                                                        <svg fill="currentColor" class="w-3.5 h-3.5"
                                                            viewBox="0 0 24 24" id="check-mark-circle-2"
                                                            xmlns="http://www.w3.org/2000/svg" class="icon line">
                                                            <path id="primary"
                                                                d="M20.94,11A8.26,8.26,0,0,1,21,12a9,9,0,1,1-9-9,8.83,8.83,0,0,1,4,1"
                                                                style="fill: none; stroke: rgb(3, 251, 44); stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5;">
                                                            </path>
                                                            <polyline id="primary-2" data-name="primary"
                                                                points="21 5 12 14 8 10"
                                                                style="fill: none; stroke: rgb(3, 251, 44); stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5;">
                                                            </polyline>
                                                        </svg>
                                                        Accept
                                                    </button>
                                                    {{-- Decline --}}
                                                    <button
                                                        x-on:click="
                                                        $wire.declinePeminjaman({{ $peminjaman->id_peminjaman }})"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5
                                                        text-xs text-rose-600 dark:text-rose-400
                                                        hover:bg-rose-50 dark:hover:bg-rose-950/40
                                                        rounded transition-colors text-left">
                                                        <svg class="w-3.5 h-3.5 color-rose-600" viewBox="0 0 24 24"
                                                            role="img" xmlns="http://www.w3.org/2000/svg"
                                                            aria-labelledby="cancelIconTitle" stroke="currentColor"
                                                            stroke-width="1" stroke-linecap="square"
                                                            stroke-linejoin="miter" fill="none">
                                                            <title id="cancelIconTitle">Cancel</title>
                                                            <path
                                                                d="M15.5355339 15.5355339L8.46446609 8.46446609M15.5355339 8.46446609L8.46446609 15.5355339" />
                                                            <path
                                                                d="M4.92893219,19.0710678 C1.02368927,15.1658249 1.02368927,8.83417511 4.92893219,4.92893219 C8.83417511,1.02368927 15.1658249,1.02368927 19.0710678,4.92893219 C22.9763107,8.83417511 22.9763107,15.1658249 19.0710678,19.0710678 C15.1658249,22.9763107 8.83417511,22.9763107 4.92893219,19.0710678 Z" />
                                                        </svg>
                                                        Decline
                                                    </button>
                                                    {{-- Pending --}}
                                                    <button
                                                        x-on:click="
                                                            $wire.pendingPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5
                                                            text-xs text-orange-500 dark:text-orange-400
                                                            hover:bg-orange-50 dark:hover:bg-orange-950/40
                                                            rounded transition-colors text-left">
                                                        <svg class="w-3 h-3 color-orange-400" fill="currentColor"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            shape-rendering="geometricPrecision"
                                                            text-rendering="geometricPrecision"
                                                            image-rendering="optimizeQuality" fill-rule="evenodd"
                                                            clip-rule="evenodd" viewBox="0 0 502 511.82">
                                                            <path fill-rule="nonzero"
                                                                d="M279.75 471.21c14.34-1.9 25.67 12.12 20.81 25.75-2.54 6.91-8.44 11.76-15.76 12.73a260.727 260.727 0 0 1-50.81 1.54c-62.52-4.21-118.77-31.3-160.44-72.97C28.11 392.82 0 330.04 0 260.71 0 191.37 28.11 128.6 73.55 83.16S181.76 9.61 251.1 9.61c24.04 0 47.47 3.46 69.8 9.91a249.124 249.124 0 0 1 52.61 21.97l-4.95-12.96c-4.13-10.86 1.32-23.01 12.17-27.15 10.86-4.13 23.01 1.32 27.15 12.18L428.8 68.3a21.39 21.39 0 0 1 1.36 6.5c1.64 10.2-4.47 20.31-14.63 23.39l-56.03 17.14c-11.09 3.36-22.8-2.9-26.16-13.98-3.36-11.08 2.9-22.8 13.98-26.16l4.61-1.41a210.71 210.71 0 0 0-41.8-17.12c-18.57-5.36-38.37-8.24-59.03-8.24-58.62 0-111.7 23.76-150.11 62.18-38.42 38.41-62.18 91.48-62.18 150.11 0 58.62 23.76 111.69 62.18 150.11 34.81 34.81 81.66 57.59 133.77 61.55 14.9 1.13 30.23.76 44.99-1.16zm-67.09-312.63c0-10.71 8.69-19.4 19.41-19.4 10.71 0 19.4 8.69 19.4 19.4V276.7l80.85 35.54c9.8 4.31 14.24 15.75 9.93 25.55-4.31 9.79-15.75 14.24-25.55 9.93l-91.46-40.2c-7.35-2.77-12.58-9.86-12.58-18.17V158.58zm134.7 291.89c-15.62 7.99-13.54 30.9 3.29 35.93 4.87 1.38 9.72.96 14.26-1.31 12.52-6.29 24.54-13.7 35.81-22.02 5.5-4.1 8.36-10.56 7.77-17.39-1.5-15.09-18.68-22.74-30.89-13.78a208.144 208.144 0 0 1-30.24 18.57zm79.16-69.55c-8.84 13.18 1.09 30.9 16.97 30.2 6.21-.33 11.77-3.37 15.25-8.57 7.86-11.66 14.65-23.87 20.47-36.67 5.61-12.64-3.13-26.8-16.96-27.39-7.93-.26-15.11 4.17-18.41 11.4-4.93 10.85-10.66 21.15-17.32 31.03zm35.66-99.52c-.7 7.62 3 14.76 9.59 18.63 12.36 7.02 27.6-.84 29.05-14.97 1.33-14.02 1.54-27.9.58-41.95-.48-6.75-4.38-12.7-10.38-15.85-13.46-6.98-29.41 3.46-28.34 18.57.82 11.92.63 23.67-.5 35.57zM446.1 177.02c4.35 10.03 16.02 14.54 25.95 9.96 9.57-4.4 13.86-15.61 9.71-25.29-5.5-12.89-12.12-25.28-19.69-37.08-9.51-14.62-31.89-10.36-35.35 6.75-.95 5.03-.05 9.94 2.72 14.27 6.42 10.02 12 20.44 16.66 31.39z" />
                                                        </svg>
                                                        Pending
                                                    </button>
                                                    {{-- Dikembalikan --}}
                                                    <button
                                                        x-on:click="
                                                            $wire.returnPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                        class="cursor-pointer w-full flex items-center gap-2 px-2.5 py-1.5
                                                            text-xs text-blue-600 dark:text-blue-400
                                                            hover:bg-blue-50 dark:hover:bg-blue-950/40
                                                            rounded transition-colors text-left">
                                                        <svg fill="currentColor" class="w-3 h-3" version="1.1"
                                                            id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            viewBox="0 0 384.97 384.97" xml:space="preserve">
                                                            <g>
                                                                <g id="Arrow_Left_Circle">
                                                                    <path
                                                                        d="M192.485,0C86.185,0,0,86.185,0,192.485C0,298.797,86.185,384.97,192.485,384.97 c106.312,0,192.485-86.173,192.485-192.485C384.97,86.185,298.797,0,192.485,0z M192.485,360.909 c-93.018,0-168.424-75.406-168.424-168.424S99.467,24.061,192.485,24.061s168.424,75.406,168.424,168.424 S285.503,360.909,192.485,360.909z" />
                                                                    <path
                                                                        d="M300.758,180.226H113.169l62.558-63.46c4.692-4.74,4.692-12.439,0-17.179c-4.704-4.74-12.319-4.74-17.011,0l-82.997,84.2 c-2.25,2.25-3.537,5.414-3.537,8.59c0,3.164,1.299,6.328,3.525,8.59l82.997,84.2c4.704,4.752,12.319,4.74,17.011,0 c4.704-4.752,4.704-12.439,0-17.191l-62.558-63.46h187.601c6.641,0,12.03-5.438,12.03-12.151 C312.788,185.664,307.398,180.226,300.758,180.226z" />
                                                                </g>
                                                                <g></g>
                                                                <g></g>
                                                                <g></g>
                                                                <g></g>
                                                                <g></g>
                                                                <g></g>
                                                            </g>
                                                        </svg>
                                                        Dikembalikan
                                                    </button>

                                                    <flux:separator />

                                                    {{-- Hapus --}}
                                                    <button
                                                        wire:click="confirmDelete({{ $peminjaman->id_peminjaman }})"
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
                {{ $peminjamans->links('pagination::tailwind') }}
            </div>

        </div>
        {{-- ── DELETE MODAL ── --}}
        @if ($showDeleteModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 dark:bg-black/60 backdrop-blur-sm"
                wire:click.self="$set('showDeleteModal', false)">
                <div
                    class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 shadow-2xl w-full max-w-xs p-5">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-full bg-red-50 dark:bg-red-950 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-100 mb-0.5">Hapus
                                Peminjaman</h3>
                            <p class="text-xs text-stone-500 dark:text-stone-400">Data peminjaman akan dihapus permanen
                                dari
                                sistem.</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-5">
                        <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 px-3 py-2 text-xs font-medium rounded-lg border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-800 transition-colors">
                            Batal
                        </button>
                        <button wire:click="deletePeminjaman"
                            class="flex-1 px-3 py-2 text-xs font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
