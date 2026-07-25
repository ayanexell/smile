<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\Inventaris;
use App\Models\Peminjaman;

new #[Layout('layouts.guest')] class extends Component {
    public $showModal = false;
    public $inventaris;
    #[Validate('required|date|after_or_equal:today')]
    public $tgl_peminjaman;
    #[Validate('required|date|after:tgl_peminjaman')]
    public $tgl_pengembalian;

    protected $messages = [
        'tgl_peminjaman.after_or_equal' => 'Tanggal pinjam minimal hari ini.',
        'tgl_pengembalian.after' => 'Tanggal kembali harus setelah tanggal pinjam.',
    ];

    #[On('openModalPeminjaman')]
    public function showModal(Inventaris $id)
    {
        $this->inventaris = $id;
        $this->showModal = true;
    }

    public function submitPeminjaman()
    {
        $this->validate();

        // Simpan peminjaman (sesuaikan dengan struktur tabel Anda)
        Peminjaman::create([
            'user_id' => auth()->id(),
            'inventaris_id' => $this->inventaris->id_inventaris,
            'tanggal_pinjam' => $this->tgl_peminjaman,
            'tanggal_kembali' => $this->tgl_pengembalian,
            'status' => 'menunggu',
        ]);

        // Notifikasi sukses
        session()->flash('notification', [
            'type' => 'success',
            'message' => 'Peminjaman berhasil diajukan!',
        ]);
        $this->showModal = false;
    }
};
?>

<div class="grid-pattern relative min-h-screen overflow-hidden">
    {{-- MODAL PEMINJAMAN --}}
    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4 backdrop-blur-sm"
        @click.self="show = false">

        <div class="w-full max-w-sm overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-stone-900" @click.stop>
            @if ($inventaris)
                {{-- Header --}}
                <div
                    class="flex items-center justify-between border-b border-stone-200 px-5 py-3 dark:border-stone-800">
                    <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-200">Ajukan Peminjaman</h3>
                    <button @click="show = false"
                        class="rounded-md p-1 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600 dark:hover:bg-stone-800 dark:hover:text-stone-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Info Barang Singkat --}}
                <div class="border-b border-stone-100 bg-stone-50 px-5 py-3 dark:border-stone-800 dark:bg-stone-800/50">
                    <p class="text-xs font-medium text-stone-800 dark:text-stone-200">{{ $inventaris->nama_barang }}</p>
                    <p class="text-[10px] text-stone-500 dark:text-stone-400">
                        {{ $inventaris->departemen->nama_departemen }}</p>
                </div>

                {{-- Form Tanggal --}}
                <div class="space-y-4 p-5">
                    <div>
                        <label class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Tanggal
                            Pinjam</label>
                        <input type="date" wire:model="tgl_peminjaman"
                            class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-white px-3 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                        @error('tgl_peminjaman')
                            <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-medium text-stone-600 dark:text-stone-400">Tanggal
                            Kembali (rencana)</label>
                        <input type="date" wire:model="tgl_pengembalian"
                            class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-white px-3 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                        @error('tgl_pengembalian')
                            <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="flex justify-end gap-2 border-t border-stone-100 bg-stone-50 px-5 py-3 dark:border-stone-800 dark:bg-stone-800/30">
                    <button type="button" @click="show = false"
                        class="rounded-lg border border-stone-200 px-4 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-100 dark:border-stone-700 dark:text-stone-400 dark:hover:bg-stone-700">
                        Batal
                    </button>
                    <button type="button" wire:click="submitPeminjaman"
                        class="bg-sage-600 hover:bg-sage-700 focus:ring-sage-500 rounded-lg px-4 py-1.5 text-xs font-medium text-white transition focus:outline-none focus:ring-2">
                        Ajukan
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
