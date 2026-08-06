<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LaporanInventaris;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Kelola Laporan Inventaris')] class extends Component {
    use WithPagination;

    public string $search = '';
    public $status;
    public $perPage = 10;

    #[Computed]
    public function laporanInventaris()
    {
        return LaporanInventaris::with('user.departemen')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->orWhere('bulan', 'like', '%' . $this->search . '%')->orWhereHas('user.departemen', function ($q) {
                        $q->where('nama_departemen', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);
    }

    public function downloadFile($path)
    {
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        session()->flash('error', 'File tidak ditemukan.');
    }

    public function deleteLaporan(LaporanInventaris $laporan)
    {
        try {
            if (Storage::exists($laporan->laporan_path)) {
                Storage::delete($laporan->laporan_path);
            }
            $laporan->delete();
            session()->flash('success', 'Laporan berhasil dihapus!');
        } catch (Exception $e) {
            Log::error('Delete Laporan : ' . $e->getMessage());
            session()->flash('error', 'Gagal menghapus Laporan: ' . $e->getMessage());
        }
    }

    public function terimaLaporan(LaporanInventaris $laporan)
    {
        try {
            $laporan->update(['status' => 'diterima']);
            session()->flash('success', 'Status Laporan berhasil diperbaharui!');
        } catch (\Exception $e) {
            Log::error('Gagal menerima laporan: ' . $e->getMessage());
            session()->flash('error', 'Status Laporan gagal diperbaharui!');
        }
    }

    public function tolakLaporan(LaporanInventaris $laporan)
    {
        try {
            $laporan->update(['status' => 'ditolak']);
            session()->flash('success', 'Status Laporan berhasil diperbaharui!');
        } catch (\Exception $e) {
            Log::error('Gagal menolak laporan: ' . $e->getMessage());
            session()->flash('error', 'Status Laporan gagal diperbaharui!');
        }
    }

    public function pendingLaporan(LaporanInventaris $laporan)
    {
        try {
            $laporan->update(['status' => 'pending']);
            session()->flash('success', 'Status Laporan berhasil diperbaharui!');
        } catch (\Exception $e) {
            Log::error('Gagal mengubah status laporan ke pending: ' . $e->getMessage());
            session()->flash('error', 'Status Laporan gagal diperbaharui!');
        }
    }
};
?>

<div class="min-h-screen bg-stone-100 dark:bg-stone-950">
    <x-page-header title="Daftar Laporan Inventaris" leading="Kelola semua Laporan Inventaris dari Departemen" />

    <div class="mx-auto mt-2 max-w-7xl space-y-2">

        {{-- ── FLASH MESSAGE ── --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="z-9999 fixed left-1/2 top-5 w-full max-w-sm -translate-x-1/2 px-4" style="display: none;">

                {{-- Kontainer Utama Toast --}}
                <div
                    class="flex select-none items-center gap-2.5 rounded-lg border border-emerald-100 bg-white py-2 pl-3 pr-2.5 shadow-xl shadow-stone-200/50 dark:border-emerald-950/60 dark:bg-stone-900 dark:shadow-none">

                    {{-- Ikon Sukses (Simbol Check Bulat) --}}
                    <div class="shrink-0 text-emerald-500 dark:text-emerald-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    {{-- Pesan Teks --}}
                    <div class="flex-1 text-[11px] font-medium leading-normal text-stone-700 dark:text-stone-300">
                        {{ session('success') }}
                    </div>

                    {{-- Tombol Close Manual (Silang) --}}
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
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari nama departemen & bulan"
                        class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 py-1.5 pl-8 pr-3 text-xs text-stone-800 transition placeholder:text-stone-400 focus:border-transparent focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100 dark:placeholder:text-stone-500" />
                </div>

                {{-- Per Page --}}
                <select wire:model.live="perPage"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">{{ __('Per Page') }}</option>
                    <option value="5">{{ __('5') }}</option>
                    <option value="10">{{ __('10') }}</option>
                    <option value="15">{{ __('15') }}</option>
                    <option value="20">{{ __('20') }}</option>
                    <option value="30">{{ __('30') }}</option>
                </select>

                {{-- Filter Gender --}}
                <select wire:model.live="status"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">{{ __('Semua Status') }}</option>
                    <option value="pending">{{ __('Menunggu') }}</option>
                    <option value="diterima">{{ __('Diterima') }}</option>
                    <option value="ditolak">{{ __('Ditolak') }}</option>
                </select>
            </div>
        </div>

        {{-- ── TABLE CARD ── --}}
        <div
            class="overflow-hidden rounded-xl border border-stone-200 bg-white px-4 dark:border-stone-800 dark:bg-stone-900">

            {{-- Table meta --}}
            <div class="flex items-center justify-between border-b border-stone-100 py-2 dark:border-stone-800">
                <span class="font-mono text-[10px] uppercase tracking-wider text-stone-400 dark:text-stone-500">
                    {{ $this->laporanInventaris->total() }} {{ __('laporan') }}
                </span>
            </div>

            {{-- Scrollable table wrapper --}}
            <div class="overflow-x-auto rounded-xl border bg-white shadow-sm dark:border-stone-800 dark:bg-stone-900">
                <table class="w-full border-collapse text-left text-[11px]">
                    <thead>
                        <tr
                            class="border-b border-stone-200 bg-stone-50 font-semibold uppercase tracking-wider text-stone-500 dark:border-stone-800 dark:bg-stone-800/50 dark:text-stone-400">
                            <th class="w-6 px-2.5 py-1.5 text-center">#</th>
                            <th class="px-2.5 py-1.5">{{ __('Departemen') }}</th>
                            <th class="px-2.5 py-1.5">{{ __('Bulan') }}</th>
                            <th class="hidden px-2.5 py-1.5 lg:table-cell">{{ __('Laporan') }}</th>
                            <th class="hidden w-10 px-2.5 py-1.5 text-center sm:table-cell">{{ __('Status') }}</th>
                            <th class="w-20 px-2.5 py-1.5 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                        @forelse ($this->laporanInventaris as $laporan)
                            <tr wire:key="laporan-{{ $laporan->id_laporan }}"
                                class="group transition-colors hover:bg-stone-50 dark:hover:bg-stone-800/30">

                                {{-- No --}}
                                <td class="px-2.5 py-1.5 text-center font-mono text-stone-400 dark:text-stone-600">
                                    {{ $loop->iteration + ($this->laporanInventaris->currentPage() - 1) * $this->laporanInventaris->perPage() }}
                                </td>

                                {{-- Departemen --}}
                                <td class="px-2.5 py-1.5 font-mono text-stone-500 sm:table-cell dark:text-stone-400">
                                    {{ $laporan->user->departemen->nama_departemen ?? '-' }}
                                </td>

                                {{-- Bulan --}}
                                <td class="px-2.5 py-1.5 font-mono text-stone-500 sm:table-cell dark:text-stone-400">
                                    {{ $laporan->bulan }}
                                </td>

                                {{-- Laporan --}}
                                <td class="truncate px-2.5 py-1.5 text-stone-500 lg:table-cell dark:text-stone-400">
                                    <button
                                        class="flex cursor-pointer items-center gap-1 text-green-700 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                                        wire:click="downloadFile('{{ $laporan->laporan_path }}')"
                                        title="Unduh laporan Excel">
                                        <svg class="h-4a w-4" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">

                                            <defs>

                                                <style>
                                                    .cls-1 {
                                                        fill: #0f773d;
                                                    }
                                                </style>

                                            </defs>

                                            <title />

                                            <g id="xxx-word">

                                                <path class="cls-1"
                                                    d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,1,1,10,0V95h70a5,5,0,0,1,0,10Z" />

                                                <path class="cls-1"
                                                    d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z" />

                                                <path class="cls-1"
                                                    d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z" />

                                                <path class="cls-1"
                                                    d="M275,280H125a5,5,0,1,1,0-10H275a5,5,0,0,1,0,10Z" />

                                                <path class="cls-1"
                                                    d="M200,330H125a5,5,0,1,1,0-10h75a5,5,0,0,1,0,10Z" />

                                                <path class="cls-1"
                                                    d="M325,280H75a30,30,0,0,1-30-30V173.17a30,30,0,0,1,30-30h.2l250,1.66a30.09,30.09,0,0,1,29.81,30V250A30,30,0,0,1,325,280ZM75,153.17a20,20,0,0,0-20,20V250a20,20,0,0,0,20,20H325a20,20,0,0,0,20-20V174.83a20.06,20.06,0,0,0-19.88-20l-250-1.66Z" />

                                                <path class="cls-1"
                                                    d="M152.44,236H117.79V182.68h34.3v7.93H127.4v14.45h19.84v7.73H127.4v14.92h25Z" />

                                                <path class="cls-1"
                                                    d="M190.18,236H180l-8.36-14.37L162.52,236h-7.66L168,215.69l-11.37-19.14h10.2l6.48,11.6,7.38-11.6h7.46L177,213.66Z" />

                                                <path class="cls-1"
                                                    d="M217.4,221.51l7.66.78q-1.49,7.42-5.74,11A15.5,15.5,0,0,1,209,236.82q-8.17,0-12.56-6a23.89,23.89,0,0,1-4.39-14.59q0-8.91,4.8-14.73a15.77,15.77,0,0,1,12.81-5.82q12.89,0,15.35,13.59l-7.66,1.05q-1-7.34-7.23-7.34a6.9,6.9,0,0,0-6.58,4,20.66,20.66,0,0,0-2.05,9.59q0,6,2.13,9.22a6.74,6.74,0,0,0,6,3.24Q215.49,229,217.4,221.51Z" />

                                                <path class="cls-1"
                                                    d="M257,223.42l8,1.09a16.84,16.84,0,0,1-6.09,8.83,18.13,18.13,0,0,1-11.37,3.48q-8.2,0-13.2-5.51t-5-14.92q0-8.94,5-14.8t13.67-5.86q8.44,0,13,5.78t4.61,14.84l0,1H238.61a22.12,22.12,0,0,0,.76,6.45,8.68,8.68,0,0,0,3,4.22,8.83,8.83,0,0,0,5.66,1.8Q254.67,229.83,257,223.42Zm-.55-11.8a9.92,9.92,0,0,0-2.56-7,8.63,8.63,0,0,0-12.36-.18,11.36,11.36,0,0,0-2.89,7.13Z" />

                                                <path class="cls-1" d="M282.71,236h-8.91V182.68h8.91Z" />

                                            </g>

                                        </svg>
                                        <span class="text-xs">Unduh</span>
                                    </button>
                                </td>

                                {{-- Status --}}
                                <td class="px-2.5 py-1.5 text-center sm:table-cell">
                                    @if ($laporan->status === 'pending')
                                        <span
                                            class="inline-block rounded-full bg-yellow-100 px-2 py-0.5 text-xs text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            Pending
                                        </span>
                                    @elseif ($laporan->status === 'diterima')
                                        <span
                                            class="inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            Diterima
                                        </span>
                                    @elseif ($laporan->status === 'ditolak')
                                        <span
                                            class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            Ditolak
                                        </span>
                                    @else
                                        <span
                                            class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                            {{ $laporan->status }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-2.5 py-1.5 text-right">
                                    <div class="flex items-center justify-end" x-data="{ open: false }">
                                        <div class="relative inline-block text-left">

                                            {{-- Tombol Titik Tiga --}}
                                            <button @click="open = !open" @click.outside="open = false"
                                                class="cursor-pointer rounded-md p-1 text-stone-400 transition-colors hover:bg-stone-100 hover:text-stone-700 focus:outline-none dark:hover:bg-stone-800 dark:hover:text-stone-200"
                                                title="Menu Aksi">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
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
                                                class="absolute right-0 z-30 mt-1 w-32 origin-top-right rounded-md border border-stone-200 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:border-stone-800 dark:bg-stone-900"
                                                style="display: none;">
                                                <div class="space-y-0.5 p-1">
                                                    @can('isSuperAdmin')
                                                        {{-- Accept --}}
                                                        <button
                                                            x-on:click="
                                                            $wire.terimaLaporan({{ $laporan->id_laporan_inventaris }})"
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
                                                            Terima
                                                        </button>
                                                        {{-- Decline --}}
                                                        <button
                                                            x-on:click="
                                                            $wire.tolakLaporan({{ $laporan->id_laporan_inventaris }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                                            <svg class="color-rose-600 h-3.5 w-3.5" viewBox="0 0 24 24"
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
                                                                $wire.pendingLaporan({{ $laporan->id_laporan_inventaris }})"
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
                                                    @endcan
                                                    <flux:separator />
                                                    {{-- Hapus --}}
                                                    <button x-data @click="open = false"
                                                        class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                                        <svg class="h-3.5 w-3.5 text-red-400" fill="none"
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
                                        <p class="text-xs font-medium">{{ __('Tidak ada pengguna ditemukan') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-stone-100 px-4 py-3 dark:border-stone-800">
                {{ $this->laporanInventaris->links('pagination::tailwind') }}
            </div>
        </div>
        <x-modal-hapus modal_name="delete-laporan" action_hapus="deleteLaporan" title="Hapus Laporan"
            description="Data pengguna akan dihapus permanen dari sistem." />
    </div>
</div>
