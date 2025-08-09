@extends('layouts.app')

@section('content')

    <div x-data="{
                open: false,
                selectedItem: {
                    no_rekam_medis: '',
                    shri_id: '',
                    nama_pasien: '',
                    jenis_kelamin: '',
                    tanggal_masuk: '',
                    tanggal_keluar: '',
                    ruangan: '',
                    lama_dirawat: ''
                },
                calculateLamaDirawat() {
                    const masuk = new Date(this.selectedItem.tanggal_masuk);
                    const keluar = new Date(this.selectedItem.tanggal_keluar);

                    if (!this.selectedItem.tanggal_masuk || !this.selectedItem.tanggal_keluar) {
                        this.selectedItem.lama_dirawat = '';
                        return;
                    }

                    const diff = Math.floor((keluar - masuk) / (1000 * 60 * 60 * 24));
                    this.selectedItem.lama_dirawat = `${diff} hari`;
                },
                closeModal() {
                    this.open = false;
                    // Reset form data when closing
                    this.selectedItem = {
                        no_rekam_medis: '',
                        shri_id: '',
                        nama_pasien: '',
                        jenis_kelamin: '',
                        tanggal_masuk: '',
                        tanggal_keluar: '',
                        ruangan: '',
                        lama_dirawat: ''
                    };
                }
            }">

        <!-- Header -->
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
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             x-cloak
             style="display: none;">
            <div x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-75"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-75"
                 class="bg-gray-100 p-6 rounded-lg w-full max-w-4xl relative"
                 @click.outside="closeModal()">

                <form action="{{ route('register-shri.keluar.store') }}" method="POST">
                    @csrf
                    <h2 class="text-lg font-semibold underline mb-4">Formulir Pendaftaran Pasien Keluar</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekam Medis</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.no_rekam_medis"
                                disabled />
                        </div>
                        <input type="hidden" name="shri_id" :value="selectedItem.shri_id">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Keluar</label>
                            <input name="tanggal_keluar" type="date" class="w-full p-2 rounded border"
                                x-model="selectedItem.tanggal_keluar" @change="calculateLamaDirawat" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.nama_pasien"
                                disabled />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DPJP</label>
                            <select name="dpjp_id" class="w-full p-2 rounded border">
                                <option value="">Pilih salah satu</option>
                                @foreach ($dpjps as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.jenis_kelamin"
                                disabled />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosa Akhir</label>
                            <select name="diagnosa_id" class="w-full p-2 rounded border">
                                <option value="">Pilih salah satu</option>
                                @foreach ($diagnosas as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode_icd }} - {{ $item->diagnosa }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.tanggal_masuk"
                                disabled />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cara Keluar</label>
                            <select name="cara_keluar" class="w-full p-2 rounded border">
                                <option value="">Pilih salah satu</option>
                                @foreach (config('data.cara_keluar') as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.ruangan"
                                disabled />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lama Dirawat</label>
                            <input type="text" name="lama_dirawat" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.lama_dirawat" readonly />
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button @click="closeModal()" type="button"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">Tutup</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
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
                    @foreach ($dirawats as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $item->pasien->no_rekam_medis }}</td>
                            <td class="px-4 py-2 border">{{ $item->pasien->nama_pasien }}</td>
                            <td class="px-4 py-2 border">{{ $item->pasien->jenis_kelamin }}</td>
                            <td class="px-4 py-2 border">{{ $item->tanggal_masuk }}</td>
                            <td class="px-4 py-2 border">
                                {{ $item->has('pindah') ? $item->pindah->kelas->nama_ruangan : $item->kelasPerawatan->nama_ruangan }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item->has('pindah') ? $item->pindah->kelas->kelas_ruangan : $item->kelasPerawatan->kelas_ruangan }}
                            </td>
                            <td class="px-4 py-2 border">
                                <button @click="open = true; selectedItem = {
                                                        shri_id: '{{ $item->id }}',
                                                        no_rekam_medis: '{{ $item->pasien->no_rekam_medis }}',
                                                        nama_pasien: '{{ $item->pasien->nama_pasien }}',
                                                        jenis_kelamin: '{{ $item->pasien->jenis_kelamin }}',
                                                        tanggal_masuk: '{{ $item->tanggal_masuk }}',
                                                        tanggal_keluar: '',
                                                        ruangan: '{{ $item->has('pindah') ? $item->pindah->kelas->nama_ruangan : $item->kelasPerawatan->nama_ruangan }}',
                                                        lama_dirawat: ''
                                                    }"
                                        class="flex items-center gap-2 text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                                    <i class="fa-solid fa-plus"></i> Tambah
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
