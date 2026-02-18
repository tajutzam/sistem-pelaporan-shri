<!DOCTYPE html>
<html>
<head>
    <title>Laporan 20 Besar Penyakit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        /* Judul Utama */
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .sub-header-title {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        /* Informasi Metadata (Kanan Atas) & Filter (Kiri) */
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

        /* Tabel Utama */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th {
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }

        td {
            padding: 4px 8px;
            height: 20px; /* Menjaga tinggi baris agar mirip contoh */
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Area Tanda Tangan */
        .footer-sign {
            margin-top: 30px;
            width: 100%;
        }

        .sign-table {
            width: 100%;
            border: none !important;
        }

        .sign-table td {
            border: none !important;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-top: 20px;
        }

        .space-sign {
            height: 60px;
        }
    </style>
</head>

<body>
    <div class="header-title">RUMAH SAKIT UMUM DAERAH REDA BOLO</div>
    <div class="sub-header-title">LAPORAN 20 BESAR PENYAKIT RAWAT INAP</div>

    <div class="meta-container clearfix">
        <div class="meta-left">
            <table style="border: none">
                <tr style="border:none">
                    <td style="border:none; width:100px; padding:2px">Ruangan</td>
                    <td style="border:none; padding:2px">: {{ $filter['ruangan'] ?? '' }}</td>
                </tr>
                <tr style="border:none">
                    <td style="border:none; padding:2px">Periode Laporan</td>
                    <td style="border:none; padding:2px">: {{ $filter['periode'] ?? '' }}</td>
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
                <tr style="border:none">
                    <td style="border:none; padding:2px">Halaman</td>
                    <td style="border:none; padding:2px">: 1 dari 1</td>
                </tr>
            </table>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th>Diagnosa</th>
                <th width="25%">Kode ICD-10</th>
                <th width="20%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($diagnosas as $index => $d)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $d->diagnosa }}</td>
                    <td class="text-center">{{ $d->kode_icd }}</td>
                    <td class="text-center">{{ $d->jumlah }}</td>
                </tr>
            @endforeach
            
            @for ($i = count($diagnosas); $i < 20; $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="text-right" style="margin-top: 15px; margin-right: 50px;">
        Wee Londa, {{ date('d F Y') }}
    </div>

    <div class="footer-sign">
        <table class="sign-table">
            <tr>
                <td>
                    Petugas Pelaporan<br><br>
                    <div class="space-sign"></div>
                    {{ $pelaporan }}
                </td>
                <td>
                    Kepala Ruangan Rekam Medis<br><br>
                    <div class="space-sign"></div>
                    {{ $namaKepala }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>