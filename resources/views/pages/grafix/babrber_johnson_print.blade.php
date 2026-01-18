<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Grafik Barber Johnson</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        .hospital-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
            text-decoration: underline;
        }

        .meta-container {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .meta-container td {
            padding: 3px;
            vertical-align: top;
        }

        .chart-box {
            border: 2px solid #000;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .chart-img {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
        }

        .indicator-grid {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
        }

        .indicator-item {
            border: 1px solid #ccc;
            padding: 10px;
            width: 22%;
            text-align: center;
            border-radius: 5px;
        }

        .indicator-label {
            font-size: 10px;
            color: #666;
            font-weight: bold;
        }

        .indicator-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .footer {
            margin-top: 50px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="no-print"
        style="background: #fdf6e3; padding: 10px; margin-bottom: 20px; text-align: center; border: 1px solid #eee;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer;">Cetak
            Sekarang</button>
    </div>

    <div class="header">
        <div class="hospital-name">RUMAH SAKIT UMUM DAERAH REDA BOLO</div>
        <div class="report-title">GRAFIK BARBER JOHNSON</div>
    </div>

    <table class="meta-container">
        <tr>
            <td width="15%"><strong>Ruangan</strong></td>
            <td width="35%">: {{ $ruangan }}</td>
            <td width="20%"><strong>Tanggal Cetak</strong></td>
            <td width="30%">: {{ date('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Periode Laporan</strong></td>
            <td>: {{ $periode }}</td>
            <td><strong>User</strong></td>
            <td>: {{ auth()->user()->name ?? 'Administrator' }}</td>
        </tr>
    </table>

    <div class="chart-box">
        <div style="font-weight: bold; margin-bottom: 10px;">Preview berdasarkan data sistem</div>

        <div class="indicator-grid">
            <div class="indicator-item">
                <div class="indicator-label">BOR (%)</div>
                <div class="indicator-value">{{ $bor }}</div>
            </div>
            <div class="indicator-item">
                <div class="indicator-label">AvLOS (Hari)</div>
                <div class="indicator-value">{{ $avlos }}</div>
            </div>
            <div class="indicator-item">
                <div class="indicator-label">BTO (Kali)</div>
                <div class="indicator-value">{{ $bto }}</div>
            </div>
            <div class="indicator-item">
                <div class="indicator-label">TOI (Hari)</div>
                <div class="indicator-value">{{ $toi }}</div>
            </div>
        </div>

        <img src="{{ $chartImage }}" class="chart-img">
    </div>

    <div class="footer">
        <div style="text-align: right; margin-right: 50px;">
            Wee Londa, {{ date('d F Y') }}
        </div>
        <table class="signature-table">
            <tr>
                <td>
                    <strong>Petugas Pelaporan</strong>
                    <br><br><br><br><br>
                    ( {{ $pelaporan ?? '...........................' }} )
                </td>
                <td>
                    <strong>Kepala Ruangan Rekam Medis</strong>
                    <br><br><br><br><br>
                    ( {{ $namaKepala }} )
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Otomatis buka dialog print saat halaman dimuat
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>