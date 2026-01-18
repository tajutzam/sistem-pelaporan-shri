@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-4 underline">Pasien Masuk</h2>

    <div>
        <div class="flex gap-4">
            <div class="rounded-lg px-4 flex items-center" style="background-color: #EADDFF;">
                <span>Pilih Tanggal Sensus</span>
            </div>
            <input type="date" id="tanggal-sensus" class="border border-gray-500 rounded px-4 py-2">
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

        <form action="{{ route('register-shri.masuk.view') }}" method="GET" class="mt-4">
            <div class="flex flex-row lg:flex-row gap-4 items-end">
                <div class="relative flex-1 max-w-xs bg-[#E7E9D4]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama / no. rekam medis..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
                </div>

                <!-- Year Filter -->
                <div class="flex flex-col">
                    <label for="tahun" class="text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="tahun" id="tahun"
                        class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        @for ($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ (request('tahun') ?? date('Y')) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="bulan" class="text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select name="bulan" id="bulan"
                        class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Bulan</option>
                        @php
                            $months = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];
                        @endphp
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ (request('bulan') ?? date('n')) == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-filter"></i>
                        Tampilkan
                    </button>
                </div>

                <!-- Clear Filter Button -->
                @if (request('search') || request('tahun') || request('bulan'))
                    <div>
                        <a href="{{ route('register-shri.masuk.view') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    </div>
                @endif
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
                            <td class="px-4 py-2 border">{{ $shri->tanggal_masuk }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->no_rekam_medis ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->nama_pasien ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->tanggal_lahir ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->pasien->jenis_kelamin ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->status_pasien ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->kelasPerawatan->nama_ruangan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $shri->kelasPerawatan->kelas->name ?? '-' }}</td>
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
    @push('js')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const inputTanggal = document.getElementById("tanggal-sensus");
                const tambahBtn = document.getElementById("btn-tambah");

                const nowJakarta = new Date(
                    new Date().toLocaleString("en-US", { timeZone: "Asia/Jakarta" })
                );

                const maxDateStr = nowJakarta.toISOString().split("T")[0];

                inputTanggal.setAttribute("max", maxDateStr);

                let isSensusValid = false;
                let isLoading = false;
                const jenisRegister = "masuk";



                function updateTambahHref() {
                    const selectedDate = inputTanggal.value;

                    if (selectedDate) {
                        tambahBtn.classList.remove("pointer-events-none", "opacity-50");
                        tambahBtn.querySelector("button").disabled = false;

                        tambahBtn.setAttribute(
                            "href",
                            `/register-shri/${jenisRegister}/create?tanggal_sensus=${encodeURIComponent(selectedDate)}`
                        );
                    } else {
                        tambahBtn.classList.add("pointer-events-none", "opacity-50");
                        tambahBtn.querySelector("button").disabled = true;

                        tambahBtn.setAttribute("href", "#");
                    }
                }

                const form = tambahBtn.closest("form");
                if (form) {
                    form.addEventListener("submit", function (e) {
                        e.preventDefault();
                        showValidationMessage(false, inputTanggal.value);
                    });
                }
                updateTambahHref();
                inputTanggal.addEventListener("change", updateTambahHref);
            });
        </script>
    @endpush
@endpush