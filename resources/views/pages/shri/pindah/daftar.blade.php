@extends('layouts.app')

@section('content')


    <div x-data="{ open: false }">
        <div class="flex justify-between items-center mt-3 bg-[#34495E] p-4 rounded-lg text-white">
            <h2>Daftar Pasien Dirawat</h2>
            <div class="flex justify-end">
                <div class="relative w-full max-w-xs bg-[#E7E9D4] rounded-lg text-gray-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" placeholder="Search..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-8"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.50)"
            x-cloak>
            <div class="bg-gray-100 p-6 rounded-lg w-full max-w-4xl relative">
                <h2 class="text-lg font-semibold underline mb-4">Formulir Pendaftaran Pasien Pindah</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>No. Rekam Medis</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>Tanggal Pindah</label>
                        <input type="date" class="w-full p-2 rounded border" />
                    </div>
                    <div>
                        <label>Nama Pasien</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>Ruangan Tujuan</label>
                        <select class="w-full p-2 rounded border">
                            <option>Pilih salah satu</option>
                            <option>Ruang NICU</option>
                        </select>
                    </div>
                    <div>
                        <label>Jenis Kelamin</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>Kelas Tujuan</label>
                        <select class="w-full p-2 rounded border">
                            <option>Pilih salah satu</option>
                            <option>Kelas I</option>
                            <option>Kelas II</option>
                        </select>
                    </div>
                    <div>
                        <label>Tanggal Masuk</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>Lama Dirawat</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div class="col-span-2">
                        <label>Ruangan</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-6">
                    <button @click="open = false" class="bg-gray-600 text-white px-4 py-2 rounded">Tutup</button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </div>
        </div>

        <!-- Search Bar -->


        <!-- Tabel Pasien Pindah -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Tanggal Masuk</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">Kelas</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">RM654321</td>
                        <td class="px-4 py-2 border">Siti Aminah</td>
                        <td class="px-4 py-2 border">Perempuan</td>
                        <td class="px-4 py-2 border">2024-06-20</td>
                        <td class="px-4 py-2 border">Ruang Anak</td>
                        <td class="px-4 py-2 border">Kelas II</td>
                        <td class="px-4 py-2 border flex gap-2">
                            <button @click="open = true" class="flex items-center gap-3">
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
