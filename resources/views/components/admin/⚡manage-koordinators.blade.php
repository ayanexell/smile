<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Roles;
use App\Models\Departemens;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterGender = '';
    public string $filterDepartemen = '';
    public bool $showDeleteModal = false;
    public ?int $deleteTargetId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterGender(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteTargetId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        User::findOrFail($this->deleteTargetId)->delete();
        $this->showDeleteModal = false;
        $this->deleteTargetId = null;
        session()->flash('success', 'User berhasil dihapus.');
    }

    public function render()
    {
        $users = User::onlyKoordinators()
            ->when(
                $this->search,
                fn($q) => $q
                    ->where('nama_lengkap', 'like', "%{$this->search}%")
                    ->orWhere('nik', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"),
            )
            ->when($this->filterGender, fn($q) => $q->where('jenis_kelamin', $this->filterGender))
            ->when($this->filterDepartemen, fn($q) => $q->where('departemen_id', $this->filterDepartemen))
            ->latest()
            ->paginate(10);

        return $this->view([
            'users' => $users,
            'departemens' => Departemens::all(),
        ]);
    }
};
?>

<div class="min-h-screen bg-stone-100 dark:bg-stone-950">

    {{-- ── PAGE HEADER ── --}}
    <div class="bg-white dark:bg-stone-900 border-b border-stone-200 dark:border-stone-800 px-5 py-3.5">
        <div class="max-w-screen-xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span
                        class="font-mono text-[9px] tracking-widest uppercase text-sage-600 dark:text-sage-400">{{ __('Manajemen') }}</span>
                </div>
                <h1 class="font-display text-xl font-semibold text-stone-800 dark:text-stone-100">
                    {{ __('Daftar Koordinator') }}</h1>
                <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">
                    {{ __('Kelola semua akun koordinator sistem SMILE') }}</p>
            </div>
            <a href="#"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah User') }}
            </a>
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

                {{-- Filter Departemen --}}
                <select wire:model.live="filterDepartemen"
                    class="py-1.5 px-2.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-700 dark:text-stone-300 focus:outline-none focus:ring-1 focus:ring-sage-500 transition">
                    <option value="">{{ __('Semua Departemen') }}</option>
                    @foreach ($departemens as $dep)
                        <option value="{{ $dep->id_departemen }}">{{ $dep->nama_departemen }}</option>
                    @endforeach
                </select>

                {{-- Filter Gender --}}
                <select wire:model.live="filterGender"
                    class="py-1.5 px-2.5 text-xs rounded-lg border border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800 text-stone-700 dark:text-stone-300 focus:outline-none focus:ring-1 focus:ring-sage-500 transition">
                    <option value="">{{ __('Semua Gender') }}</option>
                    <option value="laki-laki">{{ __('Laki-laki') }}</option>
                    <option value="perempuan">{{ __('Perempuan') }}</option>
                </select>

            </div>
        </div>

        {{-- ── TABLE CARD ── --}}
        <div
            class="bg-white dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden px-4">

            {{-- Table meta --}}
            <div class="py-2 border-b border-stone-100 dark:border-stone-800 flex items-center justify-between">
                <span class="text-[10px] font-mono text-stone-400 dark:text-stone-500 uppercase tracking-wider">
                    {{ $users->total() }} {{ __('pengguna ditemukan') }}
                </span>
                <div wire:loading class="flex items-center gap-1 text-[11px] text-sage-600 dark:text-sage-400">
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    {{ __('Memuat…') }}
                </div>
            </div>

            {{-- Scrollable table wrapper --}}
            <div class="overflow-x-auto border dark:border-stone-800 rounded-xl bg-white dark:bg-stone-900 shadow-sm">
                <table class="w-full text-[11px] text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-stone-50 dark:bg-stone-800/50 border-b border-stone-200 dark:border-stone-800 text-stone-500 dark:text-stone-400 font-semibold uppercase tracking-wider">
                            <th class="px-2.5 py-1.5 w-6 text-center">#</th>
                            <th class="px-2.5 py-1.5">{{ __('Pengguna') }}</th>
                            <th class="px-2.5 py-1.5 hidden sm:table-cell">{{ __('NIK') }}</th>
                            <th class="px-2.5 py-1.5">{{ __('Departemen') }}</th>
                            <th class="px-2.5 py-1.5 hidden md:table-cell">{{ __('Tgl. Lahir') }}</th>
                            <th class="px-2.5 py-1.5 hidden sm:table-cell w-10 text-center">{{ __('JK') }}</th>
                            <th class="px-2.5 py-1.5 hidden xl:table-cell">{{ __('Alamat') }}</th>
                            <th class="px-2.5 py-1.5 hidden lg:table-cell">{{ __('Pekerjaan') }}</th>
                            <th class="px-2.5 py-1.5 text-right w-20">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                        @forelse ($users as $user)
                            <tr class="hover:bg-stone-50 dark:hover:bg-stone-800/30 transition-colors group">

                                {{-- No --}}
                                <td class="px-2.5 py-1.5 text-stone-400 dark:text-stone-600 font-mono text-center">
                                    {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                </td>

                                {{-- Nama, Avatar + Email (Digabung agar hemat space) --}}
                                <td class="px-2.5 py-1.5">
                                    <div class="flex items-center gap-2 max-w-[180px] sm:max-w-xs">
                                        <div
                                            class="w-5 h-5 rounded-full bg-sage-100 dark:bg-sage-900/60 flex items-center justify-center flex-shrink-0 text-sage-700 dark:text-sage-400 font-bold text-[9px] uppercase">
                                            {{ mb_substr($user->nama_lengkap, 0, 2) }}
                                        </div>
                                        <div class="truncate">
                                            <div class="font-medium text-stone-800 dark:text-stone-200 truncate">
                                                {{ $user->nama_lengkap }}</div>
                                            <div class="text-[10px] text-stone-400 dark:text-stone-500 truncate"
                                                title="{{ $user->email }}">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- NIK (Sembunyi di HP) --}}
                                <td
                                    class="px-2.5 py-1.5 font-mono text-stone-500 dark:text-stone-400 hidden sm:table-cell">
                                    {{ $user->nik }}
                                </td>

                                {{-- Departemen & Role (Digabung vertikal) --}}
                                <td class="px-2.5 py-1.5 space-y-0.5">
                                    <div class="text-stone-700 dark:text-stone-300 font-medium">
                                        {{ $user->departemen->singkatan }}</div>
                                </td>

                                {{-- Tgl Lahir (Sembunyi di HP/Tablet) --}}
                                <td
                                    class="px-2.5 py-1.5 text-stone-500 dark:text-stone-400 whitespace-nowrap hidden md:table-cell">
                                    {{ \Carbon\Carbon::parse($user->tgl_lahir)->translatedFormat('d M Y') }}
                                </td>

                                {{-- Gender (Dipersingkat) --}}
                                <td class="px-2.5 py-1.5 text-center hidden sm:table-cell">
                                    @if ($user->jenis_kelamin === 'laki-laki')
                                        <span class="text-blue-600 dark:text-blue-400 font-semibold"
                                            title="Laki-laki">L</span>
                                    @else
                                        <span class="text-pink-600 dark:text-pink-400 font-semibold"
                                            title="Perempuan">P</span>
                                    @endif
                                </td>

                                {{-- Alamat (Hanya tampil di layar ultra lebar) --}}
                                <td class="px-2.5 py-1.5 text-stone-500 dark:text-stone-500 max-w-[140px] truncate hidden xl:table-cell"
                                    title="{{ $user->alamat }}">
                                    {{ $user->alamat }}
                                </td>

                                {{-- Pekerjaan (Hanya tampil di desktop) --}}
                                <td
                                    class="px-2.5 py-1.5 text-stone-500 dark:text-stone-400 truncate hidden lg:table-cell">
                                    {{ __($user->pekerjaan) }}
                                </td>

                                {{-- Aksi --}}
                                <td class="px-2.5 py-1.5 text-right">
                                    <div
                                        class="flex items-center justify-end gap-0.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                        <a href="#"
                                            class="p-1 rounded text-stone-400 hover:text-stone-700 dark:hover:text-stone-200 hover:bg-stone-100 dark:hover:bg-stone-800"
                                            title="Detail">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="#"
                                            class="p-1 rounded text-sage-600 hover:text-sage-800 dark:text-sage-400 dark:hover:text-sage-200 hover:bg-sage-50 dark:hover:bg-sage-950/50"
                                            title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                            class="p-1 rounded text-red-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/50"
                                            title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
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
            <div
                class="px-3 py-2 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between dynamic-pagination">
                <div class="w-full text-xs transform scale-95 origin-left text-stone-500 dark:text-stone-400">
                    {{ $users->links('pagination::tailwind') }}
                </div>
            </div>
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
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-stone-800 dark:text-stone-100 mb-0.5">Hapus Pengguna</h3>
                        <p class="text-xs text-stone-500 dark:text-stone-400">Data pengguna akan dihapus permanen dari
                            sistem.</p>
                    </div>
                </div>
                <div class="flex gap-2 mt-5">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 px-3 py-2 text-xs font-medium rounded-lg border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-800 transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteUser"
                        class="flex-1 px-3 py-2 text-xs font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
