<?php

use Livewire\Component;
use App\Models\Inventaris;
use App\Exports\InventarisExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\LaporanInventaris;

new class extends Component {
    use Livewire\WithPagination;

    public $search = '';
    public $filterKondisi = '';
    public $filterTipe = '';
    public $filterDptDipinjam = '';

    public function mount()
    {
        Carbon::setLocale('id');
    }

    public function editInventaris($id)
    {
        $this->dispatch('edit-inventaris', $id);
    }

    public function deleteInventaris(Inventaris $inventaris)
    {
        try {
            $inventaris->delete();
            session('success', 'Inventaris berhasil dihapus!');
        } catch (Exception $e) {
            Log::log('error', $e->getMessage());
            session('error', 'Gagal menghapus Inventaris' . $e->getMessage());
        }
    }

    public function render()
    {
        $departemenId = Auth::user()->departemen->id_departemen;
        $inventaris = Inventaris::query()
            ->whereHas('user.departemen', function ($q) use ($departemenId) {
                $q->where('departemen_id', $departemenId);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', "%{$this->search}%")->orWhere('tipe', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterKondisi, fn($q) => $q->where('kondisi', $this->filterKondisi))
            ->when($this->filterTipe, fn($q) => $q->where('tipe', $this->filterTipe))
            ->when($this->filterDptDipinjam, fn($q) => $q->where('dpt_dipinjam', $this->filterDptDipinjam))
            ->latest()
            ->paginate(10);
        return $this->view([
            'inventaris' => $inventaris,
        ]);
    }

    public function buatLaporan()
    {
        $user = Auth::user();
        $month = Carbon::now()->translatedFormat('F');
        $filename = $user->departemen->singkatan . '-inventaris.xlsx';
        $folderPath = 'export-inventaris/' . $month;
        $fullPath = $folderPath . '/' . $filename;
        try {
            LaporanInventaris::create([
                'user_id' => $user->id_user,
                'laporan_path' => $fullPath,
                'bulan' => $month,
                'status' => 'pending',
            ]);
            session()->flash('success', 'Berhasil menyimpan data laporan bulanan!');
            return Excel::store(new InventarisExport($user), $fullPath);
            // return Excel::download(new InventarisExport($user), $fullPath);
        } catch (Exception $e) {
            Log::error('Gagal membuat laporan: ' . $e->getMessage());
            if (Storage::exists($fullPath)) {
                Storage::delete($fullPath);
            }
            session()->flash('error', 'Gagal menyimpan database: ' . $e->getMessage());
        }
    }
};
?>

<div>
    <x-page-header title="Daftar Inventaris" leading="Kelola semua inventaris"
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
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari nama barang, tipe, atau warna…"
                        class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-stone-50 py-1.5 pl-8 pr-3 text-xs text-stone-800 transition placeholder:text-stone-400 focus:border-transparent focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100 dark:placeholder:text-stone-500" />
                </div>

                {{-- Filter Kondisi --}}
                <select wire:model.live="filterKondisi"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">Semua Kondisi</option>
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="servis">Servis</option>
                </select>

                {{-- Filter Tipe --}}
                <select wire:model.live="filterTipe"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">Semua Tipe</option>
                    @foreach ($tipeList ?? [] as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>

                {{-- Filter Dapat Dipinjam --}}
                <select wire:model.live="filterDptDipinjam"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">Semua</option>
                    <option value="1">Dapat Dipinjam</option>
                    <option value="0">Tidak Dapat Dipinjam</option>
                </select>

                {{-- Tombol Tambah --}}
                <button x-data x-on:click="$dispatch('open-inventaris-modal')" x-cloak
                    class="bg-sage-600 dark:bg-sage-500 hover:bg-sage-700 dark:hover:bg-sage-400 hover:shadow-sage-600/20 inline-flex shrink-0 cursor-pointer items-center gap-1.5 whitespace-nowrap rounded-lg px-3 py-1.5 text-xs font-medium text-white shadow-sm transition-all hover:shadow">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Barang
                </button>

                {{-- Tombol Laporan --}}
                <button wire:click="buatLaporan"
                    class="bg-sage-100 dark:bg-white-500 hover:bg-white-700 dark:hover:bg-white-400 hover:shadow-white-600/20 text-sage-600 inline-flex shrink-0 cursor-pointer items-center gap-1.5 whitespace-nowrap rounded-lg px-3 py-1.5 text-xs font-medium shadow-sm transition-all hover:shadow">
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

                            <path class="cls-1" d="M275,280H125a5,5,0,1,1,0-10H275a5,5,0,0,1,0,10Z" />

                            <path class="cls-1" d="M200,330H125a5,5,0,1,1,0-10h75a5,5,0,0,1,0,10Z" />

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
                    Laporan
                </button>
            </div>
        </div>


        {{-- ── TABLE CARD ── --}}
        <div
            class="overflow-hidden rounded-xl border border-stone-200 bg-white px-4 dark:border-stone-800 dark:bg-stone-900">

            {{-- Meta --}}
            <div class="flex items-center justify-between border-b border-stone-100 py-2 dark:border-stone-800">
                <span class="font-mono text-[10px] uppercase tracking-wider text-stone-400 dark:text-stone-500">
                    {{ $inventaris->count() }} barang ditemukan
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
                            <th class="hidden px-2.5 py-1.5 sm:table-cell">Tipe</th>
                            <th class="hidden w-16 px-2.5 py-1.5 text-center md:table-cell">Jumlah</th>
                            <th class="hidden px-2.5 py-1.5 sm:table-cell">Kondisi</th>
                            <th class="hidden px-2.5 py-1.5 lg:table-cell">Warna</th>
                            <th class="hidden px-2.5 py-1.5 text-center md:table-cell">Dipinjam</th>
                            <th class="w-20 px-2.5 py-1.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                        @forelse ($inventaris as $item)
                            <tr wire:key="inventaris-{{ $item->id }}"
                                class="group transition-colors hover:bg-stone-50 dark:hover:bg-stone-800/30">

                                {{-- No --}}
                                <td class="px-2.5 py-1.5 text-center font-mono text-stone-400 dark:text-stone-600">
                                    {{ $loop->iteration + ($inventaris->currentPage() - 1) * $inventaris->perPage() }}
                                </td>

                                {{-- Gambar + Nama Barang --}}
                                <td class="px-2.5 py-1.5">
                                    <div class="max-w-45 flex items-center gap-2 sm:max-w-xs">
                                        {{-- Thumbnail gambar atau placeholder --}}
                                        @if ($item->img_path && Storage::exists($item->img_path))
                                            <img src="{{ Storage::url($item->img_path) }}"
                                                alt="{{ $item->nama_barang }}"
                                                class="h-7 w-7 shrink-0 rounded-lg border border-stone-200 object-cover dark:border-stone-700" />
                                        @else
                                            <div
                                                class="bg-sage-100 dark:bg-sage-900/40 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-stone-200 dark:border-stone-700">
                                                <svg class="text-sage-500 dark:text-sage-400 h-3.5 w-3.5"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="truncate">
                                            <div class="truncate font-medium text-stone-800 dark:text-stone-200">
                                                {{ $item->nama_barang }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Tipe --}}
                                <td
                                    class="hidden truncate px-2.5 py-1.5 text-stone-500 sm:table-cell dark:text-stone-400">
                                    {{ $item->tipe ?? '-' }}
                                </td>

                                {{-- Jumlah --}}
                                <td
                                    class="hidden px-2.5 py-1.5 text-center font-mono text-stone-600 md:table-cell dark:text-stone-400">
                                    {{ $item->jumlah }}
                                </td>

                                {{-- Kondisi Badge --}}
                                <td class="hidden px-2.5 py-1.5 sm:table-cell">
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
                                        class="{{ $cls }} inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium">
                                        {{ $lbl }}
                                    </span>
                                </td>

                                {{-- Warna --}}
                                <td class="hidden px-2.5 py-1.5 lg:table-cell">
                                    @if ($item->warna)
                                        <div class="flex items-center gap-1.5">
                                            {{-- Swatch warna (best-effort CSS color name / hex) --}}
                                            <span
                                                class="h-3 w-3 shrink-0 rounded-full border border-stone-300 dark:border-stone-600"
                                                style="background-color: {{ $item->warna }};"></span>
                                            <span
                                                class="max-w-20 truncate capitalize text-stone-500 dark:text-stone-400">
                                                {{ $item->warna }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-stone-300 dark:text-stone-600">—</span>
                                    @endif
                                </td>

                                {{-- Dapat Dipinjam --}}
                                <td class="hidden px-2.5 py-1.5 text-center md:table-cell">
                                    @if ($item->dpt_dipinjam)
                                        <span
                                            class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-600 dark:text-emerald-400">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Ya
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-[10px] font-medium text-stone-400 dark:text-stone-600">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor"
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
                                                class="cursor-pointer rounded-md p-1 text-stone-400 transition-colors hover:bg-stone-100 hover:text-stone-700 focus:outline-none dark:hover:bg-stone-800 dark:hover:text-stone-200"
                                                title="Menu Aksi">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
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
                                                class="absolute right-0 z-30 mt-1 w-36 origin-top-right rounded-md border border-stone-200 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:border-stone-800 dark:bg-stone-900"
                                                style="display: none;">
                                                <div class="space-y-0.5 p-1">

                                                    {{-- Detail --}}
                                                    <button
                                                        x-on:click="
                                                            $flux.modal('detail-inventaris-modal').show();
                                                            $wire.lihatDetail({{ $item->id }});
                                                            open = false;"
                                                        class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-stone-600 transition-colors hover:bg-stone-50 dark:text-stone-400 dark:hover:bg-stone-800/60">
                                                        <svg class="h-3.5 w-3.5 text-stone-400" fill="none"
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
                                                        class="text-sage-600 dark:text-sage-400 hover:bg-sage-50 dark:hover:bg-sage-950/30 flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs transition-colors">
                                                        <svg class="text-sage-500 h-3.5 w-3.5" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                        </svg>
                                                        Edit
                                                    </button>

                                                    <flux:separator />

                                                    {{-- Hapus --}}
                                                    <button wire:click="deleteInventaris({{ $item->id_inventaris }})"
                                                        @click="open = false"
                                                        wire:confirm="Apakah anda yakin ingin menghapus inventaris ini?"
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
                {{ $inventaris->links('pagination::tailwind') }}
            </div>

        </div>
        <livewire:koordinator.inventaris.add-inventaris />
    </div>
</div>
