@extends('layouts.app')

@section('content')

    <h2 class="font-bold text-2xl mb-4 underline">Pasien Keluar</h2>

    <div x-data="{ open: false }">

        <!-- Header -->
        <div class="flex justify-between mt-3 bg-[#34495E] p-4 rounded-lg text-white">
            <h2>Daftar Pasien Keluar</h2>
            <button @click="open = true" class="flex items-center gap-3">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah</span>
            </button>
        </div>

        <!-- Modal -->
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
            <div class="bg-gray-100 p-6 rounded-lg w-full max-w-4xl"
                x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="scale-95"
                x-transition:enter-end="scale-100" x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="scale-100" x-transition:leave-end="scale-95">
                <h2 class="text-lg font-semibold underline mb-4">Formulir Pendaftaran Pasien Keluar</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>No. Rekam Medis</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>Tanggal Keluar</label>
                        <input type="date" class="w-full p-2 rounded border" />
                    </div>
                    <div>
                        <label>Nama Pasien</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>DPJP</label>
                        <select class="w-full p-2 rounded border">
                            <option>Pilih salah satu</option>
                            <option>DPJP001</option>
                        </select>
                    </div>
                    <div>
                        <label>Jenis Kelamin</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>Diagnosa Akhir</label>
                        <select class="w-full p-2 rounded border">
                            <option>Pilih salah satu</option>
                            <option>Diagnosa A</option>
                        </select>
                    </div>
                    <div>
                        <label>Tanggal Masuk</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>Cara Keluar</label>
                        <select class="w-full p-2 rounded border">
                            <option>Pilih salah satu</option>
                            <option>Rawat Jalan</option>
                            <option>Rujuk</option>
                            <option>Pulang</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label>Ruangan</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                    <div>
                        <label>Lama Dirawat</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" disabled />
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button @click="open = false" class="bg-gray-600 text-white px-4 py-2 rounded">Tutup</button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </div>
        </div>

        <!-- Tabel Pasien Keluar -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Tanggal Keluar</th>
                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">Diagnosa Akhir</th>
                        <th class="px-4 py-2 border">Cara Keluar</th>
                        <th class="px-4 py-2 border">Lama Dirawat</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">2024-06-26</td>
                        <td class="px-4 py-2 border">RM123456</td>
                        <td class="px-4 py-2 border">Budi Santoso</td>
                        <td class="px-4 py-2 border">Laki-laki</td>
                        <td class="px-4 py-2 border">Ruang Bedah</td>
                        <td class="px-4 py-2 border">Diagnosa A</td>
                        <td class="px-4 py-2 border">Pulang</td>
                        <td class="px-4 py-2 border">5 hari</td>
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
