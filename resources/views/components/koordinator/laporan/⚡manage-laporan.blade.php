<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LaporanInventaris;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterGender = '';
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    #[Computed]
    public function laporanInventaris()
    {
        return LaporanInventaris::with('user')
            ->when(
                $this->search,
                fn($q) => $q
                    ->where('nama_lengkap', 'like', "%{$this->search}%")
                    ->orWhere('nik', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"),
            )
            ->when($this->filterGender, fn($q) => $q->where('jenis_kelamin', $this->filterGender))
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
};
?>

<div class="min-h-screen bg-stone-100 dark:bg-stone-950">
    {{-- ── PAGE HEADER ── --}}
    <div class="border-b border-stone-200 bg-white px-5 py-3.5 dark:border-stone-800 dark:bg-stone-900">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-0.5 flex items-center gap-2">
                    <span
                        class="text-sage-600 dark:text-sage-400 font-mono text-[9px] uppercase tracking-widest">Manajemen</span>
                </div>
                <h1 class="font-display text-xl font-semibold text-stone-800 dark:text-stone-100">Laporan Inventaris</h1>
                <p class="mt-0.5 text-xs text-stone-500 dark:text-stone-400">Kelola semua Laporan Invenataris untuk
                    {{ $this->user->departemen->nama_departemen }}</p>
            </div>
        </div>
    </div>

    <div class="mx-auto mt-2 max-w-7xl space-y-2">

        {{-- ── FLASH MESSAGE ── --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
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
                        placeholder="Cari nama, NIK, atau email…"
                        class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 py-1.5 pl-8 pr-3 text-xs text-stone-800 transition placeholder:text-stone-400 focus:border-transparent focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100 dark:placeholder:text-stone-500" />
                </div>

                {{-- Filter Gender --}}
                <select wire:model.live="filterGender"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">{{ __('Semua Gender') }}</option>
                    <option value="laki-laki">{{ __('Laki-laki') }}</option>
                    <option value="perempuan">{{ __('Perempuan') }}</option>
                </select>

                <a href="#"
                    class="bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold text-white shadow-sm transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah User
                </a>

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
                                                    {{-- Hapus --}}
                                                    <button
                                                        wire:click="deleteLaporan({{ $laporan->id_laporan_inventaris }})"
                                                        wire:confirm="Apakah anda yakin ingin menghapus Laporan ini?"
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

    </div>
</div>
