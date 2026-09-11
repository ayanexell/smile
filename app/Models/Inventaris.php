<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Models\User;
use App\Models\Peminjaman;

#[Fillable(['user_id', 'nama_barang', 'jumlah', 'kondisi', 'tipe', 'img_path', 'warna', 'dpt_dipinjam', 'frequensi_peminjaman'])]
class Inventaris extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_inventaris';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'inventaris_id', 'id_inventaris');
    }

    #[Scope()]
    protected function onlyDipinjamkan()
    {
        return $this->where('dpt_dipinjam', true)->where('kondisi', 'baik')->where('jumlah', '>=', 1);
    }
}
