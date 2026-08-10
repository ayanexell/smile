<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable('laporan_path', 'tahun', 'total_hibah')]
class LaporanPeminjaman extends Model
{
    protected $primaryKey = 'id_laporan_peminjaman';
    protected $casts = [
        'total_hibah' => 'integer',
    ];

    protected function hibahRupiah(): Attribute
    {
        return Attribute::make(
            get: fn() => Number::currency($this->total_hibah ?? 0, in: 'IDR', locale: 'id', precision: 0),
        );
    }
}
