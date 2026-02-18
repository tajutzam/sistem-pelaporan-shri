@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-4 underline">Hak Akses Perawat</h2>

    <div x-data="{ 
                        open: false, 
                        editOpen: false, 
                        editPerawatRuangan: { id: '', user_name: '', nama_ruangan: '' } 
                    }">
        {{-- Header --}}
        <div class="flex justify-between px-4 py-2 bg-[#34495E] rounded-lg text-white">
            <h2 class="font-semibold">Daftar Hak Akses Perawat</h2>
            <button @click="open = true" class="flex gap-3 items-center cursor-pointer hover:text-gray-300 transition">
                <i class="fa-solid fa-plus"></i> <span>Tambah</span>
            </button>
        </div>

        {{-- Search --}}
        <div class="flex justify-end mt-4">
            <form method="GET" action="{{ route('hak-akses-perawat.index') }}" class="w-full max-w-xs">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" placeholder="Cari nama atau ruangan..." value="{{ request('search') }}"
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 w-full text-sm" />
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="mt-4 overflow-x-auto shadow-sm border rounded-lg">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 border">No</th>
                        <th class="px-4 py-3 border">Nama Perawat</th>
                        <th class="px-4 py-3 border">Username</th>
                        <th class="px-4 py-3 border">Ruangan</th>
                        <th class="px-4 py-3 border">Daftar Kelas</th>
                        <th class="px-4 py-3 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse ($perawatRuangans as $userId => $items)
                        @php
                            $firstItem = $items->first();
                            // Menggabungkan kelas unik dalam satu string
                            $daftarKelas = $items->pluck('ruangan.kelas.name')->unique()->filter()->implode(', ');
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="border px-4 py-2 font-medium text-gray-900">{{ $firstItem->user->name }}</td>
                            <td class="border px-4 py-2 text-gray-600">{{ $firstItem->user->username }}</td>
                            <td class="border px-4 py-2">{{ $firstItem->ruangan->nama_ruangan }}</td>
                            <td class="border px-4 py-2">
                                <span class="text-xs px-2 py-1 bg-gray-100 rounded text-gray-700">
                                    {{ $daftarKelas ?: '-' }}
                                </span>
                            </td>
                            <td class="border px-4 py-2 text-center space-x-2">
                                {{-- Tombol Edit (Abu-abu) --}}
                                <button @click="editPerawatRuangan = { 
                                                                        id: '{{ $firstItem->id }}', 
                                                                        user_name: '{{ $firstItem->user->name }}', 
                                                                        nama_ruangan: '{{ $firstItem->ruangan->nama_ruangan }}' 
                                                                    }; editOpen = true"
                                    class="inline-flex items-center px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-xs transition">
                                    <i class="fa-solid fa-edit mr-1"></i> Edit
                                </button>

                                {{-- Form Delete --}}
                                <form action="{{ route('hak-akses-perawat.destroy', $firstItem->id) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Hapus semua hak akses untuk perawat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-2 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 text-xs transition">
                                        <i class="fa-solid fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Modal Tambah --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
            <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl" @click.outside="open = false">
                <h2 class="text-lg font-bold mb-4 border-b pb-2">Tambah Hak Akses Perawat</h2>
                <form action="{{ route('hak-akses-perawat.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Pilih Perawat</label>
                        <select name="user_id"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-400 outline-none"
                            required>
                            <option value="">-- Pilih Perawat --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1">*Hanya perawat yang belum memiliki akses yang muncul.</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Pilih Ruangan</label>
                        <select name="nama_ruangan"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-400 outline-none"
                            required>
                            @if ($isKepala)
                                <option value="">-- Pilih Ruangan --</option>
                            @endif
                            @foreach ($ruangans as $ruangan)
                                <option value="{{ $ruangan->nama_ruangan }}">{{ $ruangan->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="open = false"
                            class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                        <button type="submit"
                            class="bg-gray-600 text-white px-5 py-2 rounded-lg hover:bg-gray-700 transition font-medium">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit --}}
        <div x-show="editOpen" x-transition:enter="transition ease-out duration-300"
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
            <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl" @click.outside="editOpen = false">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <h2 class="text-lg font-bold">Ubah Ruangan Perawat</h2>
                </div>

                <form :action="'{{ url('/data/hak-akses-perawat') }}/' + editPerawatRuangan.id" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1 text-gray-500">Nama Perawat</label>
                        <input type="text"
                            class="w-full border rounded-lg px-3 py-2 bg-gray-50 text-gray-500 cursor-not-allowed italic"
                            :value="editPerawatRuangan.user_name" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Ruangan Baru</label>
                        <select name="nama_ruangan"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-400 outline-none"
                            x-model="editPerawatRuangan.nama_ruangan" required>
                            @if ($isKepala)
                                <option value="">-- Pilih Ruangan --</option>
                            @endif
                            @foreach ($ruangans as $ruangan)
                                <option value="{{ $ruangan->nama_ruangan }}">{{ $ruangan->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="editOpen = false"
                            class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                        <button type="submit"
                            class="bg-gray-600 text-white px-5 py-2 rounded-lg hover:bg-gray-700 transition font-medium">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection