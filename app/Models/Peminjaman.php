<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable(['user_id', 'inventaris_id', 'tgl_peminjaman', 'tgl_pengembalian', 'jumlah', 'status', 'hibah', 'lambat'])]
class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $cast = [
        'tgl_peminjaman' => 'datetime',
        'tgl_pengembalian' => 'datetime',
        'lambat' => 'boolean',
        'hibah' => 'integer',
    ];
    use HasFactory;
    protected $primaryKey = 'id_peminjaman';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id', 'id_inventaris');
    }

    protected function hibahRupiah(): Attribute
    {
        return Attribute::make(
            get: fn() => Number::currency($this->hibah ?? 0, in: 'IDR', locale: 'id', precision: 0),
        );
    }
}
