<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Inventaris;
use App\Models\User;
class InventarisForm extends Form
{
    public ?Inventaris $inventaris = null;
    public $departemen_id;
    public $nama_barang;
    public $jumlah;
    public $kondisi;
    public $tipe;
    public $img_path;
    public $warna;
    public $dpt_dipinjam;

    /**
     * Aturan validasi data
     */
    public function rules(): array
    {
        return [
            'departemen_id' => 'required|exists:departemens,id_departemen',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'kondisi' => 'required|string|max:100',
            'tipe' => 'required|string|max:100',
            'img_path' => 'required|string|max:255',
            'warna' => 'required|string|max:100',
            'dpt_dipinjam' => 'required|boolean'
        ];
    }

    /**
     * Set data form saat proses Edit
     */
    public function setInventaris(Inventaris $inventaris): void
    {
        $this->inventaris = $inventaris;

        $this->departemen_id = $inventaris->departemen_id;
        $this->nama_barang = $inventaris->nama_barang;
        $this->jumlah = $inventaris->jumlah;
        $this->kondisi = $inventaris->kondisi;
        $this->tipe = $inventaris->tipe;
        $this->img_path = $inventaris->img_path;
        $this->warna = $inventaris->warna;
        $this->dpt_dipinjam = $inventaris->dpt_dipinjam;
    }

    /**
     * Menyimpan data baru (Create)
     */
    public function store(): void
    {
        $this->validate();

        Inventaris::create($this->all());

        $this->reset();
    }

    /**
     * Memperbarui data lama (Update)
     */
    public function update(): void
    {
        $this->validate();

        $this->inventaris->update($this->all());
    }
}
