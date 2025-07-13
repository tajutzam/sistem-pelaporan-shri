@extends('layouts.app')

@section('content')

    <h2 class="font-bold text-2xl mb-4 underline">Pasien Pindah</h2>

    <div x-data="{ open: false }">
        <!-- Header Section -->
        <div class="flex justify-between mt-3 bg-[#34495E] p-4 rounded-lg text-white">
            <h2>Daftar Pasien Pindah</h2>
            <a href="{{ route('register-shri.pindah.daftar') }} " flex items-center gap-3">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah</span>
            </a>
        </div>

        <!-- Modal -->
        <!-- Search Bar -->
        <!-- Search Form -->
        <div class="flex justify-end mt-4">
            <form method="GET" action="{{ route('register-shri.pindah.view') }}"
                class="relative w-full max-w-xs bg-[#E7E9D4]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pasien..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
            </form>
        </div>

        <!-- Tabel Pasien Pindah -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Tanggal Masuk</th>
                        <th class="px-4 py-2 border">Ruang Awal</th>
                        <th class="px-4 py-2 border">Tanggal Pindah</th>
                        <th class="px-4 py-2 border">Ruang Tujuan</th>
                        <th class="px-4 py-2 border">Kelas Tujuan</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($pindahs as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->no_rekam_medis ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->nama_pasien ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->jenis_kelamin ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->tanggal_masuk ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->ruang_perawatan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->tanggal_pindah ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->kelas->nama_ruangan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->kelas->kelas_ruangan ?? '-' }}</td>
                            <td class="px-4 py-2 border flex gap-2">
                                <a href="{{ route('register-shri.pindah.edit', ['id' => $item->id]) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('register-shri.pindah.destroy', ['id' => $item->id]) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button class="text-red-600 hover:text-red-800" type="submit">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
</div>@endsection
