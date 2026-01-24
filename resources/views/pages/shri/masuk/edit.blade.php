@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold underline mb-6">Edit Pasien Masuk</h1>

    <form action="{{ route('register-shri.masuk.update', $shri->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label for="no_rekam_medis" class="block font-medium">No. Rekam Medis</label>
                    <input type="text" id="no_rekam_medis" class="w-full border border-gray-300 rounded px-4 py-2"
                        value="{{ $shri->pasien->no_rekam_medis }}" readonly />
                </div>

                <div>
                    <label for="nama_pasien" class="block font-medium">Nama Pasien</label>
                    <input type="text" id="nama_pasien" class="w-full border border-gray-300 rounded px-4 py-2"
                        value="{{ $shri->pasien->nama_pasien }}" readonly />
                </div>

                <div>
                    <label for="tanggal_lahir" class="block font-medium">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" class="w-full border border-gray-300 rounded px-4 py-2"
                        value="{{ $shri->pasien->tanggal_lahir }}" readonly />
                </div>

                <div>
                    <label for="jenis_kelamin" class="block font-medium">Jenis Kelamin</label>
                    <select id="jenis_kelamin" class="w-full border border-gray-300 rounded px-4 py-2" disabled>
                        <option value="Laki-Laki" {{ $shri->pasien->jenis_kelamin == 'Laki-Laki' ? 'selected' : '' }}>
                            Laki-laki</option>
                        <option value="Perempuan" {{ $shri->pasien->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                            Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="status_pasien" class="block font-medium">Status Pasien</label>
                    <input type="text" id="status_pasien" class="w-full border border-gray-300 rounded px-4 py-2"
                        value="{{ $shri->pasien->status_pasien }}" readonly />
                </div>
            </div>

            <!-- Kanan -->
            <div class="space-y-4">
                <div>
                    <label for="tanggal_masuk" class="block font-medium">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk"
                        class="w-full border border-gray-300 rounded px-4 py-2" value="{{ $shri->tanggal_masuk }}"
                        max="{{ now()->toDateString() }}" />
                </div>

                <div>
                    <label for="asal_pasien" class="block font-medium">Asal Pasien</label>
                    <select name="asal_pasien" id="asal_pasien" class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        <option value="masuk-langsung" {{ $shri->asal_pasien == 'masuk-langsung' ? 'selected' : '' }}>Masuk
                            Langsung</option>
                        <option value="pindahan-antar-ruangan"
                            {{ $shri->asal_pasien == 'pindahan-antar-ruangan' ? 'selected' : '' }}>Pindahan Antar Ruangan
                        </option>
                    </select>
                </div>



                <div>
                    <label for="kelas_perawatan" class="block font-medium">Kelas Perawatan</label>
                    <select name="kelas_perawatan" id="kelas_perawatan"
                        class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        @foreach ($kelas as $item)
                            <option value="{{ $item->id }}"
                                {{ $item->id == ($shri->ruangan->kelas_ruangan_id ?? '') ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="ruang_perawatan" class="block font-medium">Ruang Perawatan</label>
                    <select name="ruang_perawatan" id="ruang_perawatan"
                        class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih Kelas Dulu</option>
                    </select>
                </div>

                <div>
                    <label for="jenis_penjaminan" class="block font-medium">Jenis Penjaminan</label>
                    <select name="jenis_penjaminan" id="jenis_penjaminan"
                        class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        @foreach ($jaminans as $item)
                            <option value="{{ $item->id }}"
                                {{ $item->id == $shri->jenis_penjaminan_id ? 'selected' : '' }}>
                                {{ $item->jenis_penjaminan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="dpjp" class="block font-medium">DPJP</label>
                    <select name="dpjp" id="dpjp" class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        @foreach ($dpjps as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $shri->dpjp_id ? 'selected' : '' }}>
                                {{ $item->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tombol -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('register-shri.masuk.view') }}"
                class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-800">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update</button>
        </div>
    </form>
@endsection

@push('js')
    <script>
        const kelasData = @json($kelas); // Kelas + relasi ruangans
        const ruangIdLama = "{{ $shri->kelas_perawatan_id }}"; // foreign ke ruangan.id

        $(document).ready(function() {
            function populateRuang(kelasId, ruangId = null) {
                const ruangSelect = $('#ruang_perawatan');
                ruangSelect.empty();

                if (!kelasId) {
                    ruangSelect.append('<option value="">Pilih Kelas Dulu</option>');
                    return;
                }

                const selectedKelas = kelasData.find(k => k.id == kelasId);

                if (selectedKelas && selectedKelas.ruangans.length > 0) {
                    ruangSelect.append('<option value="">Pilih Ruangan</option>');
                    selectedKelas.ruangans.forEach(r => {
                        ruangSelect.append(
                            `<option value="${r.id}" ${r.id == ruangId ? 'selected' : ''}>${r.nama_ruangan}</option>`
                        );
                    });
                } else {
                    ruangSelect.append('<option value="">Tidak ada ruangan tersedia</option>');
                }
            }

            // saat user pilih kelas
            $('#kelas_perawatan').on('change', function() {
                populateRuang($(this).val());
            });

            // inisialisasi halaman edit
            const initialKelasId = $('#kelas_perawatan').val();
            if (initialKelasId) {
                populateRuang(initialKelasId, ruangIdLama);
            }
        });
    </script>
@endpush
