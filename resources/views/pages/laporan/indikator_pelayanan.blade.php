@extends('layouts.app')

@section('content')

    {{-- @dd($data) --}}
    <h2 class="text-2xl font-bold mb-6 underline">Indikator Pelayanan Rumah Sakit</h2>

    <div class="space-y-6">
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">Filter</h3>
        </div>

        <form method="GET" action="{{ route('laporan-indikator') }}" class="flex items-end space-x-4 mb-4">
            <!-- Pilih Tahun -->
            <div class="flex flex-col">
                <label for="year" class="mb-1 text-sm font-medium text-gray-700">
                    Tahun
                </label>
                <select name="year" id="year"
                    class="w-48 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @for ($y = now()->year; $y >= 2019; $y--)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Pilih Bulan -->
            <div class="flex flex-col">
                <label for="month" class="mb-1 text-sm font-medium text-gray-700">
                    Bulan
                </label>
                <select name="month" id="month"
                    class="w-48 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Semua Bulan</option>
                    <option value="1" {{ request('month') == 1 ? 'selected' : '' }}>Januari</option>
                    <option value="2" {{ request('month') == 2 ? 'selected' : '' }}>Februari</option>
                    <option value="3" {{ request('month') == 3 ? 'selected' : '' }}>Maret</option>
                    <option value="4" {{ request('month') == 4 ? 'selected' : '' }}>April</option>
                    <option value="5" {{ request('month') == 5 ? 'selected' : '' }}>Mei</option>
                    <option value="6" {{ request('month') == 6 ? 'selected' : '' }}>Juni</option>
                    <option value="7" {{ request('month') == 7 ? 'selected' : '' }}>Juli</option>
                    <option value="8" {{ request('month') == 8 ? 'selected' : '' }}>Agustus</option>
                    <option value="9" {{ request('month') == 9 ? 'selected' : '' }}>September</option>
                    <option value="10" {{ request('month') == 10 ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{ request('month') == 11 ? 'selected' : '' }}>November</option>
                    <option value="12" {{ request('month') == 12 ? 'selected' : '' }}>Desember</option>
                </select>
            </div>

            <!-- Tombol -->
            <div>
                <button type="submit" class="bg-gray-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Terapkan
                </button>
            </div>
        </form>


        <div class="overflow-x-auto">
            <!-- Informasi Filter yang Digunakan -->
            @if (request('start_date') || request('end_date'))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    @if (request('start_date') && request('end_date'))
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <span class="text-sm text-blue-700">
                                <strong>Periode:</strong>
                                {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                                -
                                {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                                ({{ \Carbon\Carbon::parse(request('start_date'))->diffInDays(\Carbon\Carbon::parse(request('end_date'))) + 1 }}
                                hari)
                            </span>
                        </div>
                    @endif
                </div>
            @endif


            @php
                $totalTempatTidur = 0;
                $totalJumlahPeriode = 0;
                $totalLamaDirawat = 0;
                $totalHariPerawatan = 0;

                $totalKeluarHidup = 0;
                $totalMati48Plus = 0;
                $totalMati48Minus = 0;

                $totalBor = 0;
                $totalAvlos = 0;
                $totalBto = 0;
                $totalToi = 0;
                $totalGdr = 0;
                $totalNdr = 0;

                $rowCount = count($data['data']);
            @endphp

            <table class="min-w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="border px-3 py-2">Ruangan</th>
                        <th rowspan="2" class="border px-3 py-2">TT</th>
                        <th rowspan="2" class="border px-3 py-2">Periode</th>
                        <th rowspan="2" class="border px-3 py-2">Lama Dirawat</th>
                        <th rowspan="2" class="border px-3 py-2">Hari Perawatan</th>
                        <th colspan="3" class="border px-3 py-2 text-center">Pasien Keluar</th>
                        <th rowspan="2" class="border px-3 py-2">BOR</th>
                        <th rowspan="2" class="border px-3 py-2">AVLOS</th>
                        <th rowspan="2" class="border px-3 py-2">BTO</th>
                        <th rowspan="2" class="border px-3 py-2">TOI</th>
                        <th rowspan="2" class="border px-3 py-2">GDR</th>
                        <th rowspan="2" class="border px-3 py-2">NDR</th>
                    </tr>
                    <tr>
                        <th class="border px-3 py-2">Hidup</th>
                        <th class="border px-3 py-2">≥ 48 Jam</th>
                        <th class="border px-3 py-2">≤ 48 Jam</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($data['data'] as $row)
                        @php
                            $totalTempatTidur += $row['jumlah_tempat_tidur'];
                            $totalJumlahPeriode += $row['jumlah_periode'];
                            $totalLamaDirawat += $row['total_lama_dirawat'];
                            $totalHariPerawatan += $row['jumlah_hari_perawatan'];

                            $totalKeluarHidup += $row['pasien_keluar_hidup'];
                            $totalMati48Plus += $row['pasien_keluar_mati_48_plus'];
                            $totalMati48Minus += $row['pasien_keluar_mati_48_minus'];

                        @endphp




                        <tr>
                            <td class="border px-3 py-2">{{ $row['nama_ruangan'] }}</td>
                            <td class="border px-3 py-2 text-center">{{ $row['jumlah_tempat_tidur'] }}</td>
                            <td class="border px-3 py-2 text-center">{{ $row['jumlah_periode'] }}</td>
                            <td class="border px-3 py-2 text-center">{{ $row['total_lama_dirawat'] }}</td>
                            <td class="border px-3 py-2 text-center">{{ $row['jumlah_hari_perawatan'] }}</td>

                            <td class="border px-3 py-2 text-center">{{ $row['pasien_keluar_hidup'] }}</td>
                            <td class="border px-3 py-2 text-center">{{ $row['pasien_keluar_mati_48_plus'] ?: '0' }}</td>
                            <td class="border px-3 py-2 text-center">{{ $row['pasien_keluar_mati_48_minus'] ?: '0' }}</td>

                            <td class="border px-3 py-2 text-center">{{ number_format($row['bor'], 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($row['avlos'], 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($row['bto'], 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($row['toi'], 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($row['gdr'], 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($row['ndr'], 2) }}</td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="15" class="border text-center py-4 text-gray-500">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse

                    @if ($rowCount > 0)
                        @php

                            // Hitung Total Keluar (Hidup + Mati) untuk pembagi rumus
                            $totalKeluar = $totalKeluarHidup + $totalMati48Plus + $totalMati48Minus;
                            $totalMatiSemua = $totalMati48Plus + $totalMati48Minus;

                            // Perhitungan Rumus Agregat
                            $totalBorAgregat =
                                $totalTempatTidur * $data['periode']['jumlah_hari'] > 0
                                    ? ($totalHariPerawatan / ($totalTempatTidur * $data['periode']['jumlah_hari'])) *
                                        100
                                    : 0;

                            $totalAvlosAgregat = $totalKeluar > 0 ? $totalLamaDirawat / $totalKeluar : 0;

                            $totalBtoAgregat = $totalTempatTidur > 0 ? $totalKeluar / $totalTempatTidur : 0;

                            $totalToiAgregat =
                                $totalKeluar > 0
                                    ? ($totalTempatTidur * $data['periode']['jumlah_hari'] - $totalHariPerawatan) /
                                        $totalKeluar
                                    : 0;

                            $totalGdrAgregat = $totalKeluar > 0 ? ($totalMatiSemua / $totalKeluar) * 1000 : 0;

                            $totalNdrAgregat = $totalKeluar > 0 ? ($totalMati48Plus / $totalKeluar) * 1000 : 0;
                        @endphp


                        <tr class="bg-gray-200 font-bold">
                            <td class="border px-3 py-2 text-center">TOTAL</td>
                            <td class="border px-3 py-2 text-center">{{ $totalTempatTidur }}</td>
                            <td class="border px-3 py-2 text-center">{{ $data['periode']['jumlah_hari'] }}</td>
                            <td class="border px-3 py-2 text-center">{{ $totalLamaDirawat }}</td>
                            <td class="border px-3 py-2 text-center">{{ $totalHariPerawatan }}</td>
                            <td class="border px-3 py-2 text-center">{{ $totalKeluarHidup }}</td>
                            <td class="border px-3 py-2 text-center">{{ $totalMati48Plus }}</td>
                            <td class="border px-3 py-2 text-center">{{ $totalMati48Minus }}</td>

                            <td class="border px-3 py-2 text-center">{{ number_format($totalBorAgregat, 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($totalAvlosAgregat, 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($totalBtoAgregat, 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($totalToiAgregat, 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($totalGdrAgregat, 2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ number_format($totalNdrAgregat, 2) }}</td>
                        </tr>
                    @endif

                </tbody>
            </table>

        </div>

        <div class="flex justify-end items-center gap-3 mt-6">
            {{-- Tombol Verifikasi (Hanya untuk Kepala) --}}
            @if (auth()->user()->hak_akses == 'kepala')
                <form method="post" action="{{ route('laporan.verifikasi') }}">
                    <input type="hidden" name="jenis_laporan" value="indikator">
                    @csrf
                    <button type="submit"
                        class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium inline-flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        VERIFIKASI
                    </button>
                </form>
            @endif

            <a href="{{ route('laporan-indikator.preview-pdf', [
                'year' => request('year', date('Y')),
                'month' => request('month'),
                'ruangan_id' => request('ruangan_id'),
            ]) }}"
                target="_blank"
                class="bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 transition font-medium inline-flex items-center gap-2">
                <i class="fas fa-print"></i>
                Cetak Laporan
            </a>

        </div>
    </div>
@endsection
