<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Indikator Pelayanan Rumah Sakit</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; margin: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #34495E; }
        .hospital-name { font-size: 14px; font-weight: bold; color: #34495E; }
        .report-title { font-size: 12px; font-weight: bold; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f2f2f2; font-weight: bold; border: 1px solid #000; padding: 4px; }
        td { border: 1px solid #000; padding: 4px; text-align: center; }
        .text-left { text-align: left !important; }
        .footer { margin-top: 20px; font-size: 9px; }
        .signature-area { margin-top: 30px; text-align: right; }
        .signature-box { display: inline-block; text-align: center; }
        .signature-line { border-bottom: 1px solid #000; width: 180px; margin: 50px auto 5px auto; }
    </style>
</head>
<body>
    <div class="header">
        <div class="hospital-name">{{ config('app.hospital_name', 'RUMAH SAKIT UMUM') }}</div>
        <div class="report-title">LAPORAN INDIKATOR PELAYANAN RUMAH SAKIT</div>
        <div style="font-size: 10px;">
            Periode: {{ $start_date->format('d/m/Y') }} - {{ $end_date->format('d/m/Y') }}
            ({{ $start_date->diffInDays($end_date) + 1 }} Hari)
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Ruangan</th>
                <th rowspan="2">TT</th>
                <th rowspan="2">LD</th>
                <th rowspan="2">HP</th>
                <th colspan="3">Pasien Keluar</th>
                <th rowspan="2">BOR<br>(%)</th>
                <th rowspan="2">AVLos<br>(Hari)</th>
                <th rowspan="2">BTO<br>(Kali)</th>
                <th rowspan="2">TOI<br>(Hari)</th>
                <th rowspan="2">GDR<br>(‰)</th>
                <th rowspan="2">NDR<br>(‰)</th>
            </tr>
            <tr>
                <th>Hidup</th>
                <th>Mati<br>&ge;48j</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['data'] as $row)
                <tr>
                    <td class="text-left">{{ $row['nama_ruangan'] }}</td>
                    <td>{{ $row['jumlah_tempat_tidur'] }}</td>
                    <td>{{ $row['total_lama_dirawat'] }}</td>
                    <td>{{ $row['jumlah_hari_perawatan'] }}</td>
                    <td>{{ $row['pasien_keluar_hidup'] }}</td>
                    <td>{{ $row['pasien_keluar_mati_48_plus'] }}</td>
                    {{-- Total Keluar = Hidup + Mati (Semua) --}}
                    <td>{{ $row['pasien_keluar_hidup'] + $row['pasien_keluar_mati_48_plus'] + $row['pasien_keluar_mati_48_minus'] }}</td>
                    <td>{{ number_format($row['bor'], 2) }}</td>
                    <td>{{ number_format($row['avlos'], 2) }}</td>
                    <td>{{ number_format($row['bto'], 2) }}</td>
                    <td>{{ number_format($row['toi'], 2) }}</td>
                    <td>{{ number_format($row['gdr'], 2) }}</td>
                    <td>{{ number_format($row['ndr'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
        {{-- Baris Total Keseluruhan --}}
        @if(count($data['data']) > 0)
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <td>TOTAL</td>
            <td>{{ collect($data['data'])->sum('jumlah_tempat_tidur') }}</td>
            <td>{{ collect($data['data'])->sum('total_lama_dirawat') }}</td>
            <td>{{ collect($data['data'])->sum('jumlah_hari_perawatan') }}</td>
            <td>{{ collect($data['data'])->sum('pasien_keluar_hidup') }}</td>
            <td>{{ collect($data['data'])->sum('pasien_keluar_mati_48_plus') }}</td>
            <td>{{ collect($data['data'])->sum(fn($r) => $r['pasien_keluar_hidup'] + $r['pasien_keluar_mati_48_plus'] + $r['pasien_keluar_mati_48_minus']) }}</td>
            <td colspan="6"></td>
        </tr>
        @endif
    </table>

    <div class="footer">
        <strong>Keterangan:</strong> TT: Tempat Tidur, LD: Lama Dirawat, HP: Hari Perawatan, BOR: Bed Occupancy Rate, AVLos: Average Length of Stay, BTO: Bed Turn Over, TOI: Turn Over Interval, GDR: Gross Death Rate, NDR: Net Death Rate.
    </div>

    <div class="signature-area">
        <div class="signature-box">
            {{ now()->translatedFormat('d F Y') }}<br>
            Kepala Bagian Rekam Medis
            <div class="signature-line"></div>
            ( _________________________ )
        </div>
    </div>
</body>
</html>