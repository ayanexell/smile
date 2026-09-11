<?php

use Livewire\Component;
use App\Models\Peminjaman;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Illuminate\Validation\ValidationException;

new #[Title('Manajemen Peminjaman')] class extends Component {
    use WithPagination;
    public $search = '';
    public $status;
    public $peminjaman;
    public $showDeleteModal = false;

    public function acceptPeminjaman(Peminjaman $peminjaman)
    {
        DB::transaction(function () use ($peminjaman) {
            $oldStatus = $peminjaman->status;

            if ($peminjaman->inventaris->jumlah < $peminjaman->jumlah) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Stok tidak mencukupi.',
                ]);
            }

            $peminjaman->update(['status' => 'dipinjam']);

            if ($oldStatus === 'menunggu') {
                $peminjaman->inventaris()->decrement('jumlah', $peminjaman->jumlah);
                $peminjaman->inventaris()->increment('frequensi_peminjaman', 1);
            }

            $peminjaman->sendStatusNotification('Diterima');
            session()->flash('success', 'Peminjaman berhasil diterima.');
        });
    }

    public function declinePeminjaman(Peminjaman $peminjaman)
    {
        DB::transaction(function () use ($peminjaman) {
            $oldStatus = $peminjaman->status;
            $peminjaman->update(['status' => 'ditolak']);

            if (in_array($oldStatus, ['dipinjam', 'terlambat'])) {
                $peminjaman->inventaris()->increment('jumlah', $peminjaman->jumlah);
            }

            $peminjaman->sendStatusNotification('Ditolak');
            session()->flash('success', 'Peminjaman berhasil ditolak.');
        });
    }

    public function returnPeminjaman(Peminjaman $peminjaman, $hibah)
    {
        $validator = Validator::make(['hibah' => $hibah], ['hibah' => 'required|numeric|min:0']);
        if ($validator->fails()) {
            $this->dispatch('hibah-error', message: $validator->errors()->first());
            return;
        }

        DB::transaction(function () use ($peminjaman, $hibah) {
            $oldStatus = $peminjaman->status;
            $peminjaman->update(['status' => 'dikembalikan', 'hibah' => $hibah]);

            if (in_array($oldStatus, ['dipinjam', 'terlambat'])) {
                $peminjaman->inventaris()->increment('jumlah', $peminjaman->jumlah);
            }

            $peminjaman->sendStatusNotification('Dikembalikan');
            $this->dispatch('hibah-success');
            session()->flash('success', 'Peminjaman berhasil dikembalikan.');
        });
    }

    public function pendingPeminjaman(Peminjaman $peminjaman)
    {
        DB::transaction(function () use ($peminjaman) {
            $oldStatus = $peminjaman->status;
            $peminjaman->update(['status' => 'menunggu']);

            if ($oldStatus === 'dipinjam') {
                $peminjaman->inventaris()->increment('jumlah', $peminjaman->jumlah);
            }

            $peminjaman->sendStatusNotification('Pending');
            session()->flash('success', 'Peminjaman dikembalikan ke status pending.');
        });
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
        $user = Auth::user();
        $departemenId = $user->departemen_id;
        $peminjamans = Peminjaman::query()
            ->whereHas('inventaris.user', function ($query) use ($departemenId) {
                $query->where('departemen_id', $departemenId);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('inventaris', function ($sub) {
                        $sub->where('nama_barang', 'like', '%' . $this->search . '%');
                    })->orWhereHas('user', function ($sub) {
                        $sub->where('nama_lengkap', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate(10);
        return $this->view([
            'peminjamans' => $peminjamans,
            'user' => $user,
        ]);
    }
};
?>

<div>
    <x-page-header title="Daftar Peminjaman" leading="Kelola semua peminjaman"
        departemen="{{ Auth::user()->departemen->nama_departemen }}" />

    <div class="mx-auto mt-2 max-w-7xl space-y-2">

        {{-- ── FLASH MESSAGE ── --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="z-9999 fixed left-1/2 top-5 w-full max-w-sm -translate-x-1/2 px-4" style="display: none;">
                <div
                    class="flex select-none items-center gap-2.5 rounded-lg border border-emerald-100 bg-white py-2 pl-3 pr-2.5 shadow-xl shadow-stone-200/50 dark:border-emerald-950/60 dark:bg-stone-900 dark:shadow-none">
                    <div class="shrink-0 text-emerald-500 dark:text-emerald-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 text-[11px] font-medium leading-normal text-stone-700 dark:text-stone-300">
                        {{ session('success') }}
                    </div>
                    <button @click="show = false"
                        class="shrink-0 rounded p-1 text-stone-400 transition-colors hover:text-stone-600 focus:outline-none dark:hover:text-stone-200">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif


        {{-- ── FILTER BAR ── --}}
        <div class="rounded-xl border border-stone-200 bg-white px-3 py-3 dark:border-stone-800 dark:bg-stone-900">
            <div class="flex flex-col gap-2 sm:flex-row">

                {{-- Search --}}
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-stone-400 dark:text-stone-500"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                    <input wire:model.live.debounce.100ms="search" type="text"
                        placeholder="Cari nama peminjam, nama barang..."
                        class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 py-1.5 pl-8 pr-3 text-xs text-stone-800 transition placeholder:text-stone-400 focus:border-transparent focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100 dark:placeholder:text-stone-500" />
                </div>
                {{-- Filter Status --}}
                <select wire:model.live="status"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">Semua Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="dipinjam">Dipinjam</option>
                    <option value="dikembalikan">Dikembalikan</option>
                </select>

            </div>
        </div>


        {{-- ── TABLE CARD ── --}}
        <div
            class="overflow-hidden rounded-xl border border-stone-200 bg-white px-4 dark:border-stone-800 dark:bg-stone-900">

            {{-- Meta --}}
            <div class="flex items-center justify-between border-b border-stone-100 py-2 dark:border-stone-800">
                <span class="font-mono text-[10px] uppercase tracking-wider text-stone-400 dark:text-stone-500">
                    {{ $peminjamans->count() }} peminjaman ditemukan
                </span>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto rounded-xl border bg-white shadow-sm dark:border-stone-800 dark:bg-stone-900">
                <table class="w-full border-collapse text-left text-[11px]">
                    <thead>
                        <tr
                            class="border-b border-stone-200 bg-stone-50 font-semibold uppercase tracking-wider text-stone-500 dark:border-stone-800 dark:bg-stone-800/50 dark:text-stone-400">
                            <th class="w-6 px-2.5 py-1.5 text-center">#</th>
                            <th class="px-2.5 py-1.5">Barang</th>
                            <th class="px-2.5 py-1.5">Peminjam</th>
                            <th class="px-2.5 py-1.5">Tgl. Peminjaman</th>
                            <th class="px-2.5 py-1.5">Tgl. Pengembalian</th>
                            <th class="px-2.5 py-1.5 text-center">Jumlah</th>
                            <th class="px-2.5 py-1.5 text-center">Status</th>
                            {{-- <th class="w-20 px-2.5 py-1.5 text-right">Aksi</th> --}}
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                        @forelse ($peminjamans as $peminjaman)
                            <tr wire:key="peminjaman-{{ $peminjaman->id_peminjaman }}"
                                class="group transition-colors hover:bg-stone-50 dark:hover:bg-stone-800/30">

                                {{-- No --}}
                                <td class="px-2.5 py-1.5 text-center font-mono text-stone-400 dark:text-stone-600">
                                    {{ $loop->iteration + ($peminjamans->currentPage() - 1) * $peminjamans->perPage() }}
                                </td>

                                {{-- Gambar + Nama Barang + Departemen --}}
                                <td class="px-2.5 py-1.5">
                                    <div class="max-w-45 flex items-center gap-2 sm:max-w-xs">
                                        {{-- Thumbnail gambar atau placeholder --}}
                                        @if ($peminjaman->inventaris->img_path && Storage::exists($peminjaman->inventaris->img_path))
                                            <img src="{{ Storage::url($peminjaman->inventaris->img_path) }}"
                                                alt="{{ $peminjaman->inventaris->nama_barang }}"
                                                class="h-7 w-7 shrink-0 rounded-lg border border-stone-200 object-cover dark:border-stone-700" />
                                        @else
                                            <div
                                                class="bg-sage-100 dark:bg-sage-900/40 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-stone-200 dark:border-stone-700">
                                                <svg class="text-sage-500 dark:text-sage-400 h-3.5 w-3.5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="truncate">
                                            <div class="truncate font-medium text-stone-800 dark:text-stone-200">
                                                {{ $peminjaman->inventaris->nama_barang }}
                                            </div>
                                            <div class="truncate text-[10px] text-stone-400 dark:text-stone-500">
                                                {{ $peminjaman->inventaris->user->departemen->nama_departemen ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nama Peminjam --}}
                                <td class="truncate px-2.5 py-1.5 text-stone-500 dark:text-stone-400">
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
                                            'menunggu' => [
                                                'Pending',
                                                'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400',
                                            ],
                                            'ditolak' => [
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
                                        class="{{ $cls }} inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium">
                                        {{ $lbl }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-2.5 py-1.5 text-right">
                                    <div class="flex items-center justify-end" x-data="{ open: false }">
                                        <div class="relative inline-block text-left">

                                            <button @click="open = !open" @click.outside="open = false"
                                                class="cursor-pointer rounded-md p-1 text-stone-400 transition-colors hover:bg-stone-100 hover:text-stone-700 focus:outline-none dark:hover:bg-stone-800 dark:hover:text-stone-200"
                                                title="Menu Aksi">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                                </svg>
                                            </button>

                                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 z-50 mt-1 w-36 origin-top-right rounded-md border border-stone-200 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:border-stone-800 dark:bg-stone-900"
                                                style="display: none;">
                                                <div class="space-y-0.5 p-1">
                                                    @if ($peminjaman->status === 'dipinjam')
                                                        <button
                                                            x-on:click="
                                                                                            $wire.declinePeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                                            <svg class="color-rose-600 h-3.5 w-3.5"
                                                                viewBox="0 0 24 24" role="img"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                aria-labelledby="cancelIconTitle"
                                                                stroke="currentColor" stroke-width="1"
                                                                stroke-linecap="square" stroke-linejoin="miter"
                                                                fill="none">
                                                                <title id="cancelIconTitle">Cancel</title>
                                                                <path
                                                                    d="M15.5355339 15.5355339L8.46446609 8.46446609M15.5355339 8.46446609L8.46446609 15.5355339" />
                                                                <path
                                                                    d="M4.92893219,19.0710678 C1.02368927,15.1658249 1.02368927,8.83417511 4.92893219,4.92893219 C8.83417511,1.02368927 15.1658249,1.02368927 19.0710678,4.92893219 C22.9763107,8.83417511 22.9763107,15.1658249 19.0710678,19.0710678 C15.1658249,22.9763107 8.83417511,22.9763107 4.92893219,19.0710678 Z" />
                                                            </svg>
                                                            Decline
                                                        </button>
                                                        <button
                                                            x-on:click="
                                                                                                $wire.pendingPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-orange-500 transition-colors hover:bg-orange-50 dark:text-orange-400 dark:hover:bg-orange-950/40">
                                                            <svg class="color-orange-400 h-3 w-3" fill="currentColor"
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
                                                        <button x-data
                                                            x-on:click="$dispatch('open-hibah-modal', {id: {{ $peminjaman->id_peminjaman }}, defaultHibah: `0` })"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-blue-600 transition-colors hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40">
                                                            <svg fill="currentColor" class="h-3 w-3" version="1.1"
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
                                                    @elseif($peminjaman->status === 'ditolak')
                                                        <button
                                                            x-on:click="
                                                                                                $wire.acceptPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-green-500 transition-colors hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950/30">
                                                            <svg fill="currentColor" class="h-3.5 w-3.5"
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
                                                        <button
                                                            x-on:click="
                                                                                                $wire.pendingPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-orange-500 transition-colors hover:bg-orange-50 dark:text-orange-400 dark:hover:bg-orange-950/40">
                                                            <svg class="color-orange-400 h-3 w-3" fill="currentColor"
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
                                                        <button x-data
                                                            x-on:click="$dispatch('open-hibah-modal', {id: {{ $peminjaman->id_peminjaman }}, defaultHibah: 0 })"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-blue-600 transition-colors hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40">
                                                            <svg fill="currentColor" class="h-3 w-3" version="1.1"
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
                                                    @elseif ($peminjaman->status === 'menunggu')
                                                        <button
                                                            x-on:click="
                                                                                                $wire.acceptPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-green-500 transition-colors hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950/30">
                                                            <svg fill="currentColor" class="h-3.5 w-3.5"
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
                                                        <button
                                                            x-on:click="
                                                                                            $wire.declinePeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                                            <svg class="color-rose-600 h-3.5 w-3.5"
                                                                viewBox="0 0 24 24" role="img"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                aria-labelledby="cancelIconTitle"
                                                                stroke="currentColor" stroke-width="1"
                                                                stroke-linecap="square" stroke-linejoin="miter"
                                                                fill="none">
                                                                <title id="cancelIconTitle">Cancel</title>
                                                                <path
                                                                    d="M15.5355339 15.5355339L8.46446609 8.46446609M15.5355339 8.46446609L8.46446609 15.5355339" />
                                                                <path
                                                                    d="M4.92893219,19.0710678 C1.02368927,15.1658249 1.02368927,8.83417511 4.92893219,4.92893219 C8.83417511,1.02368927 15.1658249,1.02368927 19.0710678,4.92893219 C22.9763107,8.83417511 22.9763107,15.1658249 19.0710678,19.0710678 C15.1658249,22.9763107 8.83417511,22.9763107 4.92893219,19.0710678 Z" />
                                                            </svg>
                                                            Decline
                                                        </button>
                                                        <button
                                                            x-on:click="$dispatch('open-hibah-modal', {id: {{ $peminjaman->id_peminjaman }}, defaultHibah: 0 })"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-blue-600 transition-colors hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40">
                                                            <svg fill="currentColor" class="h-3 w-3" version="1.1"
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
                                                    @elseif ($peminjaman->status === 'dikembalikan')
                                                        <button
                                                            x-on:click="
                                                                                                $wire.acceptPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-green-500 transition-colors hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950/30">
                                                            <svg fill="currentColor" class="h-3.5 w-3.5"
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
                                                        <button
                                                            x-on:click="
                                                                                            $wire.declinePeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                                            <svg class="color-rose-600 h-3.5 w-3.5"
                                                                viewBox="0 0 24 24" role="img"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                aria-labelledby="cancelIconTitle"
                                                                stroke="currentColor" stroke-width="1"
                                                                stroke-linecap="square" stroke-linejoin="miter"
                                                                fill="none">
                                                                <title id="cancelIconTitle">Cancel</title>
                                                                <path
                                                                    d="M15.5355339 15.5355339L8.46446609 8.46446609M15.5355339 8.46446609L8.46446609 15.5355339" />
                                                                <path
                                                                    d="M4.92893219,19.0710678 C1.02368927,15.1658249 1.02368927,8.83417511 4.92893219,4.92893219 C8.83417511,1.02368927 15.1658249,1.02368927 19.0710678,4.92893219 C22.9763107,8.83417511 22.9763107,15.1658249 19.0710678,19.0710678 C15.1658249,22.9763107 8.83417511,22.9763107 4.92893219,19.0710678 Z" />
                                                            </svg>
                                                            Decline
                                                        </button>
                                                        <button
                                                            x-on:click="
                                                                                                $wire.pendingPeminjaman({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-orange-500 transition-colors hover:bg-orange-50 dark:text-orange-400 dark:hover:bg-orange-950/40">
                                                            <svg class="color-orange-400 h-3 w-3" fill="currentColor"
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
                                                    @elseif ($peminjaman->status === 'terlambat')
                                                        <button x-data
                                                            x-on:click="$dispatch('open-hibah-modal', {id: {{ $peminjaman->id_peminjaman }}, defaultHibah: `0` })"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-blue-600 transition-colors hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40">
                                                            <svg fill="currentColor" class="h-3 w-3" version="1.1"
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
                                                    @endif
                                                    <flux:separator />
                                                    <button x-data
                                                        x-on:click="$dispatch('open-wa-modal', {
                                                                                            id: {{ $peminjaman->id_peminjaman }},
                                                                                            defaultTarget: '{{ $peminjaman->user->no_wa ?? '' }}'
                                                                                        })"
                                                        class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-green-600 transition-colors hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950/40">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M17.6 6.31999C16.8669 5.58141 15.9943 4.99596 15.033 4.59767C14.0716 4.19938 13.0406 3.99622 12 3.99999C10.6089 4.00135 9.24248 4.36819 8.03771 5.06377C6.83294 5.75935 5.83208 6.75926 5.13534 7.96335C4.4386 9.16745 4.07046 10.5335 4.06776 11.9246C4.06507 13.3158 4.42793 14.6832 5.12 15.89L4 20L8.2 18.9C9.35975 19.5452 10.6629 19.8891 11.99 19.9C14.0997 19.9001 16.124 19.0668 17.6222 17.5816C19.1205 16.0965 19.9715 14.0796 19.99 11.97C19.983 10.9173 19.7682 9.87634 19.3581 8.9068C18.948 7.93725 18.3505 7.05819 17.6 6.31999ZM12 18.53C10.8177 18.5308 9.65701 18.213 8.64 17.61L8.4 17.46L5.91 18.12L6.57 15.69L6.41 15.44C5.55925 14.0667 5.24174 12.429 5.51762 10.8372C5.7935 9.24545 6.64361 7.81015 7.9069 6.80322C9.1702 5.79628 10.7589 5.28765 12.3721 5.37368C13.9853 5.4597 15.511 6.13441 16.66 7.26999C17.916 8.49818 18.635 10.1735 18.66 11.93C18.6442 13.6859 17.9355 15.3645 16.6882 16.6006C15.441 17.8366 13.756 18.5301 12 18.53ZM15.61 13.59C15.41 13.49 14.44 13.01 14.26 12.95C14.08 12.89 13.94 12.85 13.81 13.05C13.6144 13.3181 13.404 13.5751 13.18 13.82C13.07 13.96 12.95 13.97 12.75 13.82C11.6097 13.3694 10.6597 12.5394 10.06 11.47C9.85 11.12 10.26 11.14 10.64 10.39C10.6681 10.3359 10.6827 10.2759 10.6827 10.215C10.6827 10.1541 10.6681 10.0941 10.64 10.04C10.64 9.93999 10.19 8.95999 10.03 8.56999C9.87 8.17999 9.71 8.23999 9.58 8.22999H9.19C9.08895 8.23154 8.9894 8.25465 8.898 8.29776C8.8066 8.34087 8.72546 8.403 8.66 8.47999C8.43562 8.69817 8.26061 8.96191 8.14676 9.25343C8.03291 9.54495 7.98287 9.85749 8 10.17C8.0627 10.9181 8.34443 11.6311 8.81 12.22C9.6622 13.4958 10.8301 14.5293 12.2 15.22C12.9185 15.6394 13.7535 15.8148 14.58 15.72C14.8552 15.6654 15.1159 15.5535 15.345 15.3915C15.5742 15.2296 15.7667 15.0212 15.91 14.78C16.0428 14.4856 16.0846 14.1583 16.03 13.84C15.94 13.74 15.81 13.69 15.61 13.59Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                        Chat
                                                    </button>
                                                    <button x-data
                                                        x-on:click="$dispatch('open-delete-modal', { id: {{ $peminjaman->id_peminjaman }} })"
                                                        class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                                        <svg class="h-3.5 w-3.5 text-rose-400" fill="none"
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
                                        <svg class="h-8 w-8 text-stone-300 dark:text-stone-700" fill="none"
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
            <div class="border-t border-stone-100 px-4 py-3 dark:border-stone-800">
                {{ $peminjamans->links('pagination::tailwind') }}
            </div>

        </div>
        {{-- ── DELETE MODAL ── --}}
        @if ($showDeleteModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm dark:bg-black/60"
                wire:click.self="$set('showDeleteModal', false)">
                <div
                    class="w-full max-w-xs rounded-xl border border-stone-200 bg-white p-5 shadow-2xl dark:border-stone-800 dark:bg-stone-900">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-50 dark:bg-red-950">
                            <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="mb-0.5 text-sm font-semibold text-stone-800 dark:text-stone-100">Hapus
                                Peminjaman</h3>
                            <p class="text-xs text-stone-500 dark:text-stone-400">Data peminjaman akan dihapus permanen
                                dari
                                sistem.</p>
                        </div>
                    </div>
                    <div class="mt-5 flex gap-2">
                        <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 rounded-lg border border-stone-200 px-3 py-2 text-xs font-medium text-stone-700 transition-colors hover:bg-stone-50 dark:border-stone-700 dark:text-stone-300 dark:hover:bg-stone-800">
                            Batal
                        </button>
                        <button wire:click="deletePeminjaman"
                            class="flex-1 rounded-lg bg-red-500 px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-red-600">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        @endif
        <div x-data="{
            show: false,
            hibah: '',
            errorMessage: '',
            idPeminjaman: null,
            init() {
                window.addEventListener('open-hibah-modal', (e) => {
                    this.idPeminjaman = e.detail.id;
                    this.hibah = e.detail.defaultHibah || '';
                    this.message = '';
                    this.errorMessage = '';
                    this.show = true;
                });

                // Event sukses dari Livewire
                window.addEventListener('hibah-success', (e) => {
                    this.show = false;
                });

                // Event error dari Livewire
                window.addEventListener('hibah-error', (e) => {
                    this.errorMessage = e.detail.message;
                });
            },
            submit() {
                this.errorMessage = ''; // reset error sebelum kirim
                $wire.returnPeminjaman(this.idPeminjaman, this.hibah);
            }
        }" x-show="show" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm dark:bg-black/60"
            x-cloak>
            <div
                class="w-full max-w-xs rounded-md border bg-white p-4 shadow-xl dark:border-emerald-900 dark:bg-stone-900">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-stone-200 dark:border-stone-800">
                    <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-200">Tambah Inventaris</h3>
                    <button @click="show = false"
                        class="rounded-md p-1 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600 dark:hover:bg-stone-800 dark:hover:text-stone-200">
                        <svg class="h-4 w-4 cursor-pointer" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mb-4 border-t border-stone-100 dark:border-stone-800"></div>

                {{-- Pesan Error --}}
                <template x-if="errorMessage">
                    <div class="mb-3 rounded border border-red-300 bg-red-50 px-2 py-1.5 text-xs text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400"
                        x-text="errorMessage">
                    </div>
                </template>

                <div x-data="{ show: true }" x-show="show"
                    class="flex select-none items-center gap-2.5 rounded-lg border border-amber-200 bg-amber-50 py-2 pl-3 pr-2.5 text-amber-700 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-300 dark:shadow-none">
                    <div class="shrink-0">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="flex-1 text-[11px] font-medium leading-normal">
                        <span>{{ __('Masukkan nominal Hibah yang diberikan peminjam. Jika tidak, maka berikan nominal 0;') }}</span>
                    </div>
                    <button @click="show = false"
                        class="shrink-0 rounded p-1 text-stone-400 transition-colors hover:text-stone-600 dark:hover:text-stone-200">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="relative mt-2">
                    <div wire:loading wire:target="returnPeminjaman"
                        class="absolute inset-0 z-50 flex items-center justify-center rounded-md bg-white/60 pt-10 backdrop-blur-[0.5px] dark:bg-stone-900/60">
                        <div
                            class="flex items-center gap-1.5 rounded-md border border-stone-100 bg-white px-2.5 py-1.5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                            <svg class="text-sage-600 dark:text-sage-400 h-3.5 w-3.5 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V12H4z"></path>
                            </svg>
                            <span class="text-[10px] font-medium text-stone-600 dark:text-stone-300">
                                {{ __('Menyimpan data...') }}
                            </span>
                        </div>
                    </div>

                    {{-- Hibah --}}
                    <div>
                        <label class="block text-[11px] font-medium text-stone-600 dark:text-stone-400">Hibah</label>
                        <input type="text" required placeholder="0" x-model="hibah"
                            class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                        @error('hibah')
                            <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 border-t border-stone-100 pt-4 dark:border-stone-800">
                        <button type="button" x-on:click="show = false"
                            class="cursor-pointerrounded-lg border border-stone-200 px-4 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50 dark:border-stone-700 dark:text-stone-400 dark:hover:bg-stone-800">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="returnPeminjaman"
                            x-on:click="submit()"
                            class="bg-sage-600 hover:bg-sage-700 focus:ring-sage-500 cursor-pointer rounded-lg px-4 py-1.5 text-xs font-medium text-white transition focus:outline-none focus:ring-2 focus:ring-offset-1">
                            Simpan
                        </button>
                    </div>
                    {{-- Tombol --}}
                    <div class="flex justify-end gap-1.5">
                        <button @click="show = false"
                            class="rounded bg-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Batal
                        </button>
                        <button @click="submit()"
                            class="rounded bg-emerald-600 px-3 py-1.5 text-xs text-white hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                            Kirim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
