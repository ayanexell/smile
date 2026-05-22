<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'nama_lengkap'  => $this->namaLengkapRules(),
            'nik'           => $this->nikRules($userId),
            'tgl_lahir'     => $this->tglLahirRules(),
            'jenis_kelamin' => $this->jenisKelaminRules(),
            'email'         => $this->emailRules($userId),
            'alamat'        => $this->alamatRules(),
            'pekerjaan'     => $this->pekerjaanRules(),
        ];
    }

    /**
     * Get the validation rules for nama lengkap.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function namaLengkapRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules for NIK.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nikRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'size:16',
            $userId === null
                ? Rule::unique('users', 'nik')
                : Rule::unique('users', 'nik')->ignore($userId, 'id_user'),
        ];
    }

    /**
     * Get the validation rules for tanggal lahir.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function tglLahirRules(): array
    {
        return [
            'required',
            'date',
            'before:today',
            'before_or_equal:' . now()->subYears(17)->format('Y-m-d'),
        ];
    }

    /**
     * Get the validation rules for jenis kelamin.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function jenisKelaminRules(): array
    {
        return ['required', 'string', Rule::in(['laki-laki', 'perempuan'])];
    }

    /**
     * Get the validation rules for email.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique('users', 'email')
                : Rule::unique('users', 'email')->ignore($userId, 'id_user'),
        ];
    }

    /**
     * Get the validation rules for alamat.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function alamatRules(): array
    {
        return ['required', 'string'];
    }

    /**
     * Get the validation rules for pekerjaan.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function pekerjaanRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules for departemen (optional).
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function departemenRules(): array
    {
        return ['nullable', 'exists:departemens,id_departemen'];
    }
}
