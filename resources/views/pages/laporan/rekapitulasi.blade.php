@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6 underline">Rekapitulasi SHRI</h2>

    <div class="space-y-6">
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">Filter</h3>
        </div>

        <form class="flex flex-wrap items-end gap-4" method="GET" action="{{ route('laporan.rekapitulasi') }}">
            <div class="flex flex-col">
                <label for="ruangan" class="mb-1 text-sm font-medium text-gray-700">Ruangan</label>
                <select id="ruangan" name="ruangan"
                    class="w-48 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach ($ruangans as $r)
                        <option value="{{ $r->nama_ruangan }}"
                            {{ request('ruangan') == $r->nama_ruangan ? 'selected' : '' }}>
                            {{ $r->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label for="tahun" class="mb-1 text-sm font-medium text-gray-700">Tahun</label>
                <select id="tahun" name="tahun"
                    class="w-32 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @for ($i = now()->year; $i >= 2000; $i--)
                        <option value="{{ $i }}" {{ request('tahun', $tahun) == $i ? 'selected' : '' }}>
                            {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="flex flex-col">
                <label for="periode" class="mb-1 text-sm font-medium text-gray-700">Periode (Bulan)</label>
                <select id="periode" name="periode"
                    class="w-56 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="bulan_semua_bulan" {{ request('periode') == 'bulan_semua_bulan' ? 'selected' : '' }}>
                        Semua Bulan</option>
                    @foreach (['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $key => $val)
                        <option value="bulan_{{ $key }}"
                            {{ request('periode') == "bulan_$key" ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit"
                    class="bg-[#34495E] text-white px-5 py-2.5 rounded-lg hover:bg-[#2c3e50] transition font-medium">Tampilkan</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs">
                @php $kelasList = $kelas_ruangan ?? []; @endphp
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">
                            {{ request('periode') == 'bulan_semua_bulan' ? 'Bulan' : 'Tanggal' }}
                        </th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Pasien Awal</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Pasien Masuk</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Pindahan</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Dipindahkan</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Keluar Hidup</th>
                        <th colspan="2" class="border border-black p-2 text-center font-bold text-xs bg-gray-200">Mati
                            Laki</th>
                        <th colspan="2" class="border border-black p-2 text-center font-bold text-xs bg-gray-200">Mati
                            Perempuan</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Lama Dirawat</th>
                        <th rowspan="2" class="border border-black p-2 text-center font-bold text-xs">Hari Perawatan</th>
                        <th colspan="{{ count($kelasList) }}"
                            class="border border-black p-2 text-center font-bold text-xs bg-gray-200">Rincian Per Kelas</th>
                    </tr>
                    <tr>
                        <th class="border border-black p-1 text-center font-bold">&lt; 48J</th>
                        <th class="border border-black p-1 text-center font-bold">&ge; 48J</th>
                        <th class="border border-black p-1 text-center font-bold">&lt; 48J</th>
                        <th class="border border-black p-1 text-center font-bold">&ge; 48J</th>
                        @foreach ($kelasList as $item)
                            <th class="border border-black p-1 text-center font-bold">{{ $item->nama_kelas }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse ($data ?? [] as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-black p-2 text-center font-medium">
                                {{ isset($row['is_bulanan']) ? $row['tanggal'] : \Carbon\Carbon::parse($row['tanggal'])->format('d/m') }}
                            </td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_awal'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_masuk'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_pindahan'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_dipindahkan'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_hidup'] }}</td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_mati_l_kurang_48'] }}
                            </td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_mati_l_lebih_48'] }}
                            </td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_mati_p_kurang_48'] }}
                            </td>
                            <td class="border border-black p-2 text-center">{{ $row['pasien_keluar_mati_p_lebih_48'] }}
                            </td>
                            <td class="border border-black p-2 text-center">
                                {{ $row['total_lama_dirawat'] > 0 ? $row['total_lama_dirawat'] : 0 }}</td>
                            <td class="border border-black p-2 text-center font-semibold">
                                {{ $row['jumlah_hari_perawatan'] }}</td>
                            @foreach ($kelasList as $kelas)
                                <td class="border border-black p-2 text-center">
                                    {{ $row['rincian_per_kelas']->get($kelas->id)->jumlah ?? 0 }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 12 + count($kelasList) }}"
                                class="border border-black p-4 text-center text-gray-500">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>

                @if (!empty($data))
                    <tfoot class="bg-gray-100 font-semibold">
                        <tr>
                            <td class="border border-black p-2 text-center font-bold">TOTAL</td>
                            {{-- Sesuai standar SHRI, total pasien awal biasanya tidak dijumlahkan lurus ke bawah, 
                                 namun jika Anda ingin sum kolektif dari baris yang tampil: --}}
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_awal') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_masuk') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_pindahan') }}
                            </td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('pasien_dipindahkan') }}
                            </td>
                            <td class="border border-black p-2 text-center">
                                {{ collect($data)->sum('pasien_keluar_hidup') }}</td>
                            <td class="border border-black p-2 text-center">
                                {{ collect($data)->sum('pasien_keluar_mati_l_kurang_48') }}</td>
                            <td class="border border-black p-2 text-center">
                                {{ collect($data)->sum('pasien_keluar_mati_l_lebih_48') }}</td>
                            <td class="border border-black p-2 text-center">
                                {{ collect($data)->sum('pasien_keluar_mati_p_kurang_48') }}</td>
                            <td class="border border-black p-2 text-center">
                                {{ collect($data)->sum('pasien_keluar_mati_p_lebih_48') }}</td>
                            <td class="border border-black p-2 text-center">{{ collect($data)->sum('total_lama_dirawat') }}
                            </td>
                            <td class="border border-black p-2 text-center">
                                {{ collect($data)->sum('jumlah_hari_perawatan') }}</td>
                            @foreach ($kelasList as $kelas)
                                <td class="border border-black p-2 text-center">
                                    {{ collect($data)->sum(fn($item) => $item['rincian_per_kelas']->get($kelas->id)->jumlah ?? 0) }}
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
            <div class="flex gap-4 mt-4 justify-end">

                @if (auth()->user()->hak_akses == 'kepala')
                    <form method="post" action="{{ route('laporan.verifikasi') }}">
                        <input type="hidden" name="jenis_laporan" value="rekapitulasi">
                        @csrf
                        <button type="submit"
                            class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium inline-flex items-center gap-2">
                            <i class="fas fa-check"></i>
                            VERIFIKASI
                        </button>
                    </form>
                @endif

                @if (isset($data) && !empty($data))
                    <form method="GET" action="{{ route('laporan.rekapitulasi.cetak') }}" target="_blank">
                        <input type="hidden" name="ruangan" value="{{ request('ruangan') }}">
                        <input type="hidden" name="tahun" value="{{ request('tahun', $tahun) }}">
                        <input type="hidden" name="periode" value="{{ request('periode', $periode) }}">
                        <button type="submit"
                            class="bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 transition font-medium">
                            <i class="fas fa-print mr-2"></i> Cetak Laporan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
