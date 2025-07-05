@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-4 underline">Data Pengguna</h2>

    <div x-data="{ open: false, editOpen: false, editUser: {} }">
        {{-- Header --}}
        <div class="flex justify-between px-4 py-2 bg-[#34495E] rounded-lg text-white">
            <h2>Daftar Pengguna</h2>
            <button @click="open = true" class="flex gap-3 items-center cursor-pointer">
                <i class="fa-solid fa-plus"></i> <span>Tambah</span>
            </button>
        </div>

        {{-- Search --}}
        <div class="flex justify-end mt-4">
            <form method="GET" action="{{ route('pengguna.index') }}" class="w-full max-w-xs">
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
                        <th class="px-4 py-2 border">Nama</th>
                        <th class="px-4 py-2 border">Tanggal Lahir</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Username</th>
                        <th class="px-4 py-2 border">Hak Akses</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($users as $user)
                        <tr>
                            <td class="border px-4 py-2">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="border px-4 py-2">{{ $user->name }}</td>
                            <td class="border px-4 py-2">{{ $user->tanggal_lahir }}</td>
                            <td class="border px-4 py-2">{{ $user->jenis_kelamin }}</td>
                            <td class="border px-4 py-2">{{ $user->username }}</td>
                            <td class="border px-4 py-2">{{ ucfirst($user->hak_akses) }}</td>
                            <td class="border px-4 py-2 space-x-2">
                                <button @click="editUser = {{ json_encode($user) }}; editOpen = true"
                                    class="text-blue-600 hover:underline">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" class="inline"
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
                {{ $users->links() }}
            </div>
        </div>

        {{-- Modal Tambah --}}
        <div x-show="open" @click.outside="open = false" x-transition
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">Tambah Pengguna</h2>
                <form action="{{ route('pengguna.store') }}" method="POST">
                    @csrf
                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2 mb-2" required>

                    <label class="block text-sm font-medium mb-1">Username</label>
                    <input type="text" name="username" class="w-full border rounded px-3 py-2 mb-2" required>

                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" class="w-full border rounded px-3 py-2 mb-2" required>

                    <label class="block text-sm font-medium mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="w-full border rounded px-3 py-2 mb-2" required>

                    <label class="block text-sm font-medium mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full border rounded px-3 py-2 mb-2">
                        <option value="Laki-Laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>

                    <label class="block text-sm font-medium mb-1">Hak Akses</label>
                    <select name="hak_akses" class="w-full border rounded px-3 py-2 mb-2">
                        <option value="perawat">Perawat</option>
                        <option value="pelaporan">Pelaporan</option>
                        <option value="kepala">Kepala</option>
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
                <h2 class="text-lg font-semibold mb-4">Edit Pengguna</h2>
                <form :action="'/data/pengguna/' + editUser.id" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" :value="editUser.id">

                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2 mb-2" x-model="editUser.name"
                        required>

                    <label class="block text-sm font-medium mb-1">Username</label>
                    <input type="text" name="username" class="w-full border rounded px-3 py-2 mb-2"
                        x-model="editUser.username" required>

                    <label class="block text-sm font-medium mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="w-full border rounded px-3 py-2 mb-2"
                        x-model="editUser.tanggal_lahir" required>

                    <label class="block text-sm font-medium mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full border rounded px-3 py-2 mb-2"
                        x-model="editUser.jenis_kelamin">
                        <option value="Laki-Laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>

                    <label class="block text-sm font-medium mb-1">Hak Akses</label>
                    <select name="hak_akses" class="w-full border rounded px-3 py-2 mb-2" x-model="editUser.hak_akses">
                        <option value="perawat">Perawat</option>
                        <option value="pelaporan">Pelaporan</option>
                        <option value="kepala">Kepala</option>
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
