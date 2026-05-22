<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
#[Fillable(['user_id', 'judul_laporan', 'month', 'file_path', 'status'])]
class Laporan extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_laporan';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
