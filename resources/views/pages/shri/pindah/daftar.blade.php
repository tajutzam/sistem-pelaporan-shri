@extends('layouts.app')

@section('content')
    <div x-data="{
                open: false,
                ruangans : [],
                selectedPatient: {
                    id: '',
                    nama_ruangan_tujuan:'',
                    no_rekam_medis: '',
                    nama_pasien: '',
                    jenis_kelamin: '',
                    tanggal_masuk: '',
                    nama_ruangan: '',
                    kelas_ruangan: '',
                    tanggal_pindah: '',
                    lama_dirawat: ''
                },
                calculateLamaDirawat() {
                    const masuk = new Date(this.selectedPatient.tanggal_masuk)
                    const pindah = new Date(this.selectedPatient.tanggal_pindah)

                    if (!this.selectedPatient.tanggal_masuk || !this.selectedPatient.tanggal_pindah) {
                        this.selectedPatient.lama_dirawat = ''
                        return
                    }

                    const masukDate = masuk.getDate()
                    const pindahDate = pindah.getDate()
                    const masukMonth = masuk.getMonth()
                    const pindahMonth = pindah.getMonth()
                    const masukYear = masuk.getFullYear()
                    const pindahYear = pindah.getFullYear()

                    // Jika tahun atau bulan sama
                    if (masukYear === pindahYear && masukMonth === pindahMonth) {
                        this.selectedPatient.lama_dirawat = pindahDate - masukDate
                    } else {
                        const lastDayOfMasukMonth = new Date(masukYear, masukMonth + 1, 0).getDate()
                        const sisaHariMasuk = lastDayOfMasukMonth - masukDate
                        this.selectedPatient.lama_dirawat = sisaHariMasuk + pindahDate
                    }
                },
                async fetchRuanganByKelas() {
                    this.selectedPatient.nama_ruangan_tujuan = ''
                    if (!this.selectedPatient.kelas_tujuan) {
                        this.ruangans = []
                        return
                    }

                    const response = await fetch(`/api/get-ruangan-by-kelas/${this.selectedPatient.kelas_tujuan}`)
                    this.ruangans = await response.json()
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
        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
            <form action="{{ route('register-shri.pindah.store') }}" method="post">
                @csrf
                <div class="bg-gray-100 p-6 rounded-lg w-full max-w-4xl relative">
                    <h2 class="text-lg font-semibold underline mb-4">Formulir Pendaftaran Pasien Pindah</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Hidden input for ID -->
                        <input type="hidden" name="id" :value="selectedPatient.id">

                        <div>
                            <label>No. Rekam Medis</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" name="no_rekam_medis"
                                x-model="selectedPatient.no_rekam_medis" disabled />
                        </div>

                        <input type="hidden" name="nama_ruangan_tujuan" :value="selectedPatient.nama_ruangan_tujuan">

                        <div>
                            <label>Tanggal Pindah</label>
                            <input type="date" name="tanggal_pindah" class="w-full p-2 rounded border"
                                x-model="selectedPatient.tanggal_pindah" @change="calculateLamaDirawat" />
                        </div>
                        <div>
                            <label>Nama Pasien</label>
                            <input type="text" name="nama_pasien" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedPatient.nama_pasien" disabled />
                        </div>
                        <div>
                            <label>Ruangan Tujuan</label>
                            <select class="w-full p-2 rounded border" x-model="selectedPatient.nama_ruangan_tujuan">
                                <option value="">Pilih salah satu</option>
                                <template x-for="ruangan in ruangans" :key="ruangan.id">
                                    <option :value="ruangan.nama_ruangan" x-text="ruangan.nama_ruangan"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label>Jenis Kelamin</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedPatient.jenis_kelamin" disabled />
                        </div>
                        <div>
                            <label>Kelas Tujuan</label>
                            <select class="w-full p-2 rounded border" x-model="selectedPatient.kelas_tujuan"
                                @change="fetchRuanganByKelas">
                                <option value="">Pilih salah satu</option>
                                @foreach (config('ruangan.kelas_ruangan') as $item)
                                    <option>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Tanggal Masuk</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedPatient.tanggal_masuk" disabled />
                        </div>
                        <div>
                            <label>Lama Dirawat</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" name="lama_dirawat"
                                x-model="selectedPatient.lama_dirawat" readonly />
                        </div>
                        <div class="col-span-2">
                            <label>Ruangan</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedPatient.nama_ruangan"
                                disabled />
                        </div>
                    </div>
                    <div class="flex justify-end gap-4 mt-6">
                        <button @click="open = false" type="button"
                            class="bg-gray-600 text-white px-4 py-2 rounded">Tutup</button>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
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
                            <td class="px-4 py-2 border">{{ $item->kelasPerawatan->nama_ruangan }}</td>
                            <td class="px-4 py-2 border">{{ $item->kelasPerawatan->kelas_ruangan }}</td>
                            <td class="px-4 py-2 border">
                                <button @click="selectedPatient = {
                                                id: '{{ $item->id }}',
                                                no_rekam_medis: '{{ $item->pasien->no_rekam_medis }}',
                                                nama_pasien: '{{ $item->pasien->nama_pasien }}',
                                                jenis_kelamin: '{{ $item->pasien->jenis_kelamin }}',
                                                tanggal_masuk: '{{ $item->tanggal_masuk }}',
                                                nama_ruangan: '{{ $item->kelasPerawatan->nama_ruangan }}',
                                                kelas_ruangan: '{{ $item->kelasPerawatan->kelas_ruangan }}',
                                                tanggal_pindah: '',
                                                lama_dirawat: ''
                                            }; open = true" class="flex items-center gap-2 text-blue-600 hover:underline">
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
