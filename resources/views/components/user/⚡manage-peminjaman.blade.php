<?php

use Livewire\Component;
use App\Models\Peminjaman;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;

new #[Title('Manajemen Peminjaman User')] class extends Component {
    use WithPagination;
    public $search = '';
    public $status;
    public $peminjaman;

    public function deletePeminjaman()
    {
        $this->peminjaman->delete();
        $this->reset('peminjaman', 'showDeleteModal');
        session()->flash('success', 'Peminjaman berhasil dihapus.');
    }

    public function render()
    {
        $peminjamans = Auth::user()
            ->peminjamans()
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
        ]);
    }

    public function downloadPdf(Peminjaman $peminjaman)
    {
        try {
            $pdf = Pdf::loadView('peminjaman-pdf', ['peminjaman' => $peminjaman])->setPaper('A4');
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
                session()->flash('success', 'Bukti berhasil diunduh');
            }, 'Bukti Peminjaman ' . Auth::user()->nama_lengkap . '.pdf');
        } catch (\Exception $e) {
            Log::error('Gagal mendownload bukti peminjaman: ' . $e->getMessage());
            session()->flash('error', 'Gagal mengunduh bukti peminjaman.');
        }
    }
};
?>

<div>
    <x-page-header title="Daftar Peminjaman" leading="Kelola semua peminjaman anda" />

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
                {{-- Button List Inventaris --}}
                <a href="{{ route('list-inventaris') }}"
                    class="dark:bg-green-20 flex cursor-pointer items-center gap-2 rounded bg-green-100 px-2.5 py-1.5 text-left text-xs text-green-600 transition-colors dark:text-green-400 dark:hover:bg-green-300 dark:hover:text-white">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M20 9c0 .55-.45 1-1 1h-2v2c0 .55-.45 1-1 1s-1-.45-1-1v-2h-2c-.55 0-1-.45-1-1s.45-1 1-1h2V6c0-.55.45-1 1-1s1 .45 1 1v2h2c.55 0 1 .45 1 1zM4 8h3V3H4v5zm-2 9h5v-7H2v7zm14-2c-.55 0-1 .45-1 1v1H9V6h3c.55 0 1-.45 1-1s-.45-1-1-1H9V2c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v6H1c-.55 0-1 .45-1 1v9c0 .55.45 1 1 1h15c.55 0 1-.45 1-1v-2c0-.55-.45-1-1-1z"
                            fill="currentColor" />
                    </svg>
                    <span wire:loading.remove wire:target="downloadPdf">
                        Inventaris
                    </span>
                </a>
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
                            <th class="px-2.5 py-1.5">Tgl. Peminjaman</th>
                            <th class="px-2.5 py-1.5">Tgl. Pengembalian</th>
                            <th class="px-2.5 py-1.5 text-center">Jumlah</th>
                            <th class="px-2.5 py-1.5 text-center">Status</th>
                            <th class="w-20 px-2.5 py-1.5 text-right">Aksi</th>
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
                                                    @if ($peminjaman->status === 'dipinjam')
                                                        <button
                                                            wire:click="downloadPdf({{ $peminjaman->id_peminjaman }})"
                                                            class="flex w-full cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-left text-xs text-green-600 transition-colors hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950/40">
                                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9 10H6" stroke="currentColor"
                                                                    stroke-width="1.5" stroke-linecap="round" />
                                                                <path d="M19 14L5 14" stroke="currentColor"
                                                                    stroke-width="1.5" stroke-linecap="round" />
                                                                <circle cx="17" cy="10" r="1"
                                                                    fill="currentColor" />
                                                                <path d="M15 16.5H9" stroke="currentColor"
                                                                    stroke-width="1.5" stroke-linecap="round" />
                                                                <path d="M13 19H9" stroke="currentColor"
                                                                    stroke-width="1.5" stroke-linecap="round" />
                                                                <path
                                                                    d="M22 12C22 14.8284 22 16.2426 21.1213 17.1213C20.48 17.7626 19.5535 17.9359 18 17.9827M6 17.9827C4.44655 17.9359 3.51998 17.7626 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6H16C18.8284 6 20.2426 6 21.1213 6.87868C21.4211 7.17848 21.6186 7.54062 21.7487 8"
                                                                    stroke="currentColor" stroke-width="1.5"
                                                                    stroke-linecap="round" />
                                                                <path
                                                                    d="M17.9827 6C17.9359 4.44655 17.7626 3.51998 17.1213 2.87868C16.2426 2 14.8284 2 12 2C9.17157 2 7.75736 2 6.87868 2.87868C6.23738 3.51998 6.06413 4.44655 6.01732 6M18 15V16C18 18.8284 18 20.2426 17.1213 21.1213C16.48 21.7626 15.5535 21.9359 14 21.9827M6 15V16C6 18.8284 6 20.2426 6.87868 21.1213C7.51998 21.7626 8.44655 21.9359 10 21.9827"
                                                                    stroke="currentColor" stroke-width="1.5"
                                                                    stroke-linecap="round" />
                                                            </svg>
                                                            <span wire:loading.remove wire:target="downloadPdf">
                                                                Cetak
                                                            </span>
                                                            <span wire:loading wire:target="downloadPdf">
                                                                Sedang Memproses...
                                                            </span>
                                                        </button>
                                                    @endif
                                                    <flux-spacer />
                                                    {{-- Hapus --}}
                                                    <button
                                                        wire:click="deletePeminjaman({{ $peminjaman->id_peminjaman }})"
                                                        wire:confirm="Apakah anda yakin ingin menghapus data ini?"
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
    </div>
</div>
