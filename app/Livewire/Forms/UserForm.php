<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserForm extends Form
{
    public ?User $user = null;

    public $nama_lengkap = '';
    public $nik = '';
    public $tgl_lahir = '';
    public $jenis_kelamin = '';
    public $email = '';
    public $alamat = '';
    public $pekerjaan = '';
    public $id_role = '';
    public $departemen_id = '';

    /**
     * Aturan validasi data
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required','string','max:255'],
            'nik'          => ['required','string','size:16', Rule::unique('users', 'nik')->ignore($this->user->id_user, 'id_user')],
            'tgl_lahir'    => ['required','date'],
            'jenis_kelamin'=> ['required','in:laki-laki,perempuan'],
            'email'        => ['required','email', Rule::unique('users', 'email')->ignore($this->user->id_user, 'id_user')],
            'alamat'       => ['nullable','string','max:500'],
            'pekerjaan'    => ['nullable','string','max:100'],
            'departemen_id' => ['nullable','exists:departemens,id_departemen'],
        ];
    }

    /**
     * Set data form saat proses Edit
     */
    public function setUser(User $user): void
    {
        $this->user = $user;

        $this->nama_lengkap = $user->nama_lengkap;
        $this->nik = $user->nik;
        $this->tgl_lahir = $user->tgl_lahir;
        $this->jenis_kelamin = $user->jenis_kelamin;
        $this->email = $user->email;
        $this->alamat = $user->alamat;
        $this->pekerjaan = $user->pekerjaan;
        $this->departemen_id = $user->departemen_id;
    }

    /**
     * Menyimpan data baru (Create)
     */
    public function store(): void
    {
        $this->validate();

        User::create($this->all());

        $this->reset();
    }

    /**
     * Memperbarui data lama (Update)
     */
    public function update(): void
    {
        $this->validate();

        $this->user->update($this->all());
    }
}
