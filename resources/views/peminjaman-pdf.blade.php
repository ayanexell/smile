<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Peminjaman Inventaris</title>
    <style>
        body {
            font-family: 'Book Antiqua ', serif;
            background: #ffffff;
            margin: 0;
            padding: 10px;
            color: #1f2937;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td {
            vertical-align: top;
        }

        .header-table td {
            padding-bottom: 15px;
            border-bottom: 2px solid #1f2937;
        }

        .logo-cell {
            width: 80px;
            padding-right: 15px;
            align-content: center;
        }

        .logo-cell img {
            width: 70px;
            height: 70px;
        }

        .institute-name {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .institute-address {
            font-size: 12px;
            color: #4b5563;
        }

        .title {
            text-align: center;
            margin: 25px 0 15px;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            color: #4b5563;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .data-table {
            margin-bottom: 20px;
        }

        .data-table td {
            padding: 3px 10px 3px 0;
            font-size: 12px;
            color: #374151;
        }

        .data-table .label {
            font-weight: bold;
            width: 130px;
            white-space: nowrap;
        }

        .data-table .colon {
            width: 10px;
        }

        .barang-table {
            margin-bottom: 20px;
            font-size: 11px;
        }

        .barang-table th {
            background-color: #f9fafb;
            text-align: left;
            font-weight: bold;
            padding: 8px 5px;
            border-bottom: 2px solid #9ca3af;
        }

        .barang-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-dipinjam {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-dikembalikan {
            background: #d1fae5;
            color: #065f46;
        }

        .ketentuan {
            border-top: 2px solid #d1d5db;
            padding-top: 15px;
            margin-bottom: 30px;
        }

        .ketentuan ul {
            list-style-type: disc;
            padding-left: 20px;
            font-size: 12px;
            color: #374151;
        }

        .signature-table {
            margin-top: 40px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 12px;
        }

        .signature-line {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Kop Surat -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('assets/logo.webp') }}" alt="Logo">
                </td>
                <td>
                    <div class="institute-name">Pondok Pesantren Annuqayah Latee</div>
                    <div class="institute-address">Guluk-Guluk, Sumenep, Jawa Timur</div>
                    <div class="institute-address">Periode {{ date('Y') }} - {{ date('Y') + 1 }}</div>
                </td>
            </tr>
        </table>

        <!-- Judul -->
        <div class="title">Bukti Peminjaman Inventaris</div>
        <div class="subtitle">Nomor: #{{ $peminjaman->id_peminjaman }} | Tanggal:
            {{ \Carbon\Carbon::parse($peminjaman->tgl_peminjaman)->translatedFormat('d F Y') }}</div>

        <!-- Data Peminjam -->
        <div class="section-title">Data Peminjam</div>
        <table class="data-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->user->nama_lengkap }}</td>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->user->nik }}</td>
                <td class="label">Email</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->user->email }}</td>
            </tr>
            <tr>
                <td class="label">No. WhatsApp</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->user->no_wa }}</td>
                <td class="label">Alamat</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->user->alamat }}</td>
            </tr>
            <tr>
                <td class="label">Pekerjaan</td>
                <td class="colon">:</td>
                <td>{{ $peminjaman->user->pekerjaan }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <!-- Barang Dipinjam -->
        <div class="section-title">Barang yang Dipinjam</div>
        <table class="barang-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Departemen</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $peminjaman->inventaris->nama_barang }}</td>
                    <td>{{ $peminjaman->inventaris->user->departemen->nama_departemen }}</td>
                    <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_peminjaman)->translatedFormat('d F Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_pengembalian)->translatedFormat('d F Y') }}</td>
                    <td>{{ $peminjaman->jumlah }}</td>
                    <td>
                        @if ($peminjaman->status == 'dipinjam')
                            <span class="badge badge-dipinjam">Dipinjam</span>
                        @else
                            <span class="badge badge-dikembalikan">Dikembalikan</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Ketentuan -->
        <div class="ketentuan">
            <div class="section-title">Ketentuan Peminjaman</div>
            <ul>
                <li>Barang wajib dikembalikan sesuai tanggal yang telah ditentukan.</li>
                <li>Peminjam bertanggung jawab penuh atas kerusakan atau kehilangan barang.</li>
                <li>Keterlambatan pengembalian akan dikenakan sanksi sesuai kebijakan pondok.</li>
            </ul>
        </div>

        <!-- Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td>
                    <div>Petugas,</div>
                    <div class="signature-line">................................</div>
                </td>
                <td>
                    <div>Peminjam,</div>
                    <div class="signature-line">{{ $peminjaman->user->nama_lengkap }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
