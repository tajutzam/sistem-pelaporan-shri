@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-6 underline">Rekapitulasi Kunjungan Rawat Inap</h2>

    <div class="space-y-6">
        <!-- Filter Form -->
        <div class="flex flex-wrap items-end gap-4">
            <!-- Ruangan -->
            <div class="flex flex-col">
                <label for="ruangan" class="mb-1 text-sm font-medium text-gray-700">
                    Ruangan
                </label>
                <input type="text" id="ruangan" name="ruangan" placeholder="Masukkan ruangan"
                    class="bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Tanggal Awal -->
            <div class="flex flex-col">
                <label for="tanggal-awal" class="mb-1 text-sm font-medium text-gray-700">
                    Tanggal Awal
                </label>
                <input type="date" id="tanggal-awal" name="tanggal_awal"
                    class="bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Tanggal Akhir -->
            <div class="flex flex-col">
                <label for="tanggal-akhir" class="mb-1 text-sm font-medium text-gray-700">
                    Tanggal Akhir
                </label>
                <input type="date" id="tanggal-akhir" name="tanggal_akhir"
                    class="bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Tombol Tampilkan -->
            <div>
                <button class="bg-[#34495E] text-white rounded-lg px-5 py-2.5 hover:bg-[#2c3e50] transition font-medium">
                    Tampilkan
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="flex justify-end">
            <div class="relative w-full max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" placeholder="Cari"
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg bg-[#E7E9D4] focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
            </div>
        </div>

        <!-- Tabel Pasien -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">NO</th>
                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">Kelas</th>
                        <th class="px-4 py-2 border">Penjaminan</th>
                        <th class="px-4 py-2 border">DPJP</th>
                        <th class="px-4 py-2 border">Tanggal Masuk</th>
                        <th class="px-4 py-2 border">Tanggal Keluar</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Diagnosa</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">1</td>
                        <td class="px-4 py-2 border">RM123456</td>
                        <td class="px-4 py-2 border">Budi Santoso</td>
                        <td class="px-4 py-2 border">Laki-laki</td>
                        <td class="px-4 py-2 border">Ruang Bedah</td>
                        <td class="px-4 py-2 border">Kelas I</td>
                        <td class="px-4 py-2 border">BPJS</td>
                        <td class="px-4 py-2 border">DJIP001</td>
                        <td class="px-4 py-2 border">2024-06-25</td>
                        <td class="px-4 py-2 border">2024-06-30</td>
                        <td class="px-4 py-2 border">Selesai</td>
                        <td class="px-4 py-2 border">Demam Berdarah</td>
                    </tr>
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
