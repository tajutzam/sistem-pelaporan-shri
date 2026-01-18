<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Kunjungan Rawat Inap</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }

    body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            color: #333;
        }

        /* Header Rumah Sakit */
        .hospital-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .hospital-header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hospital-header h3 {
            margin: 10px 0 0 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Meta Data (Ruangan, Tanggal Cetak, dll) */
        .meta-container {
            width: 100%;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .meta-left {
            float: left;
            width: 50%;
        }

        .meta-right {
            float: right;
            width: 30%;
        }

        .meta-table {
            width: 100%;
            border: none;
        }

        .meta-table td {
            border: none;
            padding: 2px 0;
            font-weight: bold;
            font-size: 11px;
        }

        /* Tabel Utama */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #a0a0a0;
            padding: 6px 4px;
            vertical-align: middle;
        }

        table.main-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        /* Pewarnaan Status Sesuai Foto */
        .status-dirawat {
            color: #2e59d9;
            font-weight: bold;
        }

        /* Biru */
        .status-pindah {
            color: #f6c23e;
            font-weight: bold;
        }

        /* Oranye */
        .status-keluar {
            color: #e74a3b;
            font-weight: bold;
        }

        /* Merah */

        /* Footer Tanda Tangan */
        .footer-container {
            margin-top: 30px;
            width: 100%;
        }

        .signature-row {
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }

        .date-location {
            text-align: right;
            margin-bottom: 40px;
            margin-right: 50px;
            font-weight: bold;
        }


        .meta-container {
            width: 100%;
            margin-bottom: 10px;
        }

        .meta-left {
            float: left;
            width: 50%;
        }

        .meta-right {
            float: right;
            width: 30%;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <div class="hospital-header">
        <h2>RUMAH SAKIT UMUM DAERAH REDA BOLO</h2>
        <h3>LAPORAN REKAPITULASI KUNJUNGAN RAWAT INAP</h3>
    </div>

    <div class="meta-container clearfix">
        <div class="meta-left">
            <table style="border: none">
                <tr style="border:none">
                    <td style="border:none; width:100px; padding:2px">Ruangan</td>
                    <td style="border:none; padding:2px">: {{ $filter['ruangan'] ?? 'Semua Ruangan' }}</td>
                </tr>
                <tr style="border:none">
                    <td style="border:none; padding:2px">Periode Laporan</td>
                    <td style="border:none; padding:2px">: {{ $tanggal_mulai ?? '-'  }} s/d {{ $tanggal_akhir ?? '-' }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="meta-right">
            <table style="border:none">
                <tr style="border:none">
                    <td style="border:none; padding:2px">Tanggal Cetak</td>
                    <td style="border:none; padding:2px">: {{ date('d/m/Y H:i') }}</td>
                </tr>
                <tr style="border:none">
                    <td style="border:none; padding:2px">User</td>
                    <td style="border:none; padding:2px">: {{ Auth()->user()->name }}</td>
                </tr>
            </table>
        </div>
    </div>


    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="8%">No Rekam Medis</th>
                <th width="12%">Nama Pasien</th>
                <th width="8%">Jenis Kelamin</th>
                <th width="10%">Ruangan</th>
                <th width="5%">Kelas</th>
                <th width="8%">Penjaminan</th>
                <th width="12%">DPJP</th>
                <th width="8%">Tanggal Masuk</th>
                <th width="8%">Tanggal Keluar</th>
                <th width="7%">Status</th>
                <th>Diagnosa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanKunjungans as $index => $item)
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}</td>
                    <td style="text-align:center;">{{ $item['no_rm'] }}</td>
                    <td>{{ $item['nama_pasien'] }}</td>
                    <td>{{ $item['jenis_kelamin'] }}</td>
                    <td>{{ $item['ruangan'] }}</td>
                    <td style="text-align:center;">{{ $item['kelas'] }}</td>
                    <td>{{ $item['penjaminan'] }}</td>
                    <td>{{ $item['dpjp'] }}</td>
                    <td style="text-align:center;">{{ $item['tanggal_masuk'] }}</td>
                    <td style="text-align:center;">{{ $item['tanggal_keluar'] ?: '-' }}</td>
                    <td style="text-align:center;">
                        @php
                            $statusClass = 'status-' . strtolower($item['status']);
                        @endphp
                        <span class="{{ $statusClass }}">{{ ucfirst($item['status']) }}</span>
                    </td>
                    <td>{{ $item['diagnosa'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align:center;">Tidak ada data yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer-container">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none; text-align: center; vertical-align: bottom;">
                    <p><strong>Petugas Pelaporan</strong></p>
                    <br><br><br><br><br>
                    <p><strong>( {{ $pelaporan }} )</strong></p>
                </td>

                <td style="width: 50%; border: none; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 20px;">
                        <strong>Wee Londa, {{ date('d F Y') }}</strong>
                    </div>
                    <p><strong>Kepala Ruangan Rekam Medis</strong></p>
                    <br><br><br><br><br>
                    <p><strong>( {{ $namaKepala ?? 'Nama Kepala Ruangan Rekam Medis' }} )</strong></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>