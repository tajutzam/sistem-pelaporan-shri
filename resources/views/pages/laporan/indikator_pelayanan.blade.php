@extends('layouts.app')

@section('content')

    @dd($data)
    <h2 class="text-2xl font-bold mb-6 underline">Indikator Pelayanan Rumah Sakit</h2>


    <div class="space-y-6">
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">Filter</h3>
        </div>

        <form method="GET" action="{{ route('laporan-indikator') }}" class="flex items-end space-x-4 mb-4">
            <div class="flex flex-col">
                <label for="start_date" class="mb-1 text-sm font-medium text-gray-700">
                    Tanggal Mulai
                </label>
                <input type="date" name="start_date" id="start_date"
                    class="w-48 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}">
            </div>

            <div class="flex flex-col">
                <label for="end_date" class="mb-1 text-sm font-medium text-gray-700">
                    Tanggal Selesai
                </label>
                <input type="date" name="end_date" id="end_date"
                    class="w-48 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    value="{{ request('end_date', \Carbon\Carbon::now()->toDateString()) }}">
            </div>

            <div>
                <button type="submit" class="bg-gray-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Terapkan
                </button>
            </div>
        </form>


        <div class="overflow-x-auto">

            <!-- Informasi Filter yang Digunakan -->
            @if(request('start_date') || request('end_date'))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    @if(request('start_date') && request('end_date'))
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
                        <th class="px-4 py-2 border">Mati &gt; 48 jam</th>
                        <th class="px-4 py-2 border">Mati</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['data'] as $row)
                        <tr>
                            <td class="px-4 py-2 border">{{ $row['nama_ruangan'] }}</td>
                            <td class="px-4 py-2 border text-center">{{ $row['jumlah_tempat_tidur'] }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['jumlah_periode'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ $row['total_lama_dirawat'] }}</td>
                            <td class="px-4 py-2 border text-center">{{ $row['jumlah_hari_perawatan'] }}</td>
                            <td class="px-4 py-2 border text-center">{{ $row['pasien_keluar_hidup'] }}</td>
                            <td class="px-4 py-2 border text-center">
                                {{ $row['pasien_keluar_mati'] > 0 ? $row['pasien_keluar_mati'] : '-' }}
                            </td>
                            <td class="px-4 py-2 border text-center">{{ $row['total_pasien_keluar'] }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['bor'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['avlos'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['bto'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['toi'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['gdr'], 2) }}</td>
                            <td class="px-4 py-2 border text-center">{{ number_format($row['ndr'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-4 py-4 border text-center text-gray-500">
                                Tidak ada data untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <button class="bg-[#34B3AE] text-white px-5 py-2 rounded-lg hover:bg-[#2ca8a3] transition">
                Cetak Laporan
            </button>
        </div>
    </div>
@endsection
