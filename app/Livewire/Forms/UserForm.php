<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserForm extends Form
{
    public ?User $user = null;

    public $nama_lengkap = '';
    public $nik = '';
    public $tgl_lahir = '';
    public $jenis_kelamin = '';
    public $email = '';
    public $no_wa = '';
    public $alamat = '';
    public $pekerjaan = '';
    public $id_role = '';
    public $password = '';
    public $departemen_id = '';

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'size:16', Rule::unique('users', 'nik')->ignore($this->user?->id_user, 'id_user')],
            'tgl_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user?->id_user, 'id_user')],
            'no_wa' => ['nullable', 'string', 'max:15', Rule::unique('users', 'no_wa')->ignore($this->user?->id_user, 'id_user')],
            'alamat' => ['nullable', 'string', 'max:500'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'departemen_id' => ['nullable', 'exists:departemens,id_departemen'],
            // Wajib diisi saat create (user belum ada), opsional saat edit
            'password' => [$this->user === null ? 'required' : 'nullable', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            // Nama Lengkap
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter.',

            // NIK
            'nik.required' => 'NIK wajib diisi.',
            'nik.string' => 'NIK harus berupa teks.',
            'nik.size' => 'NIK harus tepat 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar, silakan gunakan NIK lain.',

            // Tanggal Lahir
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date' => 'Format tanggal lahir tidak valid.',

            // Jenis Kelamin
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin yang dipilih tidak valid (hanya laki-laki atau perempuan).',

            // Email
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar, silakan gunakan email lain.',

            // No WhatsApp (opsional)
            'no_wa.string' => 'Nomor WhatsApp harus berupa teks.',
            'no_wa.max' => 'Nomor WhatsApp maksimal 15 karakter.',

            // Alamat (opsional)
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',

            // Pekerjaan (opsional)
            'pekerjaan.string' => 'Pekerjaan harus berupa teks.',
            'pekerjaan.max' => 'Pekerjaan maksimal 100 karakter.',

            // Departemen (opsional)
            'departemen_id.exists' => 'Departemen yang dipilih tidak valid atau tidak tersedia.',

            // Password (wajib saat create, opsional saat edit)
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }

    public function setUser(User $user): void
    {
        $this->user = $user;

        $this->nama_lengkap = $user->nama_lengkap;
        $this->nik = $user->nik;
        $this->tgl_lahir = $user->tgl_lahir;
        $this->jenis_kelamin = $user->jenis_kelamin;
        $this->email = $user->email;
        $this->no_wa = $user->no_wa;
        $this->alamat = $user->alamat;
        $this->pekerjaan = $user->pekerjaan;
        $this->departemen_id = $user->departemen_id;
    }

    /**
     * Menyimpan data baru (Create)
     * @param int $roleId ID role yang dipilih (Admin/Koordinator/User)
     * @param int|null $departemenId Hanya diisi untuk role Koordinator
     */
    public function store($roleId = null, $departemenId = null): void
    {
        $this->validate();

        User::create([
            'role_id' => $roleId,
            'departemen_id' => $departemenId,
            'nama_lengkap' => $this->nama_lengkap,
            'nik' => $this->nik,
            'tgl_lahir' => $this->tgl_lahir,
            'alamat' => $this->alamat,
            'jenis_kelamin' => $this->jenis_kelamin,
            'pekerjaan' => $this->pekerjaan,
            'email' => $this->email,
            'no_wa' => $this->no_wa,
            'password' => Hash::make($this->password),
            'profile_status' => false,
        ]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        // $data = collect($this->all())->except(['password', 'user'])->toArray();

        // if (!empty($this->password)) {
        //     $data['password'] = Hash::make($this->password);
        // }

        $this->user->update($this->all());
    }
}
