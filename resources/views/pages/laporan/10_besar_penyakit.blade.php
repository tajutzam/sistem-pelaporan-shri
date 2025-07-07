@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6 underline">Indikator Pelayanan Rumah Sakit</h2>

    <div class="space-y-6">
        <!-- Section Filter -->
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">Filter</h3>
        </div>

        <!-- Filter Form -->
        <form class="flex flex-wrap items-end gap-4">

            <!-- Ruangan -->
             <div class="flex flex-col">
                <label for="ruangan" class="mb-1 text-sm font-medium text-gray-700">
                    Ruangan
                </label>
                <input type="text" id="ruangan" name="ruangan" placeholder="Masukkan ruangan"
                    class="bg-white border border-gray-300 rounded px-3 py-2 w-48 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <!-- Tahun -->
            <div class="flex flex-col">
                <label for="tahun" class="mb-1 text-sm font-medium text-gray-700">
                    Tahun
                </label>
                <select id="tahun" name="tahun"
                    class="bg-white border border-gray-300 rounded px-3 py-2 w-32 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @for ($i = now()->year; $i >= 2000; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Periode (Bulan / Triwulan) -->
            <div class="flex flex-col">
                <label for="periode" class="mb-1 text-sm font-medium text-gray-700">
                    Periode (Bulan/Triwulan)
                </label>
                <select id="periode" name="periode"
                    class="bg-white border border-gray-300 rounded px-3 py-2 w-56 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <optgroup label="Per Bulan">
                        @foreach ([
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ] as $key => $val)
                            <option value="bulan_{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Per Triwulan">
                        <option value="triwulan_1">Triwulan 1 (Jan - Mar)</option>
                        <option value="triwulan_2">Triwulan 2 (Apr - Jun)</option>
                        <option value="triwulan_3">Triwulan 3 (Jul - Sep)</option>
                        <option value="triwulan_4">Triwulan 4 (Okt - Des)</option>
                    </optgroup>
                </select>
            </div>

            <!-- Tombol Tampilkan -->
            <div>
                <button type="submit"
                    class="bg-[#34495E] text-white rounded-lg px-5 py-2.5 hover:bg-[#2c3e50] transition font-medium">
                    Tampilkan
                </button>
            </div>
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
            </table>
        </div>

        <!-- Tombol Cetak -->
        <div class="flex justify-end">
            <button class="bg-[#34B3AE] text-white px-5 py-2 rounded-lg hover:bg-[#2ca8a3] transition">
                Cetak Laporan
            </button>
        </div>
    </div>
@endsection
