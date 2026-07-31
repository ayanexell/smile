<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class InventarisExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithEvents
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Mengambil data inventaris milik user beserta data departemen.
     */
    public function collection()
    {
        // Pastikan data inventaris sudah di-load. Jika diperlukan, bisa tambahkan eager load.
        return $this->user->inventaris;
    }

    /**
     * Format setiap baris data inventaris.
     */
    public function map($inventaris): array
    {
        return [
            $inventaris->nama_barang,
            $inventaris->jumlah,
            $inventaris->kondisi,
            $inventaris->tipe,
            $inventaris->warna,
            $inventaris->dapat_dipinjam ? 'Ya' : 'Tidak',
            Carbon::parse($inventaris->created_at)->translatedFormat('j F Y'), // contoh: "23 Juli 2026"
        ];
    }

    /**
     * Heading kolom tabel (akan ditempatkan di baris ke-4 setelah penyisipan).
     */
    public function headings(): array
    {
        return [
            'Nama Barang',
            'Jumlah',
            'Kondisi',
            'Tipe',
            'Warna',
            'Dapat Dipinjam',
            'Tanggal Dibuat',
        ];
    }

    /**
     * Daftarkan event untuk manipulasi sheet.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Sisipkan 3 baris di atas untuk judul dan info
                $sheet->insertNewRowBefore(1, 3);

                // 2. Tulis teks informasi
                $sheet->setCellValue('A1', 'DATA INVENTARIS');
                $sheet->setCellValue('A2', $this->user->departemen->nama_departemen ?? 'Departemen Tidak Diketahui');
                $sheet->setCellValue('A3', 'BULAN ' . Carbon::now()->translatedFormat('F Y')); // contoh: "Juli 2026"

                // 3. Merge cells untuk info header (A sampai G)
                $lastColumn = 'G'; // sesuai jumlah kolom
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->mergeCells("A3:{$lastColumn}3");

                // 4. Styling untuk info header
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'name' => 'Book Antiqua',
                        'size' => 11,
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // 5. Styling untuk heading tabel (baris ke-4)
                $headingRow = 4;
                $headingRange = "A{$headingRow}:{$lastColumn}{$headingRow}";
                $sheet->getStyle($headingRange)->applyFromArray([
                    'font' => [
                        'name'  => 'Book Antiqua',
                        'size'  => 11,
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'], // putih
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '538DD5'], // biru
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Opsional: atur tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(20);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(25);

                // Opsional: border untuk heading
                $sheet->getStyle($headingRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}
