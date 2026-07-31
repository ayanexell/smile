<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
#[Fillable('user_id', 'laporan_path', 'bulan', 'status')]
class LaporanInventaris extends Model
{
    protected $primaryKey = 'id_laporan_inventaris';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
