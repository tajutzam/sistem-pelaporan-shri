@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-4 underline">Hak Akses Perawat</h2>

    <div x-data="{ open: false, editOpen: false, editPerawatRuangan: {} }">
        {{-- Header --}}
        <div class="flex justify-between px-4 py-2 bg-[#34495E] rounded-lg text-white">
            <h2>Daftar Hak Akses Perawat</h2>
            <button @click="open = true" class="flex gap-3 items-center cursor-pointer">
                <i class="fa-solid fa-plus"></i> <span>Tambah</span>
            </button>
        </div>

        {{-- Search --}}
        <div class="flex justify-end mt-4">
            <form method="GET" action="{{ route('hak-akses-perawat.index') }}" class="w-full max-w-xs">
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
                        <th class="px-4 py-2 border">Nama Perawat</th>
                        <th class="px-4 py-2 border">Username</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">Kelas Ruangan</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($perawatRuangans as $perawatRuangan)
                        <tr>
                            <td class="border px-4 py-2">{{ $perawatRuangans->firstItem() + $loop->index }}</td>
                            <td class="border px-4 py-2">{{ $perawatRuangan->user->name }}</td>
                            <td class="border px-4 py-2">{{ $perawatRuangan->user->username }}</td>
                            <td class="border px-4 py-2">{{ $perawatRuangan->ruangan->nama_ruangan }}</td>
                            <td class="border px-4 py-2">{{ $perawatRuangan->ruangan->kelas->name }}</td>
                            <td class="border px-4 py-2 space-x-2">
                                <button @click="editPerawatRuangan = {{ json_encode($perawatRuangan) }}; editOpen = true"
                                    class="text-blue-600 hover:underline">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <form action="{{ route('hak-akses-perawat.destroy', $perawatRuangan->id) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Yakin hapus hak akses ini?')">
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
                {{ $perawatRuangans->links() }}
            </div>
        </div>

        {{-- Modal Tambah --}}

        <div x-show="open" @click.outside="open = false" x-transition
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">Tambah Hak Akses Perawat</h2>
                <form action="{{ route('hak-akses-perawat.store') }}" method="POST">
                    @csrf
                    <label class="block text-sm font-medium mb-1">Perawat</label>
                    <select name="user_id" class="w-full border rounded px-3 py-2 mb-2" required>
                        <option value="">Pilih Perawat</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->username }}</option>
                        @endforeach
                    </select>

                    <label class="block text-sm font-medium mb-1">Ruangan</label>
                    <select name="nama_ruangan" class="w-full border rounded px-3 py-2 mb-2" required>
                        <option value="">Pilih Ruangan</option>
                        @foreach ($ruangans as $ruangan)
                            <option value="{{ $ruangan->nama_ruangan }}">{{ $ruangan->nama_ruangan }}</option>
                        @endforeach
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
                <h2 class="text-lg font-semibold mb-4">Edit Hak Akses Perawat</h2>
                <form :action="'/data/hak-akses-perawat/' + editPerawatRuangan.id" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" :value="editPerawatRuangan.id">

                    <label class="block text-sm font-medium mb-1">Perawat</label>
                    <select name="user_id" class="w-full border rounded px-3 py-2 mb-2" x-model="editPerawatRuangan.user_id"
                        required>
                        <option value="">Pilih Perawat</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->username }}</option>
                        @endforeach
                    </select>

                    <label class="block text-sm font-medium mb-1">Ruangan</label>
                    <select name="nama_ruangan" class="w-full border rounded px-3 py-2 mb-2"
                        x-model="editPerawatRuangan.ruangan.nama_ruangan" required>
                        <option value="">Pilih Ruangan</option>
                        @foreach ($ruangans as $ruangan)
                            <option value="{{ $ruangan->nama_ruangan }}">{{ $ruangan->nama_ruangan }}</option>
                        @endforeach
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
