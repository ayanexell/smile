<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use App\Models\User;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\HasFactory;
#[Fillable(['user_id', 'nama_barang', 'jumlah', 'kondisi', 'tipe', 'img_path', 'warna', 'dpt_dipinjam'])]
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
}
