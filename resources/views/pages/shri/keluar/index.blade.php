@extends('layouts.app')

@section('content')
    <div x-data="{
        open: false,
        selectedItem: {
            shri_id: '',
            id: '',
            no_rekam_medis: '',
            nama_pasien: '',
            jenis_kelamin: '',
            tanggal_masuk: '',
            tanggal_keluar: '',
            ruangan: '',
            lama_dirawat: '',
            dpjp_id: '',
            diagnosa_id: '',
            cara_keluar: ''
        },
        calculateLamaDirawat() {
            const masuk = new Date(this.selectedItem.tanggal_masuk);
            const keluar = new Date(this.selectedItem.tanggal_keluar);

            if (!this.selectedItem.tanggal_masuk || !this.selectedItem.tanggal_keluar) {
                this.selectedItem.lama_dirawat = '';
                return;
            }

            let diff = Math.floor((keluar - masuk) / (1000 * 60 * 60 * 24));

            // kalau hasil 0, tetap hitung 1 hari
            if (diff === 0) {
                diff = 1;
            }

            this.selectedItem.lama_dirawat = `${diff} hari`;
        },
        closeModal() {
            this.open = false;
            this.selectedItem = {
                shri_id: '',
                id: '',
                no_rekam_medis: '',
                nama_pasien: '',
                jenis_kelamin: '',
                tanggal_masuk: '',
                tanggal_keluar: '',
                ruangan: '',
                lama_dirawat: '',
                dpjp_id: '',
                diagnosa_id: '',
                cara_keluar: ''
            };
        },
        
    }">

        <h2 class="font-bold text-2xl mb-4 underline">Pasien Keluar</h2>

        <div class="flex gap-4 mb-4 items-center">
            <div class="rounded-lg px-4 py-2 flex items-center" style="background-color: #EADDFF;">
                <span>Pilih Tanggal Sensus</span>
            </div>
            <input type="date" id="tanggal-sensus-keluar" class="border border-gray-500 rounded px-4 py-2"
                value="{{ request('tanggal_sensus') }}">
        </div>

        <!-- Header -->
        <div class="flex justify-between mt-3 bg-[#34495E] p-4 rounded-lg text-white">
            <h2>Daftar Pasien Keluar</h2>
            <a href="{{ route('register-shri.keluar.daftar') }}" id="btn-tambah-keluar"
                class="flex items-center gap-3 pointer-events-none opacity-50">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah</span>
            </a>
        </div>

        <!-- Search Form and Filters -->
        <form method="GET" action="{{ route('register-shri.keluar.view') }}" class="mt-4">
            <div class="flex flex-row gap-4 items-end">
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
                        <a href="{{ route('register-shri.keluar.view') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    </div>
                @endif
            </div>
        </form>

        {{-- Hitung batas tanggal keluar = H-1 dari tanggal_sensus (kalau ada) --}}
        @php
            $sensus = request('tanggal_sensus');
            $tglKeluarFix = \Carbon\Carbon::now()->format('Y-m-d');
        @endphp

        <!-- Modal -->
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak style="display: none;">
            <div x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75"
                class="bg-gray-100 p-6 rounded-lg w-full max-w-4xl relative" @click.outside="closeModal()">

                <form
                    :action="selectedItem.shri_id ? `/register-shri/keluar/update/${selectedItem.id}` :
                        '{{ route('register-shri.keluar.store') }}'"
                    method="POST">

                    @csrf
                    <template x-if="selectedItem.shri_id">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <h2 class="text-lg font-semibold underline mb-4">Formulir Pasien Keluar</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekam Medis</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.no_rekam_medis" disabled>
                        </div>

                        <input type="hidden" name="shri_id" :value="selectedItem.shri_id">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Keluar</label>
                            <input name="tanggal_keluar" type="date" class="w-full p-2 rounded border"
                                x-model="selectedItem.tanggal_keluar" @change="calculateLamaDirawat"
                                @if ($tglKeluarFix) max="{{ $tglKeluarFix }}" @endif>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.nama_pasien"
                                disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DPJP</label>
                            <select name="dpjp_id" x-model="selectedItem.dpjp_id" class="w-full p-2 rounded border">
                                <option value="">Pilih salah satu</option>
                                @foreach ($dpjps as $dpjp)
                                    <option value="{{ $dpjp->id }}">{{ $dpjp->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.jenis_kelamin" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosa Akhir</label>
                            <select name="diagnosa_id" x-model="selectedItem.diagnosa_id"
                                class="w-full p-2 rounded border">
                                <option value="">Pilih salah satu</option>
                                @foreach ($diagnosas as $diagnosa)
                                    <option value="{{ $diagnosa->id }}">{{ $diagnosa->kode_icd }} -
                                        {{ $diagnosa->diagnosa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.tanggal_masuk" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cara Keluar</label>
                            <select name="cara_keluar" x-model="selectedItem.cara_keluar"
                                class="w-full p-2 rounded border">
                                <option value="">Pilih salah satu</option>
                                @foreach (config('data.cara_keluar') as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.ruangan"
                                disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lama Dirawat</label>
                            <input type="text" name="lama_dirawat" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.lama_dirawat" readonly>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button @click="closeModal()" type="button"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">Tutup</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Tanggal Masuk</th>
                        <th class="px-4 py-2 border">Tanggal Keluar</th>

                        <th class="px-4 py-2 border">No Rekam Medis</th>
                        <th class="px-4 py-2 border">Nama Pasien</th>
                        <th class="px-4 py-2 border">Jenis Kelamin</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">Diagnosa Akhir</th>
                        <th class="px-4 py-2 border">Cara Keluar</th>
                        <th class="px-4 py-2 border">Lama Dirawat</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($shris as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $item->shri->tanggal_masuk }}</td>
                            <td class="px-4 py-2 border">{{ $item->tanggal_keluar }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->no_rekam_medis }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->nama_pasien }}</td>
                            <td class="px-4 py-2 border">{{ $item->shri->pasien->jenis_kelamin }}</td>
                            <td class="px-4 py-2 border">
                                {{ $item->shri->pindah ? $item->shri->pindah->ruangan->nama_ruangan : $item->shri->ruangan->nama_ruangan }}
                            </td>
                            <td class="px-4 py-2 border">{{ $item->diagnosa->diagnosa }}</td>
                            <td class="px-4 py-2 border">{{ $item->cara_keluar }}</td>
                            <td class="px-4 py-2 border">{{ $item->lama_dirawat }}</td>
                            <td class="px-4 py-2 border flex gap-2">
                                <button
                                    @click="open = true; selectedItem = {
                                                                            shri_id: '{{ $item->shri_id }}',
                                                                            id : '{{ $item->id }}',
                                                                            no_rekam_medis: '{{ $item->shri->pasien->no_rekam_medis }}',
                                                                            nama_pasien: '{{ $item->shri->pasien->nama_pasien }}',
                                                                            jenis_kelamin: '{{ $item->shri->pasien->jenis_kelamin }}',
                                                                            tanggal_masuk: '{{ $item->shri->tanggal_masuk }}',
                                                                            tanggal_keluar: '{{ $item->tanggal_keluar }}',
                                                                            ruangan: '{{ $item->shri->ruangan->nama_ruangan }}',
                                                                            lama_dirawat: '{{ $item->lama_dirawat }}',
                                                                            dpjp_id: '{{ $item->dpjp_id }}',
                                                                            diagnosa_id: '{{ $item->diagnosa_id }}',
                                                                            cara_keluar: '{{ $item->cara_keluar }}'
                                                                        }"
                                    class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('register-shri.keluar.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 transition-colors" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination jika diperlukan -->
            @if (method_exists($shris, 'links'))
                <div class="mt-4">
                    {{ $shris->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
   <script>
document.addEventListener("DOMContentLoaded", function() {
    const inputTanggal = document.getElementById("tanggal-sensus-keluar");
    const tambahBtn = document.getElementById("btn-tambah-keluar");

    const today = new Date();
    inputTanggal.setAttribute("max", today.toISOString().split("T")[0]);

    function updateTambahHref() {
        const selectedDate = inputTanggal.value;

        if (selectedDate) {
            tambahBtn.setAttribute(
                "href",
                `/register-shri/keluar/daftar?tanggal_sensus=${encodeURIComponent(selectedDate)}`
            );
        } else {
            tambahBtn.setAttribute("href", "#");
        }
    }

    function updateTambahState() {
        const isTanggalDipilih = inputTanggal.value !== "";

        if (isTanggalDipilih) {
            tambahBtn.classList.remove("pointer-events-none", "opacity-50");
        } else {
            tambahBtn.classList.add("pointer-events-none", "opacity-50");
        }
    }

    // INITIAL STATE (awal harus disabled)
    updateTambahHref();
    updateTambahState();

    // FIXED: gunakan callback yang benar
    inputTanggal.addEventListener("change", function() {
        updateTambahHref();
        updateTambahState();
    });
});
</script>

@endpush
