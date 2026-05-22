<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
#[Fillable(['nama_role'])]
class Roles extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_role';

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id_role');
    }
}
