{{-- resources/views/laporan/rekapitulasi_pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi SHRI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
            text-decoration: underline;
        }

        .filter-info {
            background-color: #f0f8ff;
            border: 1px solid #b0d4f1;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 9px;
        }

        .filter-info strong {
            color: #2c5aa0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        thead th {
            background-color: #e8e8e8;
            font-weight: bold;
            font-size: 6px;
        }

        .header-group {
            background-color: #d0d0d0;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        tfoot td {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
        }

        .empty-data {
            color: #666;
            font-style: italic;
        }

        .date-col {
            width: 40px;
        }

        .narrow-col {
            width: 30px;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekapitulasi SHRI</h1>
    </div>

    <!-- Filter Info -->
    @if (isset($tanggal_mulai) && isset($tanggal_selesai))
        <div class="filter-info">
            <strong>Periode:</strong>
            {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
            {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}
            @if (request('ruangan'))
                <br><strong>Ruangan:</strong> {{ request('ruangan') }}
            @endif
        </div>
    @endif

    <!-- Data Table -->
    <table>
        {{-- THEAD --}}
        @php $kelasList = $kelas_ruangan; @endphp
        <thead>
            <tr>
                <th rowspan="2" class="date-col">Tanggal</th>
                <th rowspan="2" class="narrow-col">Pasien<br>Awal</th>
                <th rowspan="2" class="narrow-col">Pasien<br>Masuk</th>
                <th rowspan="2" class="narrow-col">Pasien<br>Pindahan</th>
                <th rowspan="2" class="narrow-col">Pasien<br>Dipindahkan</th>
                <th rowspan="2" class="narrow-col">Pasien<br>Keluar<br>Hidup</th>
                <th colspan="2" class="header-group">Pasien Laki-laki<br>Keluar Mati</th>
                <th colspan="2" class="header-group">Pasien Perempuan<br>Keluar Mati</th>
                <th rowspan="2" class="narrow-col">Jumlah<br>Lama<br>Dirawat</th>
                <th rowspan="2" class="narrow-col">Jumlah<br>Hari<br>Perawatan</th>
                <th colspan="{{ count($kelasList) }}" class="header-group">Rincian Hari Perawatan Per Kelas</th>
            </tr>
            <tr>
                <th class="narrow-col">&lt; 48<br>Jam</th>
                <th class="narrow-col">≥ 48<br>Jam</th>
                <th class="narrow-col">&lt; 48<br>Jam</th>
                <th class="narrow-col">≥ 48<br>Jam</th>
                @foreach ($kelasList as $item)
                    <th class="narrow-col">{{ $item->kelas_ruangan }}</th>
                @endforeach
            </tr>
        </thead>

        {{-- TBODY --}}
        <tbody>
            @forelse ($data ?? [] as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m') }}</td>
                    <td>{{ $row['pasien_awal'] }}</td>
                    <td>{{ $row['pasien_masuk'] }}</td>
                    <td>{{ $row['pasien_pindahan'] }}</td>
                    <td>{{ $row['pasien_dipindahkan'] }}</td>
                    <td>{{ $row['pasien_keluar_hidup'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_l_kurang_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_l_lebih_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_p_kurang_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_p_lebih_48'] }}</td>
                    <td>{{ $row['total_lama_dirawat'] > 0 ? $row['total_lama_dirawat'] : '' }}</td>
                    <td style="font-weight: bold;">{{ $row['jumlah_hari_perawatan'] }}</td>
                    @foreach ($kelas_ruangan ?? [] as $ruangan)
                        <td>{{ $row['rincian_per_kelas'][$ruangan->id]->jumlah ?? 0 }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 12 + count($kelas_ruangan ?? []) }}" class="empty-data">
                        Tidak ada data untuk ditampilkan
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- TFOOT (TOTAL) --}}
        @if (!empty($data))
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td>{{ collect($data)->sum('pasien_awal') }}</td>
                    <td>{{ collect($data)->sum('pasien_masuk') }}</td>
                    <td>{{ collect($data)->sum('pasien_pindahan') }}</td>
                    <td>{{ collect($data)->sum('pasien_dipindahkan') }}</td>
                    <td>{{ collect($data)->sum('pasien_keluar_hidup') }}</td>
                    <td>{{ collect($data)->sum('pasien_keluar_mati_l_kurang_48') }}</td>
                    <td>{{ collect($data)->sum('pasien_keluar_mati_l_lebih_48') }}</td>
                    <td>{{ collect($data)->sum('pasien_keluar_mati_p_kurang_48') }}</td>
                    <td>{{ collect($data)->sum('pasien_keluar_mati_p_lebih_48') }}</td>
                    <td>{{ collect($data)->sum('total_lama_dirawat') }}</td>
                    <td>{{ collect($data)->sum('jumlah_hari_perawatan') }}</td>
                    @foreach ($kelas_ruangan ?? [] as $ruangan)
                        <td>
                            {{
                                collect($data)->sum(fn($item) => $item['rincian_per_kelas'][$ruangan->id]->jumlah ?? 0)
                            }}
                        </td>
                    @endforeach
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
