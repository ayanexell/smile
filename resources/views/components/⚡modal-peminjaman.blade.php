<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Inventaris;
use App\Models\Peminjaman;
use App\Models\User;
use App\Actions\WhatsappAction;
use Illuminate\Support\Facades\Log;

new #[Layout('layouts.guest')] class extends Component {
    public $inventaris;
    public $tgl_peminjaman;
    public $tgl_pengembalian;
    public $jumlah = 0;

    public function rules()
    {
        return [
            'tgl_peminjaman' => 'required|date|after_or_equal:today',
            'tgl_pengembalian' => 'required|date|after:tgl_peminjaman',
            'jumlah' => [
                'required',
                'min:1',
                'max:' . $this->inventaris->jumlah, // fallback agar aman
            ],
        ];
    }
    public function messages()
    {
        return [
            'tgl_peminjaman.required' => 'Tanggal peminjaman wajib diisi.',
            'tgl_peminjaman.date' => 'Format tanggal peminjaman tidak valid.',
            'tgl_peminjaman.after_or_equal' => 'Tanggal peminjaman tidak boleh sebelum hari ini.',
            'tgl_pengembalian.required' => 'Tanggal pengembalian wajib diisi.',
            'tgl_pengembalian.date' => 'Format tanggal pengembalian tidak valid.',
            'tgl_pengembalian.after' => 'Tanggal pengembalian harus setelah tanggal peminjaman.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.min' => 'Jumlah minimal adalah :min.',
            'jumlah.max' => 'Jumlah tidak boleh melebihi stok yang tersedia (:max).',
        ];
    }

    public function addPeminjaman(Inventaris $id)
    {
        $this->inventaris = $id;
    }

    public function submitPeminjaman()
    {
        $this->validate();

        // Simpan peminjaman (sesuaikan dengan struktur tabel Anda)
        Peminjaman::create([
            'user_id' => auth()->id(),
            'inventaris_id' => $this->inventaris->id_inventaris,
            'tgl_peminjaman' => $this->tgl_peminjaman,
            'tgl_pengembalian' => $this->tgl_pengembalian,
            'jumlah' => $this->jumlah,
            'status' => 'menunggu',
        ]);

        $user = auth()->user();
        $admin = User::onlyAdmins()->first();
        $barang = $this->inventaris->nama_barang; // asumsi ada properti nama
        $jumlah = $this->jumlah;
        $tglPinjam = \Carbon\Carbon::parse($this->tgl_peminjaman);
        $tglKembali = \Carbon\Carbon::parse($this->tgl_pengembalian);
        $userPhoneNumber = auth()->user()->no_wa;

        try {
            $messageUser = "Halo {$user->nama_lengkap},\n" . "Peminjaman barang *{$barang}* sebanyak {$jumlah} unit berhasil diajukan.\n" . "📅 Pinjam: {$tglPinjam->translatedFormat('d F Y')}\n" . "📅 Kembali: {$tglKembali->translatedFormat('d F Y')}\n" . "Status: Menunggu persetujuan.\n" . 'Terima kasih.';
            $messageAdmin = "📢 *Notifikasi Peminjaman Baru*\n" . "User: {$admin->nama_lengkap}\n" . "Barang: {$barang}\n" . "Jumlah: {$jumlah}\n" . "Pinjam: {$tglPinjam->translatedFormat('d F Y')}\n" . "Kembali: {$tglKembali->translatedFormat('d F Y')}\n" . "Status: Menunggu.\n" . 'Silakan cek panel admin.';

            $actionUser = new WhatsappAction($userPhoneNumber, $messageUser);
            $actionUser->send();
            $actionAdmin = new WhatsappAction($admin->no_wa, $messageAdmin);
            $actionAdmin->send();
            session()->flash('success', 'Notifikasi telah dikirim ke WhatsApp Anda dan admin.');
        } catch (\Exception $e) {
            // Tangani error jika ada
            session()->flash('notification', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat mengirim notifikasi WhatsApp. Silakan coba lagi.',
            ]);
            Log::error('Gagal mengirim notifikasi WhatsApp: ' . $e->getMessage());
            return;
        }

        // Notifikasi sukses
        session()->flash('notification', [
            'type' => 'success',
            'message' => 'Peminjaman berhasil diajukan!',
        ]);
    }
};
?>

<div class="grid-pattern relative min-h-screen overflow-hidden">
    {{-- MODAL PEMINJAMAN --}}
    <div x-data="{
        show: false,
        successMessage: '',
        errorMessage: '',
        idInventaris: null,
        init() {
            window.addEventListener('modal-peminjaman-user', (e) => {
                this.message = '';
                $wire.addPeminjaman(e.detail.id);
                this.idInventaris = e.detail.id;
                this.errorMessage = '';
                this.show = true;
            });

            // Event sukses dari Livewire
            window.addEventListener('update-success', (e) => {
                this.show = false;
                this.successMessage = e.detail.message;
            });

            // Event error dari Livewire
            window.addEventListener('update-error', (e) => {
                this.errorMessage = e.detail.message;
            });
        },
    }" x-show="show" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4 backdrop-blur-sm"
        @click.self="show = false">

        <div class="w-full max-w-sm overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-stone-900" @click.stop>
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-stone-200 px-5 py-3 dark:border-stone-800">
                <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-200">Ajukan Peminjaman</h3>
                <button @click="show = false"
                    class="rounded-md p-1 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600 dark:hover:bg-stone-800 dark:hover:text-stone-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="relative px-5 py-3">
                {{-- Indikator Loading khusus saat method create berjalan --}}
                <div wire:loading wire:target="addPeminjaman"
                    class="absolute inset-0 z-50 flex items-center justify-center rounded-md bg-white/60 p-5 backdrop-blur-[0.5px] dark:bg-stone-900/60">
                    <div
                        class="flex items-center gap-1.5 rounded-md border border-stone-100 bg-white px-2.5 py-1.5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <svg class="text-sage-600 dark:text-sage-400 h-3.5 w-3.5 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V12H4z"></path>
                        </svg>
                        <span class="text-[10px] font-medium text-stone-600 dark:text-stone-300">
                            {{ __('Mengambil data...') }}
                        </span>
                    </div>
                </div>
                {{-- Info Barang Singkat --}}
                @if ($this->inventaris)
                    <div
                        class="rounded-md border-b border-green-100 bg-green-50 px-5 py-3 dark:border-green-800 dark:bg-green-800/50">
                        <p class="text-xs font-medium text-green-800 dark:text-green-200">{{ $inventaris->nama_barang }}
                        </p>
                        <p class="text-[10px] text-green-500 dark:text-green-400">
                            {{ $inventaris->user->departemen->nama_departemen }}</p>
                    </div>
                @endif
            </div>

            {{-- Pesan Error Global --}}
            <div x-show="errorMessage" x-cloak
                class="rounded-md border border-red-200 bg-red-50 px-2.5 py-1.5 text-[11px] text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300"
                x-text="errorMessage">
            </div>

            {{-- Form Tanggal --}}
            <div class="space-y-4 px-5">
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
                        Kembali</label>
                    <input type="date" wire:model="tgl_pengembalian"
                        class="focus:ring-sage-500 w-full rounded-lg border border-stone-200 bg-white px-3 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                    @error('tgl_pengembalian')
                        <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-0.5 block text-[11px] font-medium text-stone-600 dark:text-stone-400">
                        {{ __('Jumlah') }}
                    </label>
                    <input wire:model="jumlah" type="number" placeholder="Jumlah barang yang dipinjam"
                        class="focus:border-sage-500 focus:ring-sage-500 w-full rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100" />
                    @error('jumlah')
                        <p class="mt-0.5 text-[10px] text-red-500">{{ $message }}</p>
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
                <button type="button" wire:click="submitPeminjaman" wire:loading.attr="disabled"
                    wire:target="submitPeminjaman"
                    class="bg-sage-600 hover:bg-sage-700 focus:ring-sage-500 rounded-lg px-4 py-1.5 text-xs font-medium text-white transition focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:opacity-50">
                    {{-- Teks normal --}}
                    <span wire:loading.remove wire:target="submitPeminjaman">Simpan</span>
                    {{-- Spinner saat loading --}}
                    <span wire:loading wire:target="submitPeminjaman">
                        <svg class="inline h-3 w-3 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
