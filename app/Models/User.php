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
use Illuminate\Database\Eloquent\Attributes\Scope;

#[Fillable([
    'nama_lengkap',
    'avatar',
    'role_id',
    'departemen_id',
    'nik',
    'ktp_path',
    'tgl_lahir',
    'jenis_kelamin',
    'email',
    'no_wa',
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
        $words = explode(' ', $this->nama_lengkap);
        $wordCount = count($words);

        if ($wordCount === 1) {
            return Str::substr($words[0], 0, 2);
        }

        // 2 kata atau lebih: ambil huruf pertama dari dua kata pertama
        return Str::substr($words[0], 0, 1) . Str::substr($words[1], 0, 1);
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

    #[Scope]
    protected function onlyAdmins($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('nama_role', 'Admin');
        });
    }

    #[Scope]
    protected function onlyKoordinators($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('nama_role', 'Koordinator');
        });
    }

    #[Scope]
    protected function onlyUsers($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('nama_role', 'User');
        });
    }
}
