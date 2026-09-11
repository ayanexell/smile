<?php

use Livewire\Component;
use App\Models\Inventaris;

new class extends Component {
    public function render()
    {
        return $this->view([
            'topBorrowedItems' => Inventaris::onlyDipinjamkan()->orderBy('frequensi_peminjaman', 'asc')->take(8)->get(),
        ]);
    }
};
?>

<div>
    <section id="fitur" class="bg-white py-24 transition-colors duration-500 dark:bg-stone-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Section Header --}}
            <div class="mx-auto mb-16 max-w-2xl text-center">
                <p class="text-sage-600 dark:text-sage-400 mb-3 font-mono text-xs uppercase tracking-[0.3em]">
                    {{ __('Statistik Peminjaman') }}</p>
                <h2 class="font-display mb-4 text-3xl font-bold text-stone-900 sm:text-4xl dark:text-stone-50">
                    {{ __('Inventaris') }} <span
                        class="text-sage-600 dark:text-sage-400 italic">{{ __('Paling Sering Dipinjam') }}</span>
                </h2>
                <p class="leading-relaxed text-stone-500 dark:text-stone-400">
                    {{ __('Delapan aset dengan tingkat peminjaman tertinggi berdasarkan aktivitas sistem saat ini.') }}
                </p>
            </div>

            {{-- Inventory Grid --}}
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($topBorrowedItems as $index => $item)
                    <div
                        class="group relative overflow-hidden rounded-2xl border border-stone-200/80 bg-stone-50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-stone-900/5 dark:border-stone-700/50 dark:bg-stone-800/50 dark:hover:shadow-stone-950/50">

                        {{-- Visual Header --}}
                        <div
                            class="bg-sage-100 dark:bg-sage-900/30 relative flex h-32 w-full items-center justify-center">
                            @if ($item->img_path && Storage::exists($item->img_path))
                                <img src="{{ Storage::url($item->img_path) }}" alt="{{ $item->nama_barang }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div
                                    class="bg-sage-100 dark:bg-sage-900/40 flex h-1/2 w-1/2 shrink-0 items-center justify-center rounded-lg border border-stone-200 dark:border-stone-700">
                                    <svg class="text-sage-500 dark:text-sage-400 w-1/2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Rank Badge --}}
                            <span
                                class="absolute left-3 top-3 inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 font-mono text-[10px] font-semibold text-stone-600 shadow-sm dark:bg-stone-900/80 dark:text-stone-300">
                                #{{ $index + 1 }}
                            </span>

                            {{-- Condition Badge --}}
                            <span
                                class="{{ $item[3] === 'Baik' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' }} absolute right-3 top-3 rounded-full px-2.5 py-1 text-[10px] font-medium">
                                {{ __($item->kondisi) }}
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="p-4">
                            <p
                                class="text-sage-600 dark:text-sage-400 mb-1 font-mono text-[11px] uppercase tracking-wide">
                                {{ __($item->tipe) }}</p>
                            <h3 class="font-display mb-3 line-clamp-1 font-semibold text-stone-800 dark:text-stone-100">
                                {{ __($item->nama_barang) }}</h3>

                            <div
                                class="flex items-center justify-between border-t border-stone-200/70 pt-3 dark:border-stone-700/50">
                                <div class="flex items-center gap-1.5 text-xs text-stone-500 dark:text-stone-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    {{ $item->frequensi_peminjaman ?? 0 }}x {{ __('Dipinjam') }}
                                </div>
                                <a href="{{ route('list-inventaris') }}"
                                    class="text-sage-600 dark:text-sage-400 text-xs font-medium hover:underline">
                                    {{ __('Detail') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- View All Link --}}
            <div class="mt-12 flex justify-center">
                <a href="{{ route('list-inventaris') }}"
                    class="hover:border-sage-300 dark:hover:border-sage-600 hover:text-sage-700 dark:hover:text-sage-300 inline-flex items-center justify-center gap-2.5 rounded-xl border border-stone-200 bg-white px-5 py-2 text-sm font-medium text-stone-700 transition-all duration-200 hover:-translate-y-0.5 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-300">
                    {{ __('Lihat Semua Inventaris') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
</div>
