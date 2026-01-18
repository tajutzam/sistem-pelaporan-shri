@extends('layouts.app')

@section('content')
    @php
        $tanggalSensus = request()->query('tanggal_sensus')
            ? \Carbon\Carbon::parse(request()->query('tanggal_sensus'))
            : \Carbon\Carbon::today();

        $tanggalSensusH = $tanggalSensus->copy()->format('Y-m-d'); // H
        $tanggalSensusHMin = $tanggalSensus->copy()->subDay()->format('Y-m-d'); // H-1
    @endphp

    <div x-data="{
        open: false,
        selectedItem: {
            no_rekam_medis: '',
            shri_id: '',
            nama_pasien: '',
            jenis_kelamin: '',
            tanggal_masuk: '',
            tanggal_keluar: '',
            ruangan: '',
            lama_dirawat: ''
        },
    
        // PANGGIL GLOBAL FUNCTION
        calculateLamaDirawat() {
            this.selectedItem = window.calculateLamaDirawatKeluar(this.selectedItem);
        },
    
        initTanggalKeluarRange() {
            window.setTanggalKeluarRange(this.selectedItem.tanggal_masuk);
        },
    
        closeModal() {
            this.open = false;
            $('#diagnosa_id').val(null).trigger('change');
            this.selectedItem = {
                no_rekam_medis: '',
                shri_id: '',
                nama_pasien: '',
                jenis_kelamin: '',
                tanggal_masuk: '',
                tanggal_keluar: '',
                ruangan: '',
                lama_dirawat: ''
            };
        },
    }">

        <!-- Header -->
        <div class="flex justify-between items-center mt-3 bg-[#34495E] p-4 rounded-lg text-white">
            <h2>Daftar Pasien Dirawat</h2>
            <div class="flex justify-end">
                <div class="relative w-full max-w-xs bg-[#E7E9D4] rounded-lg text-gray-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" placeholder="Search..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                        {{-- Siapkan kalau mau dipakai: oninput submit/filter --}} />
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak style="display: none;">
            <div x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75"
                class="bg-gray-100 p-6 rounded-lg w-full max-w-4xl relative"
                @click.outside.window="if(!$event.target.closest('.select2-container')) closeModal()">

                <form action="{{ route('register-shri.keluar.store') }}" method="POST">
                    @csrf

                    <h2 class="text-lg font-semibold underline mb-4">Formulir Pendaftaran Pasien Keluar</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekam Medis</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.no_rekam_medis" disabled />
                        </div>

                        <input type="hidden" name="shri_id" :value="selectedItem.shri_id">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Keluar</label>
                            <input name="tanggal_keluar" type="date" class="w-full p-2 rounded border"
                                x-model="selectedItem.tanggal_keluar" @change="calculateLamaDirawat" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.nama_pasien"
                                disabled />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DPJP</label>
                            <select name="dpjp_id" class="w-full p-2 rounded border">
                                <option value="">Pilih salah satu</option>
                                @foreach ($dpjps as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.jenis_kelamin" disabled />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosa Akhir</label>
                            <select name="diagnosa_id" id="diagnosa_id" class="w-full p-2 rounded border">
                                <option value="">Pilih diagnosa...</option>
                                <!-- Options akan diload via AJAX -->
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.tanggal_masuk" disabled />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cara Keluar</label>
                            <select name="cara_keluar" class="w-full p-2 rounded border">
                                <option value="">Pilih salah satu</option>
                                @foreach (config('data.cara_keluar') as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                            <input type="text" class="w-full bg-gray-300 p-2 rounded" x-model="selectedItem.ruangan"
                                disabled />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lama Dirawat</label>
                            <input type="text" name="lama_dirawat" class="w-full bg-gray-300 p-2 rounded"
                                x-model="selectedItem.lama_dirawat" readonly />
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button @click="closeModal()" type="button"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">
                            Tutup
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table: daftar pasien dirawat -->
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
                    @foreach ($dirawats as $item)
                        @php
                            // Tentukan ruangan & kelas terakhir (kalau ada pindah, pakai ruangan pindah; kalau tidak, pakai ruangan awal)
                            $ruanganTerakhir = $item->pindah ? $item->pindah->ruangan : $item->ruangan;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $item->pasien->no_rekam_medis }}</td>
                            <td class="px-4 py-2 border">{{ $item->pasien->nama_pasien }}</td>
                            <td class="px-4 py-2 border">{{ $item->pasien->jenis_kelamin }}</td>
                            <td class="px-4 py-2 border">{{ $item->tanggal_masuk }}</td>
                            <td class="px-4 py-2 border">{{ $ruanganTerakhir->nama_ruangan ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ optional($ruanganTerakhir->kelas)->name ?? '-' }}</td>
                            <td class="px-4 py-2 border">
                                <button class="text-blue-600 underline"
                                    @click="
                                        open = true;

                                        // Ambil tanggal dari URL parameter
                                        let urlParams = new URLSearchParams(window.location.search);
                                        let tanggalSensusDariUrl = urlParams.get('tanggal_sensus') || '';

                                        selectedItem = {
                                            no_rekam_medis: '{{ $item->pasien->no_rekam_medis }}',
                                            shri_id:        '{{ $item->id }}',
                                            nama_pasien:    '{{ $item->pasien->nama_pasien }}',
                                            jenis_kelamin:  '{{ $item->pasien->jenis_kelamin }}',
                                            tanggal_masuk:  '{{ $item->tanggal_masuk }}',
                                            tanggal_keluar: tanggalSensusDariUrl, // SET OTOMATIS DISINI
                                            ruangan:        '{{ $ruanganTerakhir->nama_ruangan ?? '-' }}',
                                            lama_dirawat:   ''
                                        };

                                        // Panggil fungsi JS global untuk set range dan kalkulasi hari
                                        setTimeout(() => {
                                            initTanggalKeluarRange();
                                            calculateLamaDirawat(); // LANGSUNG HITUNG
                                        }, 50);

                                        // Reset select2
                                        setTimeout(() => initSelect2(), 100);
                                    ">
                                    <i class="fa-solid fa-plus"></i> Tambah
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    @push('js')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Tambahkan Select2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            function initSelect2() {
                // Destroy existing Select2 if it exists
                if ($('#diagnosa_id').hasClass("select2-hidden-accessible")) {
                    $('#diagnosa_id').select2('destroy');
                }

                // Initialize Select2 with AJAX
                $('#diagnosa_id').select2({
                    placeholder: "Ketik untuk mencari diagnosa...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('.bg-gray-100'), // container modal
                    ajax: {
                        url: '{{ route('api.diagnosa.search') }}',
                        dataType: 'json',
                        delay: 300, // Delay untuk mengurangi request
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;

                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2, // Minimal 2 karakter untuk mulai search
                    language: {
                        inputTooShort: function() {
                            return 'Ketik minimal 2 karakter untuk mencari diagnosa';
                        },
                        searching: function() {
                            return 'Mencari diagnosa...';
                        },
                        noResults: function() {
                            return 'Tidak ada diagnosa yang ditemukan';
                        }
                    }
                });
            }

            $(document).ready(function() {
                initSelect2();
            });


            window.toJakarta = function(dateString = null) {
                let d = dateString ? new Date(dateString) : new Date();
                return new Date(d.toLocaleString("en-US", {
                    timeZone: "Asia/Jakarta"
                }));
            };

            window.formatDate = function(d) {
                return d.getFullYear() + "-" +
                    String(d.getMonth() + 1).padStart(2, "0") + "-" +
                    String(d.getDate()).padStart(2, "0");
            };

            window.setTanggalKeluarRange = function(tanggalMasuk) {
                let masuk = window.toJakarta(tanggalMasuk);

                let sensus = new URLSearchParams(window.location.search).get("tanggal_sensus");
                let maxDate = sensus ? window.toJakarta(sensus) : window.toJakarta();

                let input = document.querySelector("input[name=tanggal_keluar]");
                if (input) {
                    input.max = window.formatDate(maxDate);
                }
            };
            window.calculateLamaDirawatKeluar = function(selectedItem) {
                if (!selectedItem.tanggal_keluar || !selectedItem.tanggal_masuk) {
                    selectedItem.lama_dirawat = "";
                    return selectedItem;
                }

                let masuk = window.toJakarta(selectedItem.tanggal_masuk);
                let keluar = window.toJakarta(selectedItem.tanggal_keluar);

                if (keluar < masuk) {
                    Swal.fire({
                        icon: "warning",
                        title: "Tanggal tidak valid",
                        text: "Tanggal keluar tidak boleh sebelum tanggal masuk!"
                    });

                    selectedItem.tanggal_keluar = "";
                    selectedItem.lama_dirawat = "";
                    return selectedItem;
                }

                let diff = Math.ceil((keluar - masuk) / (1000 * 60 * 60 * 24));
                if (diff <= 0) diff = 1;

                selectedItem.lama_dirawat = diff + " hari";
                return selectedItem;
            };
        </script>
    @endpush
@endsection
