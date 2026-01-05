@extends('layouts.app')

@section('content')
    <div x-data="dataPindah()">

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
                        <input type="hidden" name="id" :value="selectedPatient.id">

                        <div>
                            <label>No. Rekam Medis</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" name="no_rekam_medis"
                                x-model="selectedPatient.no_rekam_medis" disabled />
                        </div>

                        <div>
                            <label>Tanggal Pindah</label>
                            @php
                                $tanggalSensus = request('tanggal_sensus');
                                $tanggalPindah = \Carbon\Carbon::parse($tanggalSensus)->subDay()->format('Y-m-d');
                            @endphp
                            <input type="date" name="tanggal_pindah" class="w-full p-2 rounded border"
                                x-model="selectedPatient.tanggal_pindah" @change="calculateLamaDirawat" />

                        </div>

                        <div>
                            <label>Nama Pasien</label>
                            <input type="text" name="nama_pasien" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedPatient.nama_pasien" disabled />
                        </div>

                        <div>
                            <label>Kelas Tujuan</label>
                            <select class="w-full p-2 rounded border" x-model="selectedPatient.kelas_tujuan"
                                @change="updateRuanganByKelas">
                                <option value="">Pilih salah satu</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->name }}">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>Jenis Kelamin</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedPatient.jenis_kelamin" disabled />
                        </div>

                        <div>
                            <label>Ruangan Tujuan</label>
                            <select class="w-full p-2 rounded border" x-model="selectedPatient.nama_ruangan_tujuan"
                                name="nama_ruangan_tujuan">
                                <option value="">Pilih salah satu</option>
                                <template x-for="ruangan in ruangans" :key="ruangan.id">
                                    <option :value="ruangan.nama_ruangan" x-text="ruangan.nama_ruangan"></option>
                                </template>
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
                            <label>Ruangan Asal</label>
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
                    @forelse ($dirawats as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $item->pasien->no_rekam_medis }}</td>
                            <td class="px-4 py-2 border">{{ $item->pasien->nama_pasien }}</td>
                            <td class="px-4 py-2 border">{{ $item->pasien->jenis_kelamin }}</td>
                            <td class="px-4 py-2 border">{{ $item->tanggal_masuk }}</td>
                            <td class="px-4 py-2 border">{{ $item->ruangan->nama_ruangan }}</td>
                            <td class="px-4 py-2 border">{{ $item->ruangan->kelas->name }}</td>
                            <td class="px-4 py-2 border">
                                <button @click="
                                                                                                    selectedPatient.id = '{{ $item->id }}';
                                                                                                    selectedPatient.no_rekam_medis = '{{ $item->pasien->no_rekam_medis }}';
                                                                                                    selectedPatient.nama_pasien = '{{ $item->pasien->nama_pasien }}';
                                                                                                    selectedPatient.jenis_kelamin = '{{ $item->pasien->jenis_kelamin }}';
                                                                                                    selectedPatient.tanggal_masuk = '{{ $item->tanggal_masuk }}';
                                                                                                    selectedPatient.nama_ruangan = '{{ $item->ruangan->nama_ruangan }}';
                                                                                                    selectedPatient.kelas_ruangan = '{{ $item->ruangan->kelas->name }}';
                                                                                                    selectedPatient.tanggal_pindah = '';
                                                                                                    selectedPatient.lama_dirawat = '';
                                                                                                    selectedPatient.kelas_tujuan = '';
                                                                                                    selectedPatient.nama_ruangan_tujuan = '';
                                                                                                    calculateLamaDirawat();

                                                                                                    open = true;

                                                                                                    setTimeout(() => { setTanggalPindahRange() }, 20);
                                                                                                "
                                    class="flex items-center gap-2 text-blue-600 hover:underline">
                                    <i class="fa-solid fa-plus"></i> Tambah
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-2 border text-center text-gray-500">
                                Tidak ada pasien dirawat hari ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function dataPindah() {
            return {
                open: false,
                ruangans: [],
                allKelas: @json($kelas),

                selectedPatient: {
                    id: '',
                    no_rekam_medis: '',
                    nama_pasien: '',
                    jenis_kelamin: '',
                    tanggal_masuk: '',
                    nama_ruangan: '',
                    kelas_ruangan: '',
                    tanggal_pindah: '',
                    lama_dirawat: '',
                    kelas_tujuan: '',
                    nama_ruangan_tujuan: ''
                },

                getJakartaDate(dateString = null) {
                    if (dateString) {
                        dateString = dateString + "T00:00:00";
                    }

                    let d = dateString ? new Date(dateString) : new Date();

                    return new Date(
                        d.toLocaleString("en-US", { timeZone: "Asia/Jakarta" })
                    );
                },


                formatDate(d) {
                    let y = d.getFullYear();
                    let m = String(d.getMonth() + 1).padStart(2, "0");
                    let day = String(d.getDate()).padStart(2, "0");
                    return `${y}-${m}-${day}`;
                },

                setTanggalPindahRange() {
                    if (!this.selectedPatient.tanggal_masuk) return;

                    let masuk = this.getJakartaDate(this.selectedPatient.tanggal_masuk);
                    let today = this.getJakartaDate();

                    this.minDate = this.formatDate(masuk);
                    this.maxDate = this.formatDate(today);

                    let input = document.querySelector("input[name='tanggal_pindah']");

                    if (input) {
                        input.max = this.maxDate;

                        const params = new URLSearchParams(window.location.search);
                        const tanggalSensus = params.get("tanggal_sensus");

                        if (tanggalSensus) {
                            let sensusDate = this.getJakartaDate(tanggalSensus);

                            this.selectedPatient.tanggal_pindah = this.formatDate(sensusDate);
                        }
                    }
                },


                calculateLamaDirawat() {
                    // 1. Ambil tanggal pindah dari model
                    let tanggalPindah = this.selectedPatient.tanggal_pindah;

                    // 2. LOGIC TAMBAHAN: Jika model kosong, coba ambil dari URL tanggal_sensus
                    if (!tanggalPindah) {
                        const params = new URLSearchParams(window.location.search);
                        tanggalPindah = params.get("tanggal_sensus");

                        // Update modelnya juga agar sinkron dengan UI
                        if (tanggalPindah) {
                            this.selectedPatient.tanggal_pindah = tanggalPindah;
                        }
                    }

                    // 3. Jika setelah dicek ke URL tetap kosong, hentikan
                    if (!this.selectedPatient.tanggal_masuk || !tanggalPindah) {
                        this.selectedPatient.lama_dirawat = "";
                        return;
                    }

                    let masuk = this.getJakartaDate(this.selectedPatient.tanggal_masuk);
                    let pindah = this.getJakartaDate(tanggalPindah);

                    if (pindah < masuk) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanggal Tidak Valid',
                            text: 'Tanggal pindah tidak boleh sebelum tanggal masuk!',
                            confirmButtonColor: '#3085d6',
                        });

                        this.selectedPatient.tanggal_pindah = this.formatDate(masuk);
                        this.selectedPatient.lama_dirawat = 1;
                        return;
                    }

                    let timeDiff = pindah.getTime() - masuk.getTime();
                    let diffDays = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

                    this.selectedPatient.lama_dirawat = diffDays === 0 ? 1 : diffDays;
                },

                updateRuanganByKelas() {
                    let kelas = this.allKelas.find(k => k.name === this.selectedPatient.kelas_tujuan);
                    this.ruangans = kelas ? kelas.ruangans : [];
                    this.selectedPatient.nama_ruangan_tujuan = "";
                }
            };
        }
    </script>

@endsection