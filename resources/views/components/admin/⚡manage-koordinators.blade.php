<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Roles;
use App\Models\Departemens;
use Livewire\Attributes\Title;

new #[Title('Manage Koordinator')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterGender = '';
    public string $filterDepartemen = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterGender(): void
    {
        $this->resetPage();
    }

    public function deleteKoordinator(User $id): void
    {
        $id->delete();
        session()->flash('success', 'Koordinator berhasil dihapus.');
    }

    public function updateKoordinator($id)
    {
        return $this->dispatch('edit-koordinator', id: $id);
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

    <x-page-header title="Daftar Koordinator" leading="Kelola semua akun Koordinator sistem SMILE" />

    <div class="mx-auto mt-2 max-w-7xl space-y-2">

        <div x-data="{ show: true }" x-show="show"
            class="flex select-none items-center gap-2.5 rounded-lg border border-amber-200 bg-amber-50 py-2 pl-3 pr-2.5 text-amber-700 shadow-xl shadow-stone-200/50 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-300 dark:shadow-none">
            <div class="shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <div class="flex-1 text-[11px] font-medium leading-normal">
                <span>{{ __('Harap diperhatikan bahwa menghapus akun Koordinator dapat menyebabkan semua Inventaris yang berhubungan dengan akun tersebut akan dihapus secara permanen dari sistem. Sebelum menghapus pastikan Koordinator belum pernah menyimpan inventaris ke dalam sistem. Hubungi Super Admin untuk informasi lebih lanjut') }}</span>
            </div>
            <button @click="show = false"
                class="shrink-0 rounded p-1 text-stone-400 transition-colors hover:text-stone-600 dark:hover:text-stone-200">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        {{-- ── FLASH MESSAGE ── --}}
        @if (session('success'))
            <div
                class="bg-sage-50 dark:bg-sage-950 border-sage-200 dark:border-sage-800 text-sage-700 dark:text-sage-300 flex items-center gap-2.5 rounded-xl border px-3 py-2 text-xs">
                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
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

                {{-- Filter Departemen --}}
                <select wire:model.live="filterDepartemen"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">{{ __('Semua Departemen') }}</option>
                    @foreach ($departemens as $dep)
                        <option value="{{ $dep->id_departemen }}">{{ $dep->nama_departemen }}</option>
                    @endforeach
                </select>

                {{-- Filter Gender --}}
                <select wire:model.live="filterGender"
                    class="focus:ring-sage-500 rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-1.5 text-xs text-stone-700 transition focus:outline-none focus:ring-1 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    <option value="">{{ __('Semua Gender') }}</option>
                    <option value="laki-laki">{{ __('Laki-laki') }}</option>
                    <option value="perempuan">{{ __('Perempuan') }}</option>
                </select>

                <a x-data x-on:click="$dispatch('add-koordinator-modal')"
                    class="bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 inline-flex cursor-pointer items-center gap-1.5 whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold text-white shadow-sm transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Tambah Koordinator') }}
                </a>

            </div>
        </div>

        {{-- ── TABLE CARD ── --}}
        <div
            class="overflow-hidden rounded-xl border border-stone-200 bg-white px-4 dark:border-stone-800 dark:bg-stone-900">

            {{-- Table meta --}}
            <div class="flex items-center justify-between border-b border-stone-100 py-2 dark:border-stone-800">
                <span class="font-mono text-[10px] uppercase tracking-wider text-stone-400 dark:text-stone-500">
                    {{ $users->total() }} {{ __('pengguna ditemukan') }}
                </span>
                <div wire:loading class="text-sage-600 dark:text-sage-400 flex items-center gap-1 text-[11px]">
                    <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    {{ __('Memuat…') }}
                </div>
            </div>

            {{-- Scrollable table wrapper --}}
            <div class="overflow-x-auto rounded-xl border bg-white shadow-sm dark:border-stone-800 dark:bg-stone-900">
                <table class="w-full border-collapse text-left text-[11px]">
                    <thead>
                        <tr
                            class="border-b border-stone-200 bg-stone-50 font-semibold uppercase tracking-wider text-stone-500 dark:border-stone-800 dark:bg-stone-800/50 dark:text-stone-400">
                            <th class="w-6 px-2.5 py-1.5 text-center">#</th>
                            <th class="px-2.5 py-1.5">{{ __('Pengguna') }}</th>
                            <th class="hidden px-2.5 py-1.5 sm:table-cell">{{ __('NIK') }}</th>
                            <th class="px-2.5 py-1.5">{{ __('Departemen') }}</th>
                            <th class="hidden px-2.5 py-1.5 md:table-cell">{{ __('Tgl. Lahir') }}</th>
                            <th class="hidden w-10 px-2.5 py-1.5 text-center sm:table-cell">{{ __('JK') }}</th>
                            <th class="hidden px-2.5 py-1.5 xl:table-cell">{{ __('Alamat') }}</th>
                            <th class="hidden px-2.5 py-1.5 lg:table-cell">{{ __('Jabatan') }}</th>
                            <th class="w-20 px-2.5 py-1.5 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-stone-800/60">
                        @forelse ($users as $user)
                            <tr class="group transition-colors hover:bg-stone-50 dark:hover:bg-stone-800/30">

                                {{-- No --}}
                                <td class="px-2.5 py-1.5 text-center font-mono text-stone-400 dark:text-stone-600">
                                    {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                </td>

                                {{-- Nama, Avatar + Email (Digabung agar hemat space) --}}
                                <td class="px-2.5 py-1.5">
                                    <div class="max-w-45 flex items-center gap-2 sm:max-w-xs">
                                        <div
                                            class="bg-sage-100 dark:bg-sage-900/60 text-sage-700 dark:text-sage-400 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[9px] font-bold uppercase">
                                            {{ mb_substr($user->nama_lengkap, 0, 2) }}
                                        </div>
                                        <div class="truncate">
                                            <div class="truncate font-medium text-stone-800 dark:text-stone-200">
                                                {{ $user->nama_lengkap }}
                                            </div>
                                            <div class="truncate text-[10px] text-stone-400 dark:text-stone-500"
                                                title="{{ $user->email }}">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- NIK (Sembunyi di HP) --}}
                                <td
                                    class="hidden px-2.5 py-1.5 font-mono text-stone-500 sm:table-cell dark:text-stone-400">
                                    {{ $user->nik }}
                                </td>

                                {{-- Departemen & Role (Digabung vertikal) --}}
                                <td class="space-y-0.5 px-2.5 py-1.5">
                                    <div class="font-medium text-stone-700 dark:text-stone-300">
                                        {{ $user->departemen->singkatan ?? '-' }}
                                    </div>
                                </td>

                                {{-- Tgl Lahir (Sembunyi di HP/Tablet) --}}
                                <td
                                    class="hidden whitespace-nowrap px-2.5 py-1.5 text-stone-500 md:table-cell dark:text-stone-400">
                                    {{ \Carbon\Carbon::parse($user->tgl_lahir)->translatedFormat('d M Y') }}
                                </td>

                                {{-- Gender (Dipersingkat) --}}
                                <td class="hidden px-2.5 py-1.5 text-center sm:table-cell">
                                    @if ($user->jenis_kelamin === 'laki-laki')
                                        <span class="font-semibold text-blue-600 dark:text-blue-400"
                                            title="Laki-laki">L</span>
                                    @else
                                        <span class="font-semibold text-pink-600 dark:text-pink-400"
                                            title="Perempuan">P</span>
                                    @endif
                                </td>

                                {{-- Alamat (Hanya tampil di layar ultra lebar) --}}
                                <td class="max-w-35 hidden truncate px-2.5 py-1.5 text-stone-500 xl:table-cell dark:text-stone-500"
                                    title="{{ $user->alamat }}">
                                    {{ $user->alamat }}
                                </td>

                                {{-- Pekerjaan (Hanya tampil di desktop) --}}
                                <td
                                    class="hidden truncate px-2.5 py-1.5 text-stone-500 lg:table-cell dark:text-stone-400">
                                    {{ __($user->pekerjaan) }}
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

                                                    {{-- Edit --}}
                                                    <button x-data
                                                        x-on:click="$dispatch('edit-koordinator-modal', {
                                                                                                                            userId: {{ $user->id_user }},
                                                                                                                        })"
                                                        @click="open = false"
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
                                                    <button x-data
                                                        x-on:click="$dispatch('open-delete-modal', { id: {{ $user->id_user }} })"
                                                        @click="open = false"
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
            <div
                class="dynamic-pagination flex items-center justify-between border-t border-stone-100 px-3 py-2 dark:border-stone-800">
                <div class="w-full origin-left scale-95 transform text-xs text-stone-500 dark:text-stone-400">
                    {{ $users->links('pagination::tailwind') }}
                </div>
            </div>
        </div>

    </div>

    <livewire:admin.koordinators.add-koordinator />
    <livewire:admin.koordinators.edit-koordinator />
    <x-modal-hapus modal_name="open-delete-modal" action_hapus="deleteKoordinator" title="Hapus Koordinator"
        description="Data koordinator akan dihapus permanen dari sistem." />

</div>
