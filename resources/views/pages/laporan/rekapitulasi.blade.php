{{-- resources/views/laporan/rekapitulasi.blade.php --}}
@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6 underline">Rekapitulasi SHRI</h2>

    <div class="space-y-6">
        <!-- Filter Header -->
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">Filter</h3>
        </div>

        <!-- Filter Form -->
        <form class="flex flex-wrap items-end gap-4" method="GET" action="{{ route('laporan.rekapitulasi') }}">
            <!-- Ruangan -->
            <div class="flex flex-col">
                <label for="ruangan" class="mb-1 text-sm font-medium text-gray-700">Ruangan</label>
                <input
                    type="text"
                    id="ruangan"
                    name="ruangan"
                    placeholder="Masukkan ruangan"
                    class="w-48 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    value="{{ request('ruangan') }}"
                >
            </div>

            <!-- Tahun -->
            <div class="flex flex-col">
                <label for="tahun" class="mb-1 text-sm font-medium text-gray-700">Tahun</label>
                <select
                    id="tahun"
                    name="tahun"
                    class="w-32 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                    @for ($i = now()->year; $i >= 2000; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Periode -->
            <div class="flex flex-col">
                <label for="periode" class="mb-1 text-sm font-medium text-gray-700">Periode (Bulan)</label>
                <select
                    id="periode"
                    name="periode"
                    class="w-56 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                    @foreach ([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ] as $key => $val)
                        <option value="bulan_{{ $key }}" {{ request('periode') == "bulan_$key" ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div>
                <button
                    type="submit"
                    class="bg-[#34495E] text-white px-5 py-2.5 rounded-lg hover:bg-[#2c3e50] transition font-medium"
                >
                    Tampilkan
                </button>
            </div>
        </form>

        <!-- Form terpisah untuk tombol cetak -->
        @if (isset($data) && !empty($data))
            <form class="flex flex-wrap items-end gap-4" method="GET" action="{{ route('laporan.rekapitulasi.cetak') }}">
                <!-- Hidden inputs untuk menyimpan filter yang sama -->
                <input type="hidden" name="ruangan" value="{{ request('ruangan') }}">
                <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                <input type="hidden" name="periode" value="{{ request('periode') }}">

                <div>
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 transition font-medium"
                    >
                        <i class="fas fa-print mr-2"></i>
                        Cetak PDF
                    </button>
                </div>
            </form>
        @endif

        <!-- Periode Info -->
        @if (isset($tanggal_mulai) && isset($tanggal_selesai))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Menampilkan data periode:
                    {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
                    {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}
                    @if (request('ruangan'))
                        | Ruangan: {{ request('ruangan') }}
                    @endif
                </p>
            </div>
        @endif

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs">
                {{-- THEAD --}}
                @php $kelasList = $kelas_ruangan ?? []; @endphp
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs w-8">Tanggal</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Pasien Awal</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Pasien Masuk</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Pasien Pindahan</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Pasien Dipindahkan</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Pasien Keluar Hidup</th>
                        <th colspan="2" class="border border-black p-2 text-center font-bold text-xs bg-gray-200">Pasien Laki-laki Keluar Mati</th>
                        <th colspan="2" class="border border-black p-2 text-center font-bold text-xs bg-gray-200">Pasien Perempuan Keluar Mati</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Jumlah Lama Dirawat</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Jumlah Hari Perawatan</th>
                        <th colspan="{{ count($kelasList) }}" class="border border-black p-2 text-center font-bold text-xs bg-gray-200">Rincian Hari Perawatan Per Kelas</th>
                    </tr>
                    <tr>
                        <th class="border border-black p-1 text-center font-bold text-xs w-12">&lt; 48<br>Jam</th>
                        <th class="border border-black p-1 text-center font-bold text-xs w-12">≥ 48<br>Jam</th>
                        <th class="border border-black p-1 text-center font-bold text-xs w-12">&lt; 48<br>Jam</th>
                        <th class="border border-black p-1 text-center font-bold text-xs w-12">≥ 48<br>Jam</th>
                        @foreach ($kelasList as $item)
                            <th class="border border-black p-1 text-center font-bold text-xs w-12">{{ $item->kelas_ruangan }}</th>
                        @endforeach
                    </tr>
                </thead>

                {{-- TBODY --}}
                <tbody class="bg-white">
                    @forelse ($data ?? [] as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-black p-2 text-center">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m') }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_awal'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_masuk'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_pindahan'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_dipindahkan'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_hidup'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_mati_l_kurang_48'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_mati_l_lebih_48'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_mati_p_kurang_48'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_mati_p_lebih_48'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['total_lama_dirawat'] > 0 ? $row['total_lama_dirawat'] : '' }}</td>
                            <td class="border border-black p-2 text-center font-semibold">{{ $row['jumlah_hari_perawatan'] }}</td>
                            @foreach ($kelas_ruangan ?? [] as $ruangan)
                                <td class="border border-black p-2 text-center">{{ $row['rincian_per_kelas'][$ruangan->id]->jumlah ?? 0 }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 12 + count($kelas_ruangan ?? []) }}" class="border border-black p-4 text-center text-gray-500">
                                Tidak ada data untuk ditampilkan. Silakan pilih periode dan klik "Tampilkan".
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- TFOOT (TOTAL) --}}
                @if (!empty($data))
                    <tfoot class="bg-gray-100 font-semibold">
                        <tr>
                            <td class="border border-black p-2 text-center font-bold">TOTAL</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_awal') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_masuk') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_pindahan') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_dipindahkan') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_keluar_hidup') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_keluar_mati_l_kurang_48') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_keluar_mati_l_lebih_48') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_keluar_mati_p_kurang_48') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_keluar_mati_p_lebih_48') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('total_lama_dirawat') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('jumlah_hari_perawatan') }}</td>
                            @foreach ($kelas_ruangan ?? [] as $ruangan)
                                <td class="border border-black p-2 text-center">
                                    {{
                                        collect($data)->sum(fn($item) => $item['rincian_per_kelas'][$ruangan->id]->jumlah ?? 0)
                                    }}
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
