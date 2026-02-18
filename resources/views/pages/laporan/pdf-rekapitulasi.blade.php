<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Sensus Harian Rawat Inap</title>
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }

        /* Logic Penomoran Halaman */
        .page-number:before {
            content: counter(page);
        }
        .page-count:before {
            content: counter(pages);
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            margin: 0;
            color: #000;
            line-height: 1.3;
        }

        /* Header Section */
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
            text-transform: uppercase;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            display: inline-block;
            margin-top: 5px;
        }

        /* Meta Data Section */
        .meta-container {
            width: 100%;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .meta-table {
            width: 100%;
            border: none;
        }

        /* Main Table */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed; /* Membantu menjaga lebar kolom tetap konsisten */
        }

        /* INI KUNCI AGAR HEADER ADA DI SEMUA HALAMAN */
        table.main-table thead {
            display: table-header-group;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.main-table th {
            background-color: #eee;
            font-size: 8.5px;
        }

        /* Footer / Signature Section */
        .footer-section {
            margin-top: 30px;
            width: 100%;
            page-break-inside: avoid; /* Mencegah tanda tangan terpotong halaman */
        }

        .signature-space {
            height: 60px;
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
        <div class="report-title">LAPORAN REKAPITULASI SENSUS HARIAN RAWAT INAP</div>
    </div>

    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td width="120">Ruangan</td>
                <td width="400">: {{ $ruangan_pilihan ?: 'Semua Ruangan' }}</td>
                
                <td width="120">Tanggal Cetak</td>
                <td>: {{ date('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Periode Laporan</td>
                <td>
                    : @if(request('periode') == 'bulan_semua_bulan')
                        Tahun {{ $tahun }}
                      @else
                        {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d/m/Y') }}
                      @endif
                </td>
                
                <td>User</td>
                <td>: {{ auth()->user()->name ?? '(nama user)' }}</td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">
                    {{ request('periode') == 'bulan_semua_bulan' ? 'Bulan' : 'Tgl' }}
                </th>
                <th rowspan="2">Pasien Awal</th>
                <th rowspan="2">Pasien Masuk</th>
                <th rowspan="2">Pasien Pindahan</th>
                <th rowspan="2">Pindah Dipindahkan</th>
                <th rowspan="2">Pasien Keluar Hidup</th>
                
                <th colspan="2">Pasien Laki-Laki Keluar Mati</th>
                <th colspan="2">Pasien Perempuan Keluar Mati</th>
                
                <th rowspan="2">Jumlah Lama Dirawat</th>
                <th rowspan="2">Jumlah Hari Perawatan</th>
                
                <th colspan="{{ count($kelas_ruangan) }}" style="background-color: #ddd;">Rincian Hari Perawatan Per Kelas</th>
            </tr>
            <tr>
                <th>< 48 Jam</th>
                <th>>= 48 Jam</th>
                <th>< 48 Jam</th>
                <th>>= 48 Jam</th>
                @foreach ($kelas_ruangan as $item)
                    <th>{{ $item->nama_kelas }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ isset($row['is_bulanan']) ? $row['tanggal'] : \Carbon\Carbon::parse($row['tanggal'])->format('d/m') }}</td>
                    <td>{{ $row['pasien_awal'] }}</td>
                    <td>{{ $row['pasien_masuk'] }}</td>
                    <td>{{ $row['pasien_pindahan'] }}</td>
                    <td>{{ $row['pasien_dipindahkan'] }}</td>
                    <td>{{ $row['pasien_keluar_hidup'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_l_kurang_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_l_lebih_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_p_kurang_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_p_lebih_48'] }}</td>
                    <td>{{ $row['total_lama_dirawat'] }}</td>
                    <td>{{ $row['jumlah_hari_perawatan'] }}</td>
                    @foreach ($kelas_ruangan as $kelas)
                        <td>{{ $row['rincian_per_kelas']->get($kelas->id)->jumlah ?? 0 }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
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
                @foreach ($kelas_ruangan as $kelas)
                    <td>{{ collect($data)->sum(fn($item) => $item['rincian_per_kelas']->get($kelas->id)->jumlah ?? 0) }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>

    <div class="footer-section">
        <div style="width: 100%;">
            <div style="float: left; width: 45%; text-align: center;">
                <br><br>
                <strong>Petugas Pelaporan</strong>
                <div class="signature-space"></div>
                <span class="underline">( {{ auth()->user()->name ?? 'Nama Petugas' }} )</span>
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