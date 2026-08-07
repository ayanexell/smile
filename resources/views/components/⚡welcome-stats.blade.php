<?php

use Livewire\Component;

new class extends Component {
    public function render()
    {
        $stats = [['Total Aset', '1,248', '+12%', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'sage'], ['Dipinjam', '84', '-3%', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'amber'], ['Kondisi Baik', '96%', '+2%', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'emerald'], ['Perlu Servis', '23', '+5', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'rose']];
        return $this->view([
            'stats' => $stats,
        ]);
    }
};
?>

<div>
    <div class="mb-5 grid grid-cols-2 gap-3">
        @foreach ($stats as $stat)
            <div class="stat-card relative rounded-xl bg-stone-50 p-3.5 dark:bg-stone-800/50">
                <div class="mb-2 flex items-start justify-between">
                    <div
                        class="{{ $stat[4] === 'sage' ? 'bg-sage-100 dark:bg-sage-900/30 text-sage-600 dark:text-sage-400' : '' }} {{ $stat[4] === 'amber' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }} {{ $stat[4] === 'emerald' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }} {{ $stat[4] === 'rose' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' : '' }} rounded-lg p-1.5">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $stat[3] }}" />
                        </svg>
                    </div>
                    <span
                        class="{{ str_starts_with($stat[2], '+') ? 'text-emerald-500' : 'text-rose-500' }} font-mono text-[10px]">
                        {{ $stat[2] }}
                    </span>
                </div>
                <p class="font-display text-xl font-bold text-stone-800 dark:text-stone-100">{{ $stat[1] }}</p>
                <p class="mt-0.5 text-[11px] text-stone-500 dark:text-stone-400">{{ $stat[0] }}</p>
            </div>
        @endforeach
        {{-- Mini Chart Bar --}}
    </div>
    <div class="rounded-xl bg-stone-50 p-3.5 dark:bg-stone-800/50">
        <p class="mb-3 font-mono text-[11px] uppercase tracking-widest text-stone-400 dark:text-stone-500">
            {{ __('Aktivitas 7 Hari Terakhir') }}</p>
        <div class="flex h-12 items-end gap-1.5">
            @foreach (['Sen' => 40, 'Sel' => 65, 'Rab' => 45, 'Kam' => 80, 'Jum' => 55, 'Sab' => 70, 'Min' => 30] as $key => $height)
                <div class="bg-sage-200 dark:bg-sage-800/60 hover:bg-sage-400 dark:hover:bg-sage-500 flex-1 cursor-pointer rounded-t-sm transition-all"
                    style="height: {{ $height }}%"></div>
            @endforeach
        </div>
        <div class="mt-1.5 flex justify-between">
            @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                <span class="font-mono text-[9px] text-stone-400 dark:text-stone-600">{{ __($day) }}</span>
            @endforeach
        </div>
    </div>
</div>
