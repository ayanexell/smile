<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id_departemen
 * @property string $nama_departemen
 * @property string|null $singkatan
 * @property string|null $deskripsi
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inventaris> $inventaris
 * @property-read int|null $inventaris_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\DepartemensFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens whereIdDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens whereNamaDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens whereSingkatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemens whereUpdatedAt($value)
 */
	class Departemens extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_inventaris
 * @property int $departemen_id
 * @property string $nama_barang
 * @property int $jumlah
 * @property string $kondisi
 * @property string $tipe
 * @property string $img_path
 * @property string $warna
 * @property int $dpt_dipinjam
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Departemens $departemen
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Peminjaman> $peminjamans
 * @property-read int|null $peminjamans_count
 * @method static \Database\Factories\InventarisFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris onlyDipinjamkan()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereDepartemenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereDptDipinjam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereIdInventaris($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereImgPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereKondisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereNamaBarang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventaris whereWarna($value)
 */
	class Inventaris extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_laporan
 * @property int $user_id
 * @property string $judul_laporan
 * @property string $month
 * @property string $file_path
 * @property string $status
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\LaporanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereIdLaporan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereJudulLaporan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereUserId($value)
 */
	class Laporan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_peminjaman
 * @property int $user_id
 * @property int $inventaris_id
 * @property string $tgl_peminjaman
 * @property string $tgl_pengembalian
 * @property int $jumlah
 * @property string $status
 * @property int $hibah
 * @property int $lambat
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Inventaris $inventaris
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PeminjamanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereHibah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereIdPeminjaman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereInventarisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereLambat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereTglPeminjaman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereTglPengembalian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Peminjaman whereUserId($value)
 */
	class Peminjaman extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_role
 * @property string $nama_role
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\RolesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereIdRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereNamaRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereUpdatedAt($value)
 */
	class Roles extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_user
 * @property int $role_id
 * @property int|null $departemen_id
 * @property string $nama_lengkap
 * @property string|null $avatar
 * @property string $nik
 * @property string|null $ktp_path
 * @property string $tgl_lahir
 * @property string $jenis_kelamin
 * @property string $email
 * @property string $no_wa
 * @property string $alamat
 * @property string $pekerjaan
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Departemens|null $departemen
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inventaris> $inventaris
 * @property-read int|null $inventaris_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Laporan> $laporans
 * @property-read int|null $laporans_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Peminjaman> $peminjamans
 * @property-read int|null $peminjamans_count
 * @property-read \App\Models\Roles $role
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyAdmins()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyKoordinators()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyUsers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartemenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereKtpPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoWa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePekerjaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTglLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

