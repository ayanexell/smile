<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
#[Fillable('nama_departemen', 'singkatan')]
class Departemens extends Model
{
    protected $primaryKey = 'id_departemen';
    use HasFactory;
    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id', 'id_departemen');
    }
}
