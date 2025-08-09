<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Indikator Pelayanan Rumah Sakit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #34495E;
        }

        .hospital-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #34495E;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .period-info {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .print-info {
            font-size: 9px;
            color: #888;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 9px;
        }

        td {
            font-size: 9px;
        }

        .text-left {
            text-align: left !important;
        }

        .no-data {
            color: #888;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }

        .signature-area {
            margin-top: 40px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            margin-left: 50px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin: 60px auto 10px auto;
        }

        /* Responsive adjustments for PDF */
        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        /* Style for numbers */
        .number {
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="hospital-name">{{ config('app.hospital_name', 'RUMAH SAKIT UMUM') }}</div>
        <div class="report-title">LAPORAN INDIKATOR PELAYANAN RUMAH SAKIT</div>
        <div class="period-info">
            Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
            ({{ \Carbon\Carbon::parse($start_date)->diffInDays(\Carbon\Carbon::parse($end_date)) + 1 }} hari)
        </div>
        <div class="print-info">
            Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Ruangan</th>
                    <th rowspan="2">Jumlah<br>Tempat Tidur</th>
                    <th rowspan="2">Jumlah<br>Periode</th>
                    <th rowspan="2">Lama<br>Dirawat</th>
                    <th rowspan="2">Hari<br>Perawatan</th>
                    <th colspan="3">Pasien Keluar</th>
                    <th rowspan="2">BOR<br>(%)</th>
                    <th rowspan="2">AVLos<br>(hari)</th>
                    <th rowspan="2">BTO<br>(kali)</th>
                    <th rowspan="2">TOI<br>(hari)</th>
                    <th rowspan="2">GDR<br>(‰)</th>
                    <th rowspan="2">NDR<br>(‰)</th>
                </tr>
                <tr>
                    <th>Hidup</th>
                    <th>Mati<br>&gt; 48 jam</th>
                    <th>Total<br>Mati</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['data'] as $row)
                    <tr>
                        <td class="text-left">{{ $row['nama_ruangan'] }}</td>
                        <td class="number">{{ $row['jumlah_tempat_tidur'] }}</td>
                        <td class="number">{{ number_format($row['jumlah_periode'], 2) }}</td>
                        <td class="number">{{ $row['total_lama_dirawat'] }}</td>
                        <td class="number">{{ $row['jumlah_hari_perawatan'] }}</td>
                        <td class="number">{{ $row['pasien_keluar_hidup'] }}</td>
                        <td class="number">{{ $row['pasien_keluar_mati'] > 0 ? $row['pasien_keluar_mati'] : '-' }}</td>
                        <td class="number">{{ $row['total_pasien_keluar'] }}</td>
                        <td class="number">{{ number_format($row['bor'], 2) }}</td>
                        <td class="number">{{ number_format($row['avlos'], 2) }}</td>
                        <td class="number">{{ number_format($row['bto'], 2) }}</td>
                        <td class="number">{{ number_format($row['toi'], 2) }}</td>
                        <td class="number">{{ number_format($row['gdr'], 2) }}</td>
                        <td class="number">{{ number_format($row['ndr'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="no-data">
                            Tidak ada data untuk ditampilkan pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div style="margin-bottom: 20px;">
            <strong>Keterangan:</strong><br>
            BOR = Bed Occupancy Rate | AVLos = Average Length of Stay<br>
            BTO = Bed Turn Over | TOI = Turn Over Interval<br>
            GDR = Gross Death Rate | NDR = Net Death Rate
        </div>

        <div class="signature-area">
            <div class="signature-box">
                {{ \Carbon\Carbon::now()->format('d M Y') }}<br>
                Kepala Bagian Rekam Medis
                <div class="signature-line"></div>
                <div>( _________________________ )</div>
            </div>
        </div>
    </div>
</body>

</html>
