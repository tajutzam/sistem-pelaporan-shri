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

            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border" rowspan="2">Ruangan</th>
                        <th class="px-4 py-2 border" rowspan="2">Jumlah tempat tidur</th>
                        <th class="px-4 py-2 border" rowspan="2">Jumlah Periode</th>
                        <th class="px-4 py-2 border" rowspan="2">Lama dirawat</th>
                        <th class="px-4 py-2 border" rowspan="2">Hari perawatan</th>
                        <th class="px-4 py-2 border text-center" colspan="3">Pasien Keluar</th>
                        <th class="px-4 py-2 border" rowspan="2">BOR</th>
                        <th class="px-4 py-2 border" rowspan="2">AVLos</th>
                        <th class="px-4 py-2 border" rowspan="2">BTO</th>
                        <th class="px-4 py-2 border" rowspan="2">TOI</th>
                        <th class="px-4 py-2 border" rowspan="2">GDR</th>
                        <th class="px-4 py-2 border" rowspan="2">NDR</th>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 border">Hidup</th>
                        <th class="px-4 py-2 border">Mati ≥ 48 jam</th>
                        <th class="px-4 py-2 border">Mati ≤ 48 jam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['data'] as $row)
                        <tr>
                            <td class="px-4 py-2 border">{{ $row['nama_ruangan'] }}</td>
                            <td class="px-4 py-2 border text-center">{{ $row['jumlah_tempat_tidur'] }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['jumlah_periode'], 0) }}</td>
                            <td class="px-4 py-2 border text-center">{{ $row['total_lama_dirawat'] }}</td>
                            <td class="px-4 py-2 border text-center">{{ $row['jumlah_hari_perawatan'] }}</td>

                            <td class="px-4 py-2 border text-center">{{ $row['pasien_keluar_hidup'] }}</td>
                            <td class="px-4 py-2 border text-center">
                                {{ $row['pasien_keluar_mati_48_plus'] > 0 ? $row['pasien_keluar_mati_48_plus'] : '-' }}
                            </td>
                            <td class="px-4 py-2 border text-center">
                                {{ $row['pasien_keluar_mati_48_minus'] > 0 ? $row['pasien_keluar_mati_48_minus'] : '-' }}
                            </td>

                            <td class="px-4 py-2 border text-center">{{ number_format($row['bor'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['avlos'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['bto'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['toi'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['gdr'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['ndr'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-4 py-4 border text-center text-gray-500">
                                Tidak ada data untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
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

            {{-- Tombol Cetak Laporan (Seragam: Hijau, Kanan Bawah) --}}
            <a href="{{ route('laporan-indikator.preview-pdf', [
                'start_date' =>
                    request('year', date('Y')) .
                    '-' .
                    (request('month') ? str_pad(request('month'), 2, '0', STR_PAD_LEFT) : '01') .
                    '-01',
                'end_date' =>
                    request('year', date('Y')) .
                    '-' .
                    (request('month') ? str_pad(request('month'), 2, '0', STR_PAD_LEFT) : '12') .
                    '-31',
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
