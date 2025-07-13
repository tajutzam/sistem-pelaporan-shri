@extends('layouts.app')

@section('content')
    <div x-data="{
            ruangans: [],
            selectedKelas: '{{ $pindah->kelas->kelas_ruangan }}',
            selectedRuangan: '{{ $pindah->ruangan_tujuan ?? '' }}',
            tanggalMasuk: '{{ $pindah->shri->tanggal_masuk }}',
            tanggalPindah: '{{ $pindah->tanggal_pindah }}',
            lamaDirawat: '{{ $pindah->lama_dirawat }}',

            async fetchRuanganByKelas() {
                this.selectedRuangan = ''
                if (!this.selectedKelas) {
                    this.ruangans = []
                    return
                }

                const response = await fetch(`/api/get-ruangan-by-kelas/${this.selectedKelas}`)
                this.ruangans = await response.json()
            },

            calculateLamaDirawat() {
                if (!this.tanggalMasuk || !this.tanggalPindah) {
                    this.lamaDirawat = ''
                    return
                }

                const masuk = new Date(this.tanggalMasuk)
                const pindah = new Date(this.tanggalPindah)

                const diffTime = pindah.getTime() - masuk.getTime()
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
                this.lamaDirawat = diffDays > 0 ? diffDays : 0
            }
        }" x-init="fetchRuanganByKelas(); calculateLamaDirawat()">
        <form action="{{ route('register-shri.pindah.update', $pindah->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="bg-gray-100 p-6 rounded-lg w-full max-w-4xl relative">
                <h2 class="text-lg font-semibold underline mb-4">Edit Pindah Pasien</h2>
                <div class="grid grid-cols-2 gap-4">
                    <input type="hidden" name="id" value="{{ $pindah->id }}">

                    <div>
                        <label>No. Rekam Medis</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded"
                            value="{{ $pindah->shri->pasien->no_rekam_medis ?? '-' }}" disabled>
                    </div>

                    <div>
                        <label>Tanggal Pindah</label>
                        <input type="date" name="tanggal_pindah" class="w-full p-2 rounded border" x-model="tanggalPindah"
                            @change="calculateLamaDirawat" />
                    </div>

                    <div>
                        <label>Nama Pasien</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded"
                            value="{{ $pindah->shri->pasien->nama_pasien ?? '-' }}" disabled>
                    </div>

                    <div>
                        <label>Kelas Tujuan</label>
                        <select class="w-full p-2 rounded border" name="kelas_perawatan_id" x-model="selectedKelas"
                            @change="fetchRuanganByKelas">
                            <option value="">Pilih salah satu</option>
                            @foreach (config('ruangan.kelas_ruangan') as $index => $item)
                                <option value="{{ $item }}" {{ $pindah->kelas->kelas_ruangan == $item ? 'selected' : '' }}>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Ruangan Tujuan</label>
                        <select name="ruangan_tujuan" class="w-full p-2 rounded border" x-model="selectedRuangan">
                            <option value="">Pilih salah satu</option>
                            <template x-for="ruangan in ruangans" :key="ruangan.id">
                                <option :value="ruangan.nama_ruangan" x-text="ruangan.nama_ruangan"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label>Tanggal Masuk</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="tanggalMasuk" disabled />
                    </div>

                    <div>
                        <label>Lama Dirawat</label>
                        <input type="number" name="lama_dirawat" class="w-full p-2 rounded border" x-model="lamaDirawat" />
                    </div>

                    <div class="col-span-2">
                        <label>Ruangan Sebelumnya</label>
                        <input type="text" class="w-full bg-gray-300 p-2 rounded"
                            value="{{ $pindah->shri->ruang_perawatan }}" disabled />
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <a href="{{ route('register-shri.pindah.view') }}"
                        class="bg-gray-600 text-white px-4 py-2 rounded">Batal</a>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Perbarui</button>
                </div>
            </div>
        </form>
    </div>
@endsection
