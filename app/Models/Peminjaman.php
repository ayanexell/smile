<?php

namespace App\Models;

use App\Actions\WhatsappAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Exception;

use Illuminate\Support\Facades\Log;
#[Fillable(['user_id', 'inventaris_id', 'tgl_peminjaman', 'tgl_pengembalian', 'jumlah', 'status', 'hibah', 'lambat'])]
class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $cast = [
        'tgl_peminjaman' => 'datetime',
        'tgl_pengembalian' => 'datetime',
        'lambat' => 'boolean',
        'hibah' => 'integer',
    ];
    use HasFactory;
    protected $primaryKey = 'id_peminjaman';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id', 'id_inventaris');
    }

    protected function hibahRupiah(): Attribute
    {
        return Attribute::make(
            get: fn() => Number::currency($this->hibah ?? 0, in: 'IDR', locale: 'id', precision: 0),
        );
    }
    public function sendStatusNotification(string $status)
    {
        $user = $this->user;
        if (!$user || !$user->no_wa) {
            Log::warning("Gagal kirim WA: Nomor tidak ditemukan untuk peminjaman ID {$this->id_peminjaman}");
            return;
        }

        $namaBarang = $this->inventaris->nama_barang ?? 'tidak diketahui';
        $appUrl = env('APP_URL');

        // Pilih template sesuai status
        $message = match ($status) {
            'Diterima' =>
            "Halo, {$user->nama_lengkap}.\n\n"
            . "Pengajuan peminjaman Anda untuk *{$namaBarang}* telah *DISETUJUI* oleh admin.\n"
            . "Silakan login ke aplikasi untuk melihat detail peminjaman.\n\n"
            . "📌 Hal yang perlu diperhatikan:\n"
            . "- Mohon ingat tanggal pengembalian yang telah ditentukan.\n"
            . "- Anda bertanggung jawab penuh atas barang yang dipinjam.\n\n"
            . "Akses aplikasi: {$appUrl}",

            'Ditolak' =>
            "Halo, {$user->nama_lengkap}.\n\n"
            . "Mohon maaf, pengajuan peminjaman Anda untuk *{$namaBarang}* *DITOLAK* oleh admin.\n"
            . "Silakan login ke aplikasi untuk informasi lebih lanjut atau ajukan peminjaman lainnya.\n\n"
            . "Akses aplikasi: {$appUrl}",

            'Pending' =>
            "Halo, {$user->nama_lengkap}.\n\n"
            . "Status pengajuan peminjaman Anda untuk *{$namaBarang}* dikembalikan menjadi *MENUNGGU*.\n"
            . "Admin akan meninjau kembali permohonan Anda. Silakan pantau melalui aplikasi.\n\n"
            . "Akses aplikasi: {$appUrl}",

            'Dikembalikan', 'Dikembalikan otomatis (melewati batas waktu)' =>
            "Halo, {$user->nama_lengkap}.\n\n"
            . "Peminjaman Anda untuk *{$namaBarang}* telah *SELESAI* dan tercatat sebagai *DIKEMBALIKAN*.\n"
            . "Terima kasih telah menyelesaikan peminjaman dengan baik.\n\n"
            . "Akses aplikasi: {$appUrl}",

            'Terlambat', 'terlambat' =>
            "Halo, {$user->nama_lengkap}.\n\n"
            . "Peminjaman Anda untuk *{$namaBarang}* telah melewati batas waktu pengembalian.\n"
            . "Segera kembalikan barang tersebut ke admin untuk menghindari sanksi lebih lanjut.\n\n"
            . "Akses aplikasi: {$appUrl}",

            default =>
            "Halo, {$user->nama_lengkap}.\n\n"
            . "Status peminjaman Anda untuk *{$namaBarang}* diperbarui menjadi *{$status}*.\n"
            . "Silakan cek aplikasi untuk detail lebih lanjut.\n\n"
            . "Akses aplikasi: {$appUrl}",
        };

        try {
            $wa = new WhatsappAction(
                target: $user->no_wa,
                message: $message
            );
            $wa->send();
            Log::info('Whatspp');
        } catch (Exception $e) {
            Log::error("WhatsApp gagal dikirim: " . $e->getMessage());
        }
    }
}
