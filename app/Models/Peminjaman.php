<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Database\Eloquent\Factories\HasFactory;
#[Fillable(['user_id', 'inventaris_id', 'tgl_peminjaman', 'tgl_pengembalian', 'status', 'hibah', 'lambat'])]
class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $cast = [
        'tgl_peminjaman' => 'date',
        'tgl_pengembalian' => 'date',
        'lambat' => 'boolean',
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
}
