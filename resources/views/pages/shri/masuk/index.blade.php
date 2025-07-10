@extends('layouts.app')

@section('content')

    <h2 class="font-bold text-2xl mb-4 underline">Pasien Masuk</h2>

    <div>
        <div class="flex gap-4">
            <div class="rounded-lg px-4 flex items-center" style="background-color: #EADDFF;">
                <span>Pilih Tanggal Sensus</span>
            </div>
            <input type="date" id="tanggal-sensus" class="border border-gray-500 rounded px-4 py-2">
            <div class="mt-2 flex items-center gap-2">
                <input type="checkbox" id="tidak-ada-pasien" class="w-4 h-4 text-red-600">
                <label for="tidak-ada-pasien" class="text-sm text-gray-700">Tidak ada pasien masuk</label>
            </div>
        </div>

        <!-- Header Section -->
        <div class="flex justify-between mt-3 bg-[#34495E] p-4 rounded-lg text-white">
            <h2>Daftar Pasien Masuk</h2>
            <a href="{{ route('register-shri.masuk.create', ['id' => 1]) }}" id="btn-tambah"
                class="flex items-center gap-3 pointer-events-none opacity-50">
                <i class="fa-solid fa-plus"></i>
                <button type="button" disabled>Tambah</button>
            </a>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('register-shri.masuk.view') }}" method="GET" class="flex justify-end mt-4">
            <div class="relative w-full max-w-xs bg-[#E7E9D4]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama / no. rekam medis..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
            </div>
        </form>

        <!-- Tabel Pasien Masuk -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Tanggal Masuk</th>
                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Tanggal Lahir</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Status Pasien</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">Kelas</th>
                        <th class="px-4 py-2 border">Penjaminan</th>
                        <th class="px-4 py-2 border">DJIP</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($shris as $shri)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $shri->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->no_rekam_medis ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->nama_pasien ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->tanggal_lahir ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->jenis_kelamin ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->status_pasien ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->kelasPerawatan->nama_ruangan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->kelasPerawatan->kelas_ruangan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->jenisPenjaminan->jenis_penjaminan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->dpjp->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-2 border flex gap-2 h-full">
                                <a href="{{ route('register-shri.masuk.edit', ['id' => $shri->id]) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('register-shri.masuk.destroy', ['id' => $shri->id]) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-gray-500 py-4">Tidak ada data pasien masuk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $shris->links() }}
            </div>
        </div>
    </div>

@endsection


@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tanggalInput = document.getElementById("tanggal-sensus");
            const tambahBtn = document.getElementById("btn-tambah");
            const checkboxTidakAda = document.getElementById("tidak-ada-pasien");

            function updateTambahState() {
                const isTanggalDipilih = tanggalInput.value !== "";
                const isTidakAdaDicentang = checkboxTidakAda.checked;

                if (isTanggalDipilih && !isTidakAdaDicentang) {
                    tambahBtn.classList.remove("pointer-events-none", "opacity-50");
                    tambahBtn.querySelector("button").disabled = false;
                } else {
                    tambahBtn.classList.add("pointer-events-none", "opacity-50");
                    tambahBtn.querySelector("button").disabled = true;
                }
            }

            tanggalInput.addEventListener("change", updateTambahState);
            checkboxTidakAda.addEventListener("change", updateTambahState);

            updateTambahState();
        });
    </script>
@endpush
