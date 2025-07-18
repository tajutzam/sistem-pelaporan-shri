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

    <!-- Filter Information -->
    {{-- @if($filters['ruangan'] || $filters['tanggal_awal'] || $filters['tanggal_akhir'] || $filters['search'])
    <div class="filter-info">
        <h3>Filter yang Diterapkan:</h3>
        @if($filters['ruangan'])
        <p><strong>Ruangan:</strong> {{ $filters['ruangan'] }}</p>
        @endif
        @if($filters['tanggal_awal'])
        <p><strong>Tanggal Awal:</strong> {{ date('d/m/Y', strtotime($filters['tanggal_awal'])) }}</p>
        @endif
        @if($filters['tanggal_akhir'])
        <p><strong>Tanggal Akhir:</strong> {{ date('d/m/Y', strtotime($filters['tanggal_akhir'])) }}</p>
        @endif
        @if($filters['search'])
        <p><strong>Pencarian:</strong> {{ $filters['search'] }}</p>
        @endif
        <p><strong>Total Data:</strong> {{ count($laporanKunjungans) }} pasien</p>
    </div>
    @endif --}}

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
                <th style="width: 8%;">Diagnosa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanKunjungans as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->shri->pasien->no_rekam_medis }}</td>
                    <td>{{ $item->shri->pasien->nama_pasien }}</td>
                    <td class="text-center">{{ $item->shri->pasien->jenis_kelamin }}</td>
                    <td>{{ $item->shri->pindah ? $item->shri->pindah->kelas->nama_ruangan : $item->shri->kelasPerawatan->nama_ruangan }}
                    </td>
                    <td class="text-center">
                        {{ $item->shri->pindah ? $item->shri->pindah->kelas->kelas_ruangan : $item->shri->kelasPerawatan->kelas_ruangan }}
                    </td>
                    <td>{{ $item->shri->jenisPenjaminan->jenis_jaminan ?? '-' }}</td>
                    <td>{{ $item->dpjp->nama_lengkap }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($item->shri->tanggal_masuk)) }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($item->tanggal_keluar)) }}</td>
                    <td class="text-center">-</td>
                    <td>{{ $item->diagnosa->diagnosa }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(count($laporanKunjungans) > 0)
        <table>
            <tr class="total-row">
                <td colspan="12" style="text-align: center; font-weight: bold; padding: 10px;">
                    TOTAL PASIEN: {{ count($laporanKunjungans) }} ORANG
                </td>
            </tr>
        </table>
    @endif

    <div class="footer">
        <p>Surabaya, {{ date('d F Y') }}</p>
        <p>Petugas Pelaporan</p>
        <br><br><br>
        <p>(_________________________)</p>
        <p>NIP: </p>
    </div>
</body>

</html>
