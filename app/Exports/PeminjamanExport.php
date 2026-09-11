<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PeminjamanExport implements FromCollection, WithHeadings
{
    public $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function headings(): array
    {
        return [
            'No',
            'Peminjam',
            'Inventaris',
            'Tgl Peminjaman',
            'Tgl Pengembalian',
            'Jumlah',
            'Status',
            'Hibah (Rp)',
            'Terlambat',
        ];
    }

    public function collection()
    {
        // Ambil data peminjaman yang inventarisnya dimiliki oleh user di departemen yang sama
        $peminjaman = Peminjaman::when($this->tahun, function ($q) {
            $q->whereYear('created_at', $this->tahun);
        })->get();
        // Jumlahkan seluruh nilai hibah
        $totalHibah = $peminjaman->sum('hibah');

        // Susun data per baris
        $data = $peminjaman->map(function ($item, $index) {
            return [
                $index + 1,
                $item->user->name_lengkap ?? 'Tanpa Nama',
                $item->inventaris->nama_barang ?? 'Tanpa Nama',
                $item->tgl_peminjaman,
                $item->tgl_pengembalian,
                $item->jumlah,
                $item->status,
                $item->hibahRupiah,
                $item->lambat ? 'Ya' : 'Tidak',
            ];
        });

        // Tambahkan baris total hibah di paling bawah
        $data->push([
            '',              // No
            '',              // Peminjam
            '',              // Inventaris
            '',              // Tgl Peminjaman
            '',              // Tgl Pengembalian
            '',              // Jumlah
            'Total Hibah',   // Status (sebagai label)
            $totalHibah,     // Hibah (total)
            '',              // Terlambat
        ]);

        return $data;
    }
}
