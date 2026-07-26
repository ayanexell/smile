<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        // Ambil role default 'User' (pastikan sudah ada di tabel roles)
        $role = Roles::where('nama_role', 'User')->first();
        if(!$role){
            $role = Roles::factory()->user()->create(['nama_role' => 'User']);
        }

        return User::create([
            'role_id'       => $role->id_role,
            'nama_lengkap'  => $input['nama_lengkap'],
            'nik'           => $input['nik'],
            'tgl_lahir'     => $input['tgl_lahir'],
            'jenis_kelamin' => $input['jenis_kelamin'],
            'email'         => $input['email'],
            'no_wa'         => $input['no_wa'],
            'alamat'        => $input['alamat'],
            'pekerjaan'     => $input['pekerjaan'],
            'password'      => $input['password'],
            'departemen_id' => $input['departemen_id']??null,
            'profile_status' => false,
        ]);
    }
}
