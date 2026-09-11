<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Inventaris;

#[Fillable('nama_departemen', 'singkatan', 'deskripsi')]
class Departemens extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_departemen';

    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id', 'id_departemen');
    }

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'departemen_id', 'id_departemen');
    }
}
