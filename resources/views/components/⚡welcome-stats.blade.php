<?php

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Inventaris;
use App\Models\Peminjaman;

new class extends Component {
    public function render()
    {
        $now = Carbon::now();
        $sevenDaysAgo = $now->copy()->subDays(7);

        // ---------- Total Aset (jumlah total barang) ----------
        $totalCurrent = Inventaris::sum('jumlah');

        // Estimasi jumlah total 7 hari lalu: hanya barang yang sudah ada saat itu
        $totalSevenDaysAgo = Inventaris::where('created_at', '<=', $sevenDaysAgo)->sum('jumlah');

        $totalChange = $this->calcPercentageChange($totalCurrent, $totalSevenDaysAgo);

        // ---------- Dipinjam (jumlah yang sedang dipinjam) ----------
        $dipinjamCurrent = Peminjaman::where('tgl_pengembalian', '>', $now)->sum('jumlah');

        $dipinjamSevenDaysAgo = Peminjaman::where('tgl_peminjaman', '<=', $sevenDaysAgo)->where('tgl_pengembalian', '>', $sevenDaysAgo)->sum('jumlah');

        $dipinjamChange = $this->calcPercentageChange($dipinjamCurrent, $dipinjamSevenDaysAgo);

        // ---------- Kondisi Baik ----------
        $totalJumlah = Inventaris::sum('jumlah');
        $baikCurrent = Inventaris::where('kondisi', 'baik')->sum('jumlah');
        $persenBaikCurrent = $totalJumlah > 0 ? round(($baikCurrent / $totalJumlah) * 100) : 0;

        // Persentase 7 hari lalu (hanya dari barang yang sudah ada saat itu)
        $totalSevenDays = Inventaris::where('created_at', '<=', $sevenDaysAgo)->sum('jumlah');
        $baikSevenDays = Inventaris::where('created_at', '<=', $sevenDaysAgo)->where('kondisi', 'baik')->sum('jumlah');
        $persenBaikSevenDays = $totalSevenDays > 0 ? round(($baikSevenDays / $totalSevenDays) * 100) : 0;

        $baikChange = $persenBaikCurrent - $persenBaikSevenDays;
        $baikChangeFormatted = ($baikChange >= 0 ? '+' : '') . $baikChange . '%';

        // ---------- Perlu Servis (barang rusak) ----------
        $totalBarangbisaDipinjam = Inventaris::where('dpt_dipinjam', 1)->sum('jumlah');

        $dptDipinjam7hari = Inventaris::where('created_at', '<=', $sevenDaysAgo)->where('dpt_dipinjam', 1)->sum('jumlah');

        $rusakChange = $totalBarangbisaDipinjam - $dptDipinjam7hari;
        $rusakChangeFormatted = ($rusakChange >= 0 ? '+' : '') . $rusakChange;

        // ---------- Mini Chart (7 hari terakhir) ----------
        $endDate = $now->copy()->endOfDay();
        $startDate = $now->copy()->subDays(6)->startOfDay();

        $loansPerDay = Peminjaman::whereBetween('tgl_peminjaman', [$startDate, $endDate])
            ->selectRaw('DATE(tgl_peminjaman) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; // Sunday = 0
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            $count = $loansPerDay[$date] ?? 0;
            $dayIndex = Carbon::parse($date)->dayOfWeek; // 0 = Minggu
            $dayLabel = $dayNames[$dayIndex];
            $chartData[] = [
                'day' => $dayLabel,
                'count' => $count,
            ];
        }

        $maxCount = max(array_column($chartData, 'count')) ?: 1;
        foreach ($chartData as &$day) {
            $day['height'] = $maxCount > 0 ? round(($day['count'] / $maxCount) * 100) : 10;
        }

        // Susun kartu statistik
        $stats = [
            ['Total Aset', number_format($totalCurrent, 0, ',', '.'), $totalChange, 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'sage'],
            ['Dipinjam', number_format($dipinjamCurrent, 0, ',', '.'), $dipinjamChange, 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'amber'],
            ['Kondisi Baik', $persenBaikCurrent . '%', $baikChangeFormatted, 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'emerald'],
            [
                'Dapat Dipinjam',
                number_format($totalBarangbisaDipinjam, 0, ',', '.'),
                $rusakChangeFormatted,
                'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"
',
                'cyan',
            ],
        ];

        return $this->view([
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }

    private function calcPercentageChange($current, $previous): string
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }
        $change = (($current - $previous) / $previous) * 100;
        $sign = $change >= 0 ? '+' : '';
        return $sign . round($change) . '%';
    }
};
?>

<div>
    <div class="mb-5 grid grid-cols-2 gap-3">
        @foreach ($stats as $stat)
            <div class="stat-card relative rounded-xl bg-stone-50 p-3.5 dark:bg-stone-800/50">
                <div class="mb-2 flex items-start justify-between">
                    <div
                        class="{{ match ($stat[4]) {
                            'sage' => 'bg-sage-100 dark:bg-sage-900/30 text-sage-600 dark:text-sage-400',
                            'amber' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
                            'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
                            'cyan' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
                            default => '',
                        } }} rounded-lg p-1.5">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $stat[3] }}" />
                        </svg>
                    </div>
                    <span
                        class="{{ str_starts_with($stat[2], '+') ? 'text-emerald-500' : 'text-cyan-500' }} font-mono text-[10px]">
                        {{ $stat[2] }}
                    </span>
                </div>
                <p class="font-display text-xl font-bold text-stone-800 dark:text-stone-100">{{ $stat[1] }}</p>
                <p class="mt-0.5 text-[11px] text-stone-500 dark:text-stone-400">{{ $stat[0] }}</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-xl bg-stone-50 p-3.5 dark:bg-stone-800/50">
        <p class="mb-3 font-mono text-[11px] uppercase tracking-widest text-stone-400 dark:text-stone-500">
            {{ __('Aktivitas 7 Hari Terakhir') }}
        </p>
        <div class="flex h-12 items-end gap-1.5">
            @foreach ($chartData as $day)
                <div class="bg-sage-200 dark:bg-sage-800/60 hover:bg-sage-400 dark:hover:bg-sage-500 flex-1 cursor-pointer rounded-t-sm transition-all"
                    style="height: {{ $day['height'] }}%"></div>
            @endforeach
        </div>
        <div class="mt-1.5 flex justify-between">
            @foreach ($chartData as $day)
                <span class="font-mono text-[9px] text-stone-400 dark:text-stone-600">{{ $day['day'] }}</span>
            @endforeach
        </div>
    </div>
</div>
