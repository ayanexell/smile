<?php

namespace App\Models;

use App\Models\Departemens;
use App\Models\Inventaris;
use App\Models\Laporan;
use App\Models\Peminjaman;
use App\Models\Roles;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'nama_lengkap',
    'role_id',
    'departemen_id',
    'nik',
    'tgl_lahir',
    'jenis_kelamin',
    'email',
    'alamat',
    'pekerjaan',
    'password'
])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{

    protected $primaryKey = 'id_user';
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id', 'id_role');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemens::class, 'departemen_id', 'id_departemen');
    }

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'user_id', 'id_user');
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'user_id', 'id_user');
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'user_id', 'id_user');
    }
}
