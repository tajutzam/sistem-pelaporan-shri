@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold underline mb-6">Formulir Pendaftaran Pasien Masuk</h1>

    <form action="{{ route('register-shri.masuk.store', ['id' => 1]) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label for="no_rekam_medis" class="block font-medium">No. Rekam Medis</label>
                    <div class="relative">
                        <input maxlength="8" type="text" name="no_rekam_medis" id="no_rekam_medis"
                            class="w-full border border-gray-300 rounded px-4 py-2 pr-10 focus:outline-none" />
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="nama_pasien" class="block font-medium">Nama Pasien</label>
                    <input type="text" name="nama_pasien" id="nama_pasien"
                        class="w-full border border-gray-300 rounded px-4 py-2" />
                </div>

                <div>
                    <label for="tanggal_lahir" class="block font-medium">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                        class="w-full border border-gray-300 rounded px-4 py-2" />
                </div>

                <div>
                    <label for="jenis_kelamin" class="block font-medium">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        <option value="Laki-Laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="status_pasien" class="block font-medium">Status Pasien</label>
                    <input type="text" name="status_pasien" id="status_pasien" value="BARU" readonly
                        class="w-full border border-gray-300 rounded px-4 py-2" />
                </div>
            </div>

            <!-- Kanan -->
            <div class="space-y-4">
                <div>
                    <label for="tanggal_masuk" class="block font-medium">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk"
                        class="w-full border border-gray-300 rounded px-4 py-2" />
                </div>

                <div>
                    <label for="asal_pasien" class="block font-medium">Asal Pasien</label>
                    <select name="asal_pasien" id="asal_pasien" class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        <option value="masuk-langsung">Masuk Langsung</option>
                        <option value="pindahan-antar-ruangan">Pindahan Antar Ruangan</option>
                    </select>
                </div>

                <div>
                    <label for="kelas_perawatan" class="block font-medium">Kelas Perawatan</label>
                    <select name="kelas_perawatan" id="kelas_perawatan"
                        class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        @foreach ($kelas as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                            <option value="{{$item->id}}">{{$item->jenis_penjaminan}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="dpjp" class="block font-medium">DPJP</label>
                    <select name="dpjp" id="dpjp" class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        @foreach ($dpjps as $item)
                            <option value="{{$item->id}}">{{$item->nama_lengkap}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tombol -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('register-shri.masuk.view') }}"
                class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-800">Kembali</a>
            <button type="submit" class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-800">Simpan</button>
        </div>
    </form>
@endsection

@push('js')
    <script>
        const kelasData = @json($kelas);

        $(document).ready(function () {
            $('#no_rekam_medis').on('input', function () {
                let query = $(this).val();
                if (query.length > 2) {
                    $.ajax({
                        url: "{{ url('/api/pasien/search') }}",
                        type: "GET",
                        data: { term: query },
                        success: function (response) {
                            if (response.success && response.data.length === 1) {
                                let pasien = response.data[0];
                                $('#nama_pasien').val(pasien.nama_pasien);
                                $('#tanggal_lahir').val(pasien.tanggal_lahir);
                                $('#jenis_kelamin').val(pasien.jenis_kelamin);
                                $('#status_pasien').val('Pasien Lama');
                            } else {
                                $('#nama_pasien').val('');
                                $('#tanggal_lahir').val('');
                                $('#jenis_kelamin').val('');
                                $('#status_pasien').val('BARU');
                            }
                        },
                        error: function () {
                            console.error('Gagal mengambil data pasien');
                        }
                    });
                }
            });

            $('#kelas_perawatan').on('change', function () {
                const kelasId = $(this).val();
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
                        ruangSelect.append(`<option value="${r.id}">${r.nama_ruangan}</option>`);
                    });
                } else {
                    ruangSelect.append('<option value="">Tidak ada ruangan tersedia</option>');
                }
            });

            function getJakartaDate(dateString = null) {
                let date = dateString ? new Date(dateString) : new Date();
                let jakarta = new Date(date.toLocaleString("en-US", { timeZone: "Asia/Jakarta" }));
                return jakarta;
            }

            function formatDate(dateObj) {
                let yyyy = dateObj.getFullYear();
                let mm = String(dateObj.getMonth() + 1).padStart(2, "0");
                let dd = String(dateObj.getDate()).padStart(2, "0");
                return `${yyyy}-${mm}-${dd}`;
            }
            const urlParams = new URLSearchParams(window.location.search);
            const tanggalSensus = urlParams.get('tanggal_sensus');

            if (tanggalSensus) {
                let sensusJakarta = getJakartaDate(tanggalSensus);
                $('#tanggal_masuk').val(formatDate(sensusJakarta));
                $('#tanggal_masuk').prop("readonly", true);

            } else {
                let todayJakarta = getJakartaDate();
                $('#tanggal_masuk').val(formatDate(todayJakarta));
                $('#tanggal_masuk').prop("readonly", true);
            }
        });
    </script>
@endpush