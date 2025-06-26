@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold underline mb-6">Formulir Pendaftaran Pasien Masuk</h1>

    <form action="" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label for="no_rekam_medis" class="block font-medium">No. Rekam Medis</label>
                    <div class="relative">
                        <input type="text" name="no_rekam_medis" id="no_rekam_medis"
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
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="status_pasien" class="block font-medium">Status Pasien</label>
                    <input type="text" name="status_pasien" id="status_pasien"
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
                        <!-- Tambahkan opsi sesuai data -->
                    </select>
                </div>

                <div>
                    <label for="ruang_perawatan" class="block font-medium">Ruang Perawatan</label>
                    <input type="text" name="ruang_perawatan" id="ruang_perawatan"
                        class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-200 cursor-not-allowed" readonly />
                </div>

                <div>
                    <label for="kelas_perawatan" class="block font-medium">Kelas Perawatan</label>
                    <select name="kelas_perawatan" id="kelas_perawatan"
                        class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        <!-- Tambahkan opsi sesuai data -->
                    </select>
                </div>

                <div>
                    <label for="jenis_penjaminan" class="block font-medium">Jenis Penjaminan</label>
                    <select name="jenis_penjaminan" id="jenis_penjaminan"
                        class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        <!-- Tambahkan opsi sesuai data -->
                    </select>
                </div>

                <div>
                    <label for="dpjp" class="block font-medium">DPJP</label>
                    <select name="dpjp" id="dpjp" class="w-full border border-gray-300 rounded px-4 py-2">
                        <option value="">Pilih salah satu</option>
                        <!-- Tambahkan opsi sesuai data -->
                    </select>
                </div>
            </div>
        </div>

        <!-- Tombol -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ back() }}" class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-800">Kembali</a>
            <button type="submit" class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-800">Simpan</button>
        </div>
    </form>
@endsection
