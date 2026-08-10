<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventaris;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('nama_role', 'User');
        })->get();
        $inventarisBorrowable = Inventaris::where('dpt_dipinjam', true)
            ->where('kondisi', 'baik')
            ->get();

        // Create peminjaman for each user
        foreach ($users as $user) {
            // Each user has 1-4 peminjamans
            $count = rand(1, 4);

            for ($i = 0; $i < $count; $i++) {
                if ($inventarisBorrowable->isNotEmpty()) {
                    $inventaris = $inventarisBorrowable->random();
                    Peminjaman::factory()
                        ->oleh($user)
                        ->untuk($inventaris)
                        ->create();
                }
            }
        }
        $now = Carbon::now();
        for ($i = 0; $i < 5; $i++) {
            $user = $users->random();
            $inventaris = $inventarisBorrowable->random();

            Peminjaman::factory()
                ->oleh($user)
                ->untuk($inventaris)
                ->masihDipinjam()         // status 'dipinjam', tgl_pengembalian > now
                ->denganJumlah(rand(1, 3))
                ->create();
        }
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dailyCount = rand(2, 4);

            for ($j = 0; $j < $dailyCount; $j++) {
                $user = $users->random();
                $inventaris = $inventarisBorrowable->random();

                Peminjaman::factory()
                    ->oleh($user)
                    ->untuk($inventaris)
                    ->denganTanggalPinjam($date)          // paksa tgl_peminjaman ke hari itu
                    ->denganJumlah(1)
                    ->create();
            }
        }
    }
}
