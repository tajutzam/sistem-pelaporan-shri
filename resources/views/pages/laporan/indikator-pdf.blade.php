<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Indikator Pelayanan Rumah Sakit</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 0;
            color: #000;
            line-height: 1.4;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .hospital-name {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
            text-decoration: underline;
        }

        /* Meta Data Section */
        .meta-wrapper {
            width: 100%;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .meta-table {
            width: 100%;
            border: none;
        }

        .meta-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        /* Main Table */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
        }

        table.main-table th {
            background-color: #f2f2f2;
            font-size: 10px;
            font-weight: bold;
        }

        .text-left {
            text-align: left !important;
            padding-left: 8px !important;
        }

        /* Footer Section */
        .footer-section {
            margin-top: 40px;
            width: 100%;
        }

        .signature-space {
            height: 70px;
        }

        .underline {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="hospital-name">RUMAH SAKIT UMUM DAERAH REDA BOLO</div>
        <div class="report-title">LAPORAN INDIKATOR PELAYANAN RUMAH SAKIT</div>
    </div>

    <div class="meta-wrapper">
        <table class="meta-table">
            <tr>
                {{-- <td width="120">Ruangan</td>
                <td width="400">: {{ $nama_ruangan ?? '-' }}</td> --}}
                <td width="150">Tanggal Cetak</td>
                <td>: {{ date('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Periode Laporan</td>
                <td>: {{ $start_date->format('d/m/Y') }} - {{ $end_date->format('d/m/Y') }}</td>
                <td>User</td>
                <td>: {{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Halaman</td>
                <td>: .. dari .. </td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" width="12%">Ruangan</th>
                <th rowspan="2" width="8%">Jumlah Tempat Tidur</th>
                <th rowspan="2" width="8%">Jumlah Periode</th>
                <th rowspan="2" width="8%">Lama Dirawat</th>
                <th rowspan="2" width="8%">Hari Perawatan</th>
                <th colspan="3">Pasien Keluar</th>
                <th rowspan="2">BOR</th>
                <th rowspan="2">LOS</th>
                <th rowspan="2">BTO</th>
                <th rowspan="2">TOI</th>
                <th rowspan="2">GDR</th>
                <th rowspan="2">NDR</th>
            </tr>
            <tr>
                <th>Hidup</th>
                <th>Mati &gt; 48 Jam</th>
                <th>Mati &lt; 48 Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['data'] as $row)
                <tr>
                    <td class="text-left">{{ $row['nama_ruangan'] }}</td>
                    <td>{{ $row['jumlah_tempat_tidur'] }}</td>
                    <td>{{ $row['jumlah_periode'] ?? $start_date->diffInDays($end_date) + 1 }}</td>
                    <td>{{ $row['total_lama_dirawat'] }}</td>
                    <td>{{ $row['jumlah_hari_perawatan'] }}</td>
                    <td>{{ $row['pasien_keluar_hidup'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_48_plus'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_48_minus'] }}</td>
                    <td>{{ number_format($row['bor'], 2) }}</td>
                    <td>{{ number_format($row['avlos'], 2) }}</td>
                    <td>{{ number_format($row['bto'], 2) }}</td>
                    <td>{{ number_format($row['toi'], 2) }}</td>
                    <td>{{ number_format($row['gdr'], 2) }}</td>
                    <td>{{ number_format($row['ndr'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                // 1. Hitung Agregat Dasar
                $sumTT = collect($data['data'])->sum('jumlah_tempat_tidur');
                $sumLD = collect($data['data'])->sum('total_lama_dirawat');
                $sumHP = collect($data['data'])->sum('jumlah_hari_perawatan');
                $sumHidup = collect($data['data'])->sum('pasien_keluar_hidup');
                $sumMati48P = collect($data['data'])->sum('pasien_keluar_mati_48_plus');
                $sumMati48M = collect($data['data'])->sum('pasien_keluar_mati_48_minus');

                $t = $data['periode']['jumlah_hari'];
                $totalKeluar = $sumHidup + $sumMati48P + $sumMati48M;
                $totalMati = $sumMati48P + $sumMati48M;

                // 2. Hitung Indikator Agregat
                $totalBor = ($sumTT * $t) > 0 ? ($sumHP / ($sumTT * $t)) * 100 : 0;
                $totalAvlos = $totalKeluar > 0 ? $sumLD / $totalKeluar : 0;
                $totalBto = $sumTT > 0 ? $totalKeluar / $sumTT : 0;
                $totalToi = $totalKeluar > 0 ? (($sumTT * $t) - $sumHP) / $totalKeluar : 0;
                $totalGdr = $totalKeluar > 0 ? ($totalMati / $totalKeluar) * 1000 : 0;
                $totalNdr = $totalKeluar > 0 ? ($sumMati48P / $totalKeluar) * 1000 : 0;
            @endphp
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td class="text-left">TOTAL</td>
                <td>{{ $sumTT }}</td>
                <td>{{ $t }}</td>
                <td>{{ $sumLD }}</td>
                <td>{{ $sumHP }}</td>
                <td>{{ $sumHidup }}</td>
                <td>{{ $sumMati48P }}</td>
                <td>{{ $sumMati48M }}</td>
                {{-- Hasil Rumus Agregat --}}
                <td>{{ number_format($totalBor, 2) }}</td>
                <td>{{ number_format($totalAvlos, 2) }}</td>
                <td>{{ number_format($totalBto, 2) }}</td>
                <td>{{ number_format($totalToi, 2) }}</td>
                <td>{{ number_format($totalGdr, 2) }}</td>
                <td>{{ number_format($totalNdr, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-section">
        <div style="width: 100%;">
            <div style="float: left; width: 45%; text-align: center;">
                <br><br>
                <strong>Petugas Pelaporan</strong>
                <div class="signature-space"></div>
                <span class="underline">( {{ $pelaporan ?? 'Nama Petugas Pelaporan' }} )</span>
            </div>

            <div style="float: right; width: 45%; text-align: center;">
                <div style="margin-bottom: 5px; font-weight: bold;">
                    Wee Londa, {{ date('d F Y') }}
                </div>
                <strong>Kepala Ruangan Rekam Medis</strong>
                <div class="signature-space"></div>
                <span class="underline">( {{ $namaKepala ?? 'Nama Kepala Ruangan' }} )</span>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

</body>

</html>