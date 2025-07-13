@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-4 underline">Data Ruangan</h2>

    <div x-data="{ open: false, editOpen: false, editRuangan: {} }">
        {{-- Header --}}
        <div class="flex justify-between px-4 py-2 bg-[#34495E] rounded-lg text-white">
            <h2>Daftar Ruangan</h2>
            <button @click="open = true" class="flex gap-3 items-center cursor-pointer">
                <i class="fa-solid fa-plus"></i> <span>Tambah</span>
            </button>
        </div>

        {{-- Search --}}
        <div class="flex justify-end mt-4">
            <form method="GET" action="{{ route('ruangan.index') }}" class="w-full max-w-xs">
                <div class="relative bg-[#E7E9D4] rounded">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full border text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Nama Ruangan</th>
                        <th class="px-4 py-2 border">Kelas Ruangan</th>
                        <th class="px-4 py-2 border">Jumlah Tempat Tidur</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($ruangans as $ruangan)
                        <tr>
                            <td class="border px-4 py-2">{{ $ruangans->firstItem() + $loop->index }}</td>
                            <td class="border px-4 py-2">{{ $ruangan->nama_ruangan }}</td>
                            <td class="border px-4 py-2">{{ $ruangan->kelas_ruangan }}</td>
                            <td class="border px-4 py-2">{{ $ruangan->jumlah_tempat_tidur }}</td>
                            <td class="border px-4 py-2">{{ $ruangan->status }}</td>
                            <td class="border px-4 py-2 space-x-2">
                                <button @click="editRuangan = {{ json_encode($ruangan) }}; editOpen = true"
                                    class="text-blue-600 hover:underline">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $ruangans->links() }}
            </div>
        </div>

        {{-- Modal Tambah --}}
        <div x-show="open" @click.outside="open = false" x-transition
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">Tambah Ruangan</h2>
                <form action="{{ route('ruangan.store') }}" method="POST">
                    @csrf
                    <label class="block text-sm font-medium mb-1">Nama Ruangan</label>
                    <input type="text" name="nama_ruangan" class="w-full border rounded px-3 py-2 mb-2" required>

                    <label class="block text-sm font-medium mb-1">Kelas Ruangan</label>
                    <select name="kelas_ruangan" class="w-full border rounded px-3 py-2 mb-2" " required>
                                <option value="">Pilih Kelas</option>
                                @foreach (config('ruangan.kelas_ruangan') as $kelas)
                                    <option value=" {{ $kelas }}">{{ $kelas }}</option>
                                @endforeach
                    </select>

                    <label class="block text-sm font-medium mb-1">Jumlah Tempat Tidur</label>
                    <input type="number" name="jumlah_tempat_tidur" class="w-full border rounded px-3 py-2 mb-2" min="1"
                        required>

                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2 mb-2" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Penuh">Penuh</option>
                        <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                    </select>

                    <div class="flex justify-end mt-4">
                        <button type="submit"
                            class="bg-[#34495E] text-white px-4 py-2 rounded hover:bg-[#2c3e50]">Simpan</button>
                        <button type="button" @click="open = false"
                            class="ml-2 text-gray-600 hover:underline">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit --}}
        <div x-show="editOpen" @click.outside="editOpen = false" x-transition
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">Edit Ruangan</h2>
                <form :action="'/data/ruangan/' + editRuangan.id" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" :value="editRuangan.id">

                    <label class="block text-sm font-medium mb-1">Nama Ruangan</label>
                    <input type="text" name="nama_ruangan" class="w-full border rounded px-3 py-2 mb-2"
                        x-model="editRuangan.nama_ruangan" required>

                    <label class="block text-sm font-medium mb-1">Kelas Ruangan</label>
                    <select name="kelas_ruangan" class="w-full border rounded px-3 py-2 mb-2"
                        x-model="editRuangan.kelas_ruangan" required>
                        <option value="">Pilih Kelas</option>
                        @foreach (config('ruangan.kelas_ruangan') as $kelas)
                            <option value="{{ $kelas }}">{{ $kelas }}</option>
                        @endforeach
                    </select>

                    <label class="block text-sm font-medium mb-1">Jumlah Tempat Tidur</label>
                    <input type="number" name="jumlah_tempat_tidur" min="1" class="w-full border rounded px-3 py-2 mb-2"
                        x-model="editRuangan.jumlah_tempat_tidur" required>

                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2 mb-2" x-model="editRuangan.status"
                        required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Penuh">Penuh</option>
                        <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                    </select>

                    <div class="flex justify-end mt-4">
                        <button type="submit"
                            class="bg-[#34495E] text-white px-4 py-2 rounded hover:bg-[#2c3e50]">Perbarui</button>
                        <button type="button" @click="editOpen = false"
                            class="ml-2 text-gray-600 hover:underline">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
