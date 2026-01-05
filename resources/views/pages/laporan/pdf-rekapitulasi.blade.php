<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi SHRI</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: sans-serif; font-size: 8px; line-height: 1.2; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; padding: 0; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 3px 2px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .bg-gray { background-color: #eeeeee; }
        .footer { margin-top: 15px; font-size: 7px; text-align: right; }
        .info { margin-bottom: 5px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI SHRI</h2>
    </div>

    <div class="info">
        <strong>Periode:</strong> 
        @if(request('periode') == 'bulan_semua_bulan')
            Tahun {{ $tahun }} (Seluruh Bulan)
        @else
            {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}
        @endif
        <br>
        <strong>Ruangan:</strong> {{ $ruangan_pilihan ?: 'Semua Ruangan' }}
    </div>

    <table>
        <thead>
            <tr>
                {{-- JUDUL KOLOM DINAMIS --}}
                <th rowspan="2" style="width: 50px;">
                    {{ request('periode') == 'bulan_semua_bulan' ? 'Bulan' : 'Tanggal' }}
                </th>
                <th rowspan="2">Ps. Awal</th>
                <th rowspan="2">Ps. Masuk</th>
                <th rowspan="2">Ps. Pindah</th>
                <th rowspan="2">Ps. Dipindah</th>
                <th rowspan="2">Ps. Keluar Hidup</th>
                <th colspan="2" class="bg-gray">Ps. Laki Keluar Mati</th>
                <th colspan="2" class="bg-gray">Ps. Pr Keluar Mati</th>
                <th rowspan="2">Lama Dirawat</th>
                <th rowspan="2">Hari Perawatan</th>
                <th colspan="{{ count($kelas_ruangan) }}" class="bg-gray">Rincian Per Kelas</th>
            </tr>
            <tr>
                <th style="width: 25px;">&lt; 48J</th>
                <th style="width: 25px;">&ge; 48J</th>
                <th style="width: 25px;">&lt; 48J</th>
                <th style="width: 25px;">&ge; 48J</th>
                @foreach ($kelas_ruangan as $item)
                    <th style="min-width: 25px;">{{ $item->nama_kelas }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    {{-- ISI KOLOM DINAMIS --}}
                    <td>
                        {{ isset($row['is_bulanan']) ? $row['tanggal'] : \Carbon\Carbon::parse($row['tanggal'])->format('d/m') }}
                    </td>
                    <td>{{ $row['pasien_awal'] }}</td>
                    <td>{{ $row['pasien_masuk'] }}</td>
                    <td>{{ $row['pasien_pindahan'] }}</td>
                    <td>{{ $row['pasien_dipindahkan'] }}</td>
                    <td>{{ $row['pasien_keluar_hidup'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_l_kurang_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_l_lebih_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_p_kurang_48'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_p_lebih_48'] }}</td>
                    <td>{{ $row['total_lama_dirawat'] > 0 ? $row['total_lama_dirawat'] : 0 }}</td>
                    <td><strong>{{ $row['jumlah_hari_perawatan'] }}</strong></td>
                    @foreach ($kelas_ruangan as $kelas)
                        <td>{{ $row['rincian_per_kelas']->get($kelas->id)->jumlah ?? 0 }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <td>TOTAL</td>
            {{-- Bagian Total tetap menggunakan sum collect agar akurat --}}
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
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>