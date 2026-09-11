<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
#[Signature('app:auto-return-overdue-peminjaman')]
#[Description('Command description')]
class AutoReturnOverduePeminjaman extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil peminjaman yang masih 'dipinjam' dan tgl_pengembalian <= sekarang
        $overdue = Peminjaman::where('status', 'dipinjam')
            ->where('tgl_pengembalian', '<=', now())
            ->lockForUpdate()
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('Tidak ada peminjaman yang terlambat.');
            return;
        }

        foreach ($overdue as $peminjaman) {
            DB::transaction(function () use ($peminjaman) {
                // Update status dan hibah default (0)
                $peminjaman->update([
                    'status' => 'terlambat',
                    'hibah' => 0, // atau null
                ]);

                // Kembalikan stok inventaris
                $peminjaman->inventaris()->update([
                    'jumlah' => $peminjaman->inventaris->jumlah + $peminjaman->jumlah,
                ]);

                // Kirim notifikasi (opsional, sesuaikan dengan method Anda)
                // $this->sendStatus($peminjaman, 'Terlambat otomatis');
            });

            $this->info("Peminjaman ID {$peminjaman->id_peminjaman} otomatis Terlambat.");
        }
    }
}
