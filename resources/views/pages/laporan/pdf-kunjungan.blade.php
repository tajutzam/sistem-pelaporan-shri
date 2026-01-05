<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
            text-decoration: underline;
        }

        .header p {
            margin: 5px 0;
        }

        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .filter-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }

        .filter-info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .footer p {
            margin: 5px 0;
        }

        .page-break {
            page-break-after: always;
        }

        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>

                <th style="width: 10%;">No RM</th>
                <th style="width: 12%;">Nama Pasien</th>
                <th style="width: 8%;">JK</th>
                <th style="width: 10%;">Ruangan</th>
                <th style="width: 8%;">Kelas</th>
                <th style="width: 8%;">Penjaminan</th>
                <th style="width: 10%;">DPJP</th>
                <th style="width: 8%;">Tgl Masuk</th>
                <th style="width: 8%;">Tgl Keluar</th>
                <th style="width: 6%;">Status</th>
                <th style="width: 6%;">Status Pasien</th>
                <th style="width: 6%;">Asal Pasien</th>
                <th style="width: 8%;">Diagnosa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanKunjungans as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['no_rm'] }}</td>
                    <td>{{ $item['nama_pasien'] }}</td>
                    <td class="text-center">{{ $item['jenis_kelamin'] }}</td>
                    <td>{{ $item['ruangan'] }}</td>
                    <td class="text-center">{{ $item['kelas'] }}</td>
                    <td>{{ $item['penjaminan'] }}</td>
                    <td>{{ $item['dpjp'] }}</td>
                    <td class="text-center">
                        {{ $item['tanggal_masuk'] ? date('d/m/Y', timestamp: strtotime($item['tanggal_masuk'])) : '-' }}
                    </td>
                    <td class="text-center">
                        {{ $item['tanggal_keluar'] ? date('d/m/Y', strtotime($item['tanggal_keluar'])) : '-' }}
                    </td>
                    <td class="text-center">{{ ucfirst($item['status']) }}</td>
                    <td class="text-center">
                        {{ $item['status_pasien'] }}
                    </td>
                    <td class="text-center">
                        {{ $item['asal_pasien'] }}
                    </td>
                    <td>{{ $item['diagnosa'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if (count($laporanKunjungans) > 0)
    @endif

    <div class="footer">
        <p>{{ date('d F Y') }}</p>
        <p>Petugas Pelaporan</p>
        <br><br><br>
        <p>(_________________________)</p>
        <p>NIP: </p>
    </div>
</body>

</html>
