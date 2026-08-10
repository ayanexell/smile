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
use Carbon\Carbon;

class InventarisExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithEvents
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    protected $user;
    protected $startDate;
    protected $endDate;

    /**
     * @param User $user
     * @param string|null $startDate  Format: Y-m-d (mis: "2026-01-01")
     * @param string|null $endDate    Format: Y-m-d (mis: "2026-03-31")
     */
    public function __construct(User $user, $startDate = null, $endDate = null)
    {
        $this->user = $user;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Ambil data inventaris dengan filter tanggal (jika ada).
     */
    public function collection()
    {
        // Mulai dari relasi sebagai query builder
        $query = $this->user->inventaris();

        if ($this->startDate && $this->endDate) {
            // Filter berdasarkan rentang tanggal created_at
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        } else {
            // Default: hanya data bulan berjalan
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        }

        return $query->get();
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
            Carbon::parse($inventaris->created_at)->translatedFormat('j F Y'),
        ];
    }

    /**
     * Heading kolom (baris ke-4 setelah penyisipan).
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
     * Event styling sheet dan penempatan teks judul/periode.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Sisipkan 3 baris di atas
                $sheet->insertNewRowBefore(1, 3);

                // Judul utama
                $sheet->setCellValue('A1', 'DATA INVENTARIS');
                $sheet->setCellValue('A2', $this->user->departemen->nama_departemen ?? 'Departemen Tidak Diketahui');

                // Label periode
                if ($this->startDate && $this->endDate) {
                    $label = 'PERIODE: '
                        . Carbon::parse($this->startDate)->translatedFormat('j F Y')
                        . ' - '
                        . Carbon::parse($this->endDate)->translatedFormat('j F Y');
                } else {
                    $label = 'BULAN ' . now()->translatedFormat('F Y');
                }
                $sheet->setCellValue('A3', $label);

                $lastColumn = 'G'; // Sesuai jumlah kolom
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->mergeCells("A3:{$lastColumn}3");

                // Styling info header
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'name' => 'Book Antiqua',
                        'size' => 11,
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Heading tabel (baris 4)
                $headingRow = 4;
                $headingRange = "A{$headingRow}:{$lastColumn}{$headingRow}";
                $sheet->getStyle($headingRange)->applyFromArray([
                    'font' => [
                        'name' => 'Book Antiqua',
                        'size' => 11,
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '538DD5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(20);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(25);

                // Border heading
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
