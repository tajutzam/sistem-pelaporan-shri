@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6 underline">20 Besar Penyakit</h2>

    <form method="GET" class="p-6 rounded-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-4">

            <!-- Filter Ruangan -->
            <div class="flex flex-col">
                <label for="ruangan" class="mb-2 text-sm font-semibold text-gray-700">Ruangan</label>
                <select id="ruangan" name="ruangan"
                    class="bg-white border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach ($ruangans as $r)
                        <option value="{{ $r->nama_ruangan }}"
                            {{ request('ruangan') == $r->nama_ruangan ? 'selected' : '' }}>
                            {{ $r->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>


            <!-- Filter Tahun -->
            <div class="flex flex-col">
                <label for="tahun" class="mb-2 text-sm font-semibold text-gray-700">Tahun</label>
                <select id="tahun" name="tahun"
                    class="bg-white border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @for ($i = now()->year; $i >= 2000; $i--)
                        <option value="{{ $i }}" {{ request('tahun', now()->year) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Filter Periode -->
            <div class="flex flex-col">
                <label for="periode" class="mb-2 text-sm font-semibold text-gray-700">Periode</label>
                <select id="periode" name="periode"
                    class="bg-white border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="semua" {{ request('periode', 'semua') == 'semua' ? 'selected' : '' }}>
                        Semua Bulan
                    </option>
                    <optgroup label="Per Bulan">
                        @foreach ([
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ] as $key => $val)
                            <option value="bulan_{{ $key }}"
                                {{ request('periode') == "bulan_$key" ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <!-- Filter Pencarian -->
            <div class="flex flex-col">
                <label for="search" class="mb-2 text-sm font-semibold text-gray-700">Cari Diagnosa/ICD</label>
                <input type="text" id="search" name="search" placeholder="Cari diagnosa atau kode ICD..."
                    class="bg-white border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    value="{{ request('search') }}">
            </div>

            <!-- Tombol Action -->
            <div class="flex flex-col justify-end">
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white rounded-md px-4 py-2.5 text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center">
                        Tampilkan
                    </button>
                    <a href="{{ route('laporan-10-penyakit') }}"
                        class="bg-gray-500 text-white rounded-md px-4 py-2.5 text-sm font-medium hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Filter Aktif -->
        @if (request()->hasAny(['ruangan', 'tahun', 'periode', 'search']))
            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-blue-800 font-medium">Filter Aktif:</span>
                </div>
                <div class="mt-2 flex flex-wrap gap-2">
                    @if (request('ruangan'))
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                            Ruangan: {{ request('ruangan') }}
                        </span>
                    @endif
                    @if (request('tahun'))
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                            Tahun: {{ request('tahun') }}
                        </span>
                    @endif
                    @if (request('periode') && request('periode') != 'semua')
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                            Periode: {{ ucfirst(str_replace(['bulan_', '_'], ['', ' '], request('periode'))) }}
                        </span>
                    @endif
                    @if (request('search'))
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">No</th>
                    <th class="px-4 py-2 border">Diagnosa</th>
                    <th class="px-4 py-2 border">Kode ICD-10</th>
                    <th class="px-4 py-2 border">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($diagnosas as $index => $diagnosa)
                    <tr>
                        <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 border">{{ $diagnosa->diagnosa }}</td>
                        <td class="px-4 py-2 border">{{ $diagnosa->kode_icd }}</td>
                        <td class="px-4 py-2 border text-center">{{ $diagnosa->jumlah }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 border text-center text-gray-500">
                            Tidak ada data diagnosa ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="flex justify-end mt-4 gap-4">
            @if (auth()->user()->hak_akses == 'kepala')
                <form method="post" action="{{ route('laporan.verifikasi') }}">
                    <input type="hidden" name="jenis_laporan" value="penyakit">
                    @csrf

                    <button type="submit" @if (isset($approved) && $approved) disabled @endif
                        class="{{ isset($approved) && $approved ? 'bg-green-600' : 'bg-gray-600 hover:bg-gray-700' }} text-white px-5 py-2.5 rounded-lg transition font-medium inline-flex items-center gap-2">

                        <i
                            class="fas {{ isset($approved) && $approved ? 'fa-check-double' : 'fa-check' }}"></i>

                        <span>
                            {{ isset($approved) && $approved ? 'TERVERIFIKASI' : 'VERIFIKASI' }}
                        </span>
                    </button>
                </form>
            @endif

            <a href="{{ route('laporan-10-penyakit.cetak', request()->all()) }}" target="_blank"
                class="bg-green-600 text-white rounded-md px-6 py-2.5 text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200 flex items-center shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Laporan (PDF)
            </a>
        </div>
    </div>
@endsection
