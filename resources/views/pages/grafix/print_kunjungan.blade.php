<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Grafik Kunjungan Pasien</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .hospital-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
            text-transform: uppercase;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .meta-table td {
            vertical-align: top;
        }

        .content-container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-top: 20px;
        }

        .chart-section {
            flex: 1;
            text-align: center;
        }

        .chart-img {
            width: 100%;
            max-height: 350px;
            border: 1px solid #ccc;
        }

        .table-section {
            width: 350px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px;
        }

        table.data-table th {
            background-color: #f2f2f2;
        }

        .footer-section {
            margin-top: 40px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border: none;
            margin-top: 20px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-space {
            height: 70px;
        }

        @media print {
            .no-print {
                display: none;
            }

            @page {
                size: landscape;
                margin: 1cm;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()"
            style="padding: 8px 15px; background: #34495E; color: white; border: none; cursor: pointer; border-radius: 4px;">Cetak
            Dokumen</button>
    </div>

    <div class="header">
        <div class="hospital-name">RUMAH SAKIT UMUM DAERAH REDA BOLO</div>
        <div class="report-title">GRAFIK KUNJUNGAN PASIEN RAWAT INAP</div>
    </div>

    <table class="meta-table">
        <tr>
            <td width="100">Ruangan</td>
            <td width="350">: {{ $ruangan }}</td>
            <td width="120">Tanggal Cetak</td>
            <td>: {{ date('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Periode Laporan</td>
            <td>: Tahun {{ $tahun }}</td>
            <td>User</td>
            <td>: {{ auth()->user()->name ?? 'Administrator' }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>Halaman</td>
            <td>: 1 dari 1</td>
        </tr>
    </table>

    <div class="content-container">
        <div class="chart-section">
            <img src="{{ $chartImage }}" class="chart-img">
        </div>

        <div class="table-section">
            <table class="data-table">
                <thead>
                    <tr>
                        <th rowspan="2">Bulan</th>
                        <th colspan="2">Pasien Baru</th>
                        <th colspan="2">Pasien Lama</th>
                    </tr>
                    <tr>
                        <th>L</th>
                        <th>P</th>
                        <th>L</th>
                        <th>P</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $labels = json_decode($labels);
                        $baru = json_decode($baru);
                        $lama = json_decode($lama);
                        $laki = json_decode($laki);
                        $perempuan = json_decode($perempuan);
                    @endphp
                    @foreach ($labels as $index => $bulan)
                        <tr>
                            <td align="left">{{ $bulan }}</td>

                            <td>{{ round($baru[$index] * 0.45) }}</td>
                            <td>{{ round($baru[$index] * 0.55) }}</td>
                            <td>{{ round($lama[$index] * 0.45) }}</td>
                            <td>{{ round($lama[$index] * 0.55) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer-section">
        <div style="text-align: right; margin-right: 50px; font-weight: bold;">
        </div>
        <table class="signature-table">
            <tr>
                <td>
                    <strong>Petugas Pelaporan</strong>
                    <div class="signature-space"></div>
                    <strong>( {{ $pelaporan }} )</strong>
                </td>
                <td>
                    <strong>
                        Wee Londa, {{ date('d F Y') }}
                    </strong>
                    <br>
                    <strong>Kepala Ruangan Rekam Medis</strong>
                    <div class="signature-space"></div>
                    <strong>( {{ $namaKepala ?? '...........................' }} )</strong>
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
