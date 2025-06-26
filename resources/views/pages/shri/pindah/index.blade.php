@extends('layouts.app')

@section('content')

    <h2 class="font-bold text-2xl mb-4 underline">Pasien Masuk</h2>

    <div>
        <!-- Filter Tanggal -->
        <div class="flex gap-4">
            <div class="rounded-lg px-4 flex items-center" style="background-color: #EADDFF;">
                <span>Pilih Tanggal Sensus</span>
            </div>
            <input type="date" class="border border-gray-500 rounded px-4 py-2">
        </div>

        <!-- Header Section -->
        <div class="flex justify-between mt-3 bg-[#34495E] p-4 rounded-lg text-white">
            <h2>Daftar Pasien Masuk</h2>
            <a href="{{ route('form.masuk', ['id' => 1]) }}" class="flex items-center gap-3">
                <i class="fa-solid fa-plus"></i>
                <button>Tambah</button>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="flex justify-end mt-4">
            <div class="relative w-full max-w-xs bg-[#E7E9D4]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" placeholder="Search..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
            </div>
        </div>

        <!-- Tabel Pasien Masuk -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Tanggal Masuk</th>
                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Tanggal Lahir</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Status Pasien</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">Kelas</th>
                        <th class="px-4 py-2 border">Pembayaran</th>
                        <th class="px-4 py-2 border">DJIP</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">2024-06-25</td>
                        <td class="px-4 py-2 border">RM123456</td>
                        <td class="px-4 py-2 border">Budi Santoso</td>
                        <td class="px-4 py-2 border">1990-01-01</td>
                        <td class="px-4 py-2 border">Laki-laki</td>
                        <td class="px-4 py-2 border">Rawat Inap</td>
                        <td class="px-4 py-2 border">Ruang Bedah</td>
                        <td class="px-4 py-2 border">Kelas I</td>
                        <td class="px-4 py-2 border">BPJS</td>
                        <td class="px-4 py-2 border">DJIP001</td>
                        <td class="px-4 py-2 border flex gap-2">
                            <a href="#" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
