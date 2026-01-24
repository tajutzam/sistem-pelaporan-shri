@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-6 underline">Rekapitulasi Kunjungan Rawat Inap</h2>

    <div class="space-y-6">
        <!-- Filter Form -->
        <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-4">
            <!-- Ruangan -->
            <div class="flex flex-col">
                <label for="ruangan" class="mb-1 text-sm font-medium text-gray-700">
                    Ruangan
                </label>
                <select id="ruangan" name="ruangan"
                    class="bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @if (auth()->user()->hak_akses == 'kepala')
                        <option value="">-- Semua Ruangan --</option>
                    @else
                        <option value="">-- Pilih Ruangan --</option>
                    @endif
                    @foreach ($ruangans as $r)
                        <option value="{{ $r->nama_ruangan }}"
                            {{ request('ruangan') == $r->nama_ruangan ? 'selected' : '' }}>
                            {{ $r->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Tanggal Awal -->
            <div class="flex flex-col">
                <label for="tanggal-awal" class="mb-1 text-sm font-medium text-gray-700">
                    Tanggal Awal
                </label>
                <input type="date" id="tanggal-awal" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
                    class="bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Tanggal Akhir -->
            <div class="flex flex-col">
                <label for="tanggal-akhir" class="mb-1 text-sm font-medium text-gray-700">
                    Tanggal Akhir
                </label>
                <input type="date" id="tanggal-akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                    class="bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Tombol Tampilkan -->
            <div>
                <button type="submit"
                    class="bg-[#34495E] text-white rounded-lg px-5 py-2.5 hover:bg-[#2c3e50] transition font-medium">
                    Tampilkan
                </button>
            </div>

            <!-- Tombol Reset -->
            <div>
                <a href="{{ url()->current() }}"
                    class="bg-gray-500 text-white rounded-lg px-5 py-2.5 hover:bg-gray-600 transition font-medium inline-block">
                    Reset
                </a>
            </div>
        </form>

        <!-- Search Bar -->
        <form method="GET" action="{{ url()->current() }}" class="flex justify-end">
            <!-- Preserve filter parameters -->
            <input type="hidden" name="ruangan" value="{{ request('ruangan') }}">
            <input type="hidden" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
            <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">

            <div class="relative w-full max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" placeholder="Cari..." value="{{ request('search') }}"
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg bg-[#E7E9D4] focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
            </div>
        </form>

        <!-- Info Hasil -->
        @if (request()->hasAny(['ruangan', 'tanggal_awal', 'tanggal_akhir', 'search']))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-sm text-blue-800">
                    <strong>Filter aktif:</strong>
                    @if (request('ruangan'))
                        Ruangan: <span class="font-semibold">{{ request('ruangan') }}</span>
                    @endif

                    @if (request('tanggal_awal'))
                        | Tanggal mulai: <span class="font-semibold">{{ request('tanggal_awal') }}</span>
                    @endif
                    @if (request('tanggal_akhir'))
                        | Tanggal akhir: <span class="font-semibold">{{ request('tanggal_akhir') }}</span>
                    @endif
                    @if (request('search'))
                        | Pencarian: <span class="font-semibold">{{ request('search') }}</span>
                    @endif
                </p>
                <p class="text-sm text-blue-600 mt-1">
                    Menampilkan {{ $laporanKunjungans->count() }} hasil
                </p>
            </div>
        @endif

        <!-- Tabel Pasien -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">NO</th>
                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">Kelas</th>
                        <th class="px-4 py-2 border">Penjaminan</th>
                        <th class="px-4 py-2 border">DPJP</th>
                        <th class="px-4 py-2 border">Tanggal Masuk</th>
                        <th class="px-4 py-2 border">Tanggal Keluar</th>
                        <th class="px-4 py-2 border">Tanggal Pindah</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Status Pasien</th>
                        <th class="px-4 py-2 border">Asal Pasien</th>
                        <th class="px-4 py-2 border">Diagnosa</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($laporanKunjungans as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border">
                                {{ $item['no_rm'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['nama_pasien'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['jenis_kelamin'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['ruangan'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['kelas'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['penjaminan'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['dpjp'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['tanggal_masuk'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['tanggal_keluar'] ?? '-' }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['tanggal_pindah'] ?? '-' }}
                            </td>
                            <td
                                class="px-4 py-2 border font-bold
                                                        @if ($item['status'] === 'dirawat') text-blue-600
                                                        @elseif ($item['status'] === 'pindah') text-yellow-600
                                                        @else text-red-600 @endif">
                                {{ ucfirst($item['status']) }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['status_pasien'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['asal_pasien'] }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item['diagnosa'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data yang ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="flex justify-end gap-2">

         

            <form method="GET" action="{{ route('laporan.kunjungan.cetak') }}" target="_blank">

                <input type="hidden" name="ruangan" value="{{ request('ruangan') }}">
                <input type="hidden" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-print mr-2"></i>Cetak Laporan
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInputs = document.querySelectorAll('input[type="date"]');
            const form = document.querySelector('form');

            dateInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Optional: Auto submit when date changes
                    // form.submit();
                });
            });

            // Real-time search (optional)
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        // Optional: Auto submit search after typing stops
                        // this.form.submit();
                    }, 500);
                });
            }
        });
    </script>
@endsection
