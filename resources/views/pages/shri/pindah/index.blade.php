@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-4 underline">Pasien Pindah</h2>

    <div>
        <div class="flex gap-4 mb-4">
            <div class="rounded-lg px-4 flex items-center" style="background-color: #EADDFF;">
                <span>Pilih Tanggal Sensus</span>
            </div>
            <input type="date" id="tanggal-sensus-pindah" class="border border-gray-500 rounded px-4 py-2">
        </div>

        <div class="flex justify-between mt-3 bg-[#34495E] p-4 rounded-lg text-white">
            <h2>Daftar Pasien Pindah</h2>
            <a href="{{ route('register-shri.pindah.daftar') }}" id="btn-tambah-pindah"
                class="flex items-center gap-3 pointer-events-none opacity-50">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah</span>
            </a>
        </div>

        <form method="GET" action="{{ route('register-shri.pindah.view') }}" class="mt-4">
            <div class="flex flex-row lg:flex-row gap-4 items-end">
                <!-- Search Input -->
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
                            <option value="{{ $year }}"
                                {{ (request('tahun') ?? date('Y')) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Month Filter -->
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
                            <option value="{{ $num }}"
                                {{ (request('bulan') ?? date('n')) == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-filter"></i>
                        Tampilkan
                    </button>
                </div>

                @if (request('search') || request('tahun') || request('bulan'))
                    <div>
                        <a href="{{ route('register-shri.pindah.view') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    </div>
                @endif
            </div>
        </form>

        <!-- Tabel Pasien Pindah -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Tanggal Masuk</th>
                        <th class="px-4 py-2 border">Ruang Awal</th>
                        <th class="px-4 py-2 border">Tanggal Pindah</th>
                        <th class="px-4 py-2 border">Ruang Tujuan</th>
                        <th class="px-4 py-2 border">Kelas Tujuan</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($pindahs as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->no_rekam_medis ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->nama_pasien ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->jenis_kelamin ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->tanggal_masuk ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->ruang_perawatan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->tanggal_pindah ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->ruangan->nama_ruangan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->ruangan->kelas->name ?? '-' }}</td>
                            <td class="px-4 py-2 border flex gap-2">
                                <a href="{{ route('register-shri.pindah.edit', ['id' => $item->id]) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('register-shri.pindah.destroy', ['id' => $item->id]) }}"
                                    method="post"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('delete')
                                    <button class="text-red-600 hover:text-red-800" type="submit">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-gray-500 py-4">Tidak ada data pasien pindah</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination jika diperlukan -->
            @if (method_exists($pindahs, 'links'))
                <div class="mt-4">
                    {{ $pindahs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
    @push('js')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const inputTanggal = document.getElementById("tanggal-sensus-pindah");
                const tambahBtn = document.getElementById("btn-tambah-pindah");
                const checkboxTidakAda = document.getElementById("tidak-ada-pindah");

                const nowJakarta = new Date(
                    new Date().toLocaleString("en-US", {
                        timeZone: "Asia/Jakarta"
                    })
                );

                const year = nowJakarta.getFullYear();
                const month = String(nowJakarta.getMonth() + 1).padStart(2, '0');
                const day = String(nowJakarta.getDate()).padStart(2, '0');

                //const maxDateStr = nowJakarta.toISOString().split("T")[0];
                const maxDateStr = `${year}-${month}-${day}`;
                inputTanggal.setAttribute("max", maxDateStr);


                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

                function updateTambahHref() {
                    const selectedDate = inputTanggal.value;
                    if (selectedDate) {
                        tambahBtn.setAttribute(
                            "href",
                            `/register-shri/pindah/daftar?tanggal_sensus=${encodeURIComponent(selectedDate)}`
                        );
                    } else {
                        tambahBtn.setAttribute("href", "#");
                    }
                }

                function updateTambahState() {
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
                inputTanggal.addEventListener("change", async function() {
                    updateTambahHref();
                    updateTambahState();
                });
                updateTambahHref();
            });
        </script>
    @endpush
@endpush
