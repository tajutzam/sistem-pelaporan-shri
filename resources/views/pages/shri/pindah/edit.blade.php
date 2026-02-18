@extends('layouts.app')

@section('content')
<div class="bg-gray-100 p-6 rounded-lg w-full max-w-4xl relative">
    <h2 class="text-lg font-semibold underline mb-4">Edit Pindah Pasien</h2>

    <form action="{{ route('register-shri.pindah.update', $pindah->id) }}" method="post" id="formPindah">
        @csrf
        @method('PUT')

        <input type="hidden" name="id" value="{{ $pindah->id }}">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>No. Rekam Medis</label>
                <input type="text" class="w-full bg-gray-300 p-2 rounded"
                    value="{{ $pindah->shri->pasien->no_rekam_medis ?? '-' }}" readonly>
            </div>

            <div>
                <label>Tanggal Pindah</label>
                <input type="date" name="tanggal_pindah" class="w-full p-2 rounded border" id="tanggalPindah"
                    value="{{ $pindah->tanggal_pindah }}" max="{{ now()->toDateString() }}">
            </div>

            <div>
                <label>Nama Pasien</label>
                <input type="text" class="w-full bg-gray-300 p-2 rounded"
                    value="{{ $pindah->shri->pasien->nama_pasien ?? '-' }}" readonly>
            </div>

            <div>
                <label>Kelas Tujuan</label>
                <select class="w-full p-2 rounded border" name="kelas_tujuan" id="kelasSelect">
                    <option value="">Pilih salah satu</option>
                    @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ $pindah->ruangan->kelas->id == $k->id ? 'selected' : '' }}>
                        {{ $k->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Tanggal Masuk</label>
                <input type="text" class="w-full bg-gray-300 p-2 rounded" id="tanggalMasuk"
                    value="{{ $pindah->shri->tanggal_masuk }}" readonly>
            </div>

            <div>
                <label>Ruangan Tujuan</label>
                <select name="ruangan_tujuan" class="w-full p-2 rounded border" id="ruanganSelect">
                    <option value="">Pilih salah satu</option>
                    @if ($pindah->ruangan_tujuan)
                    <option value="{{ $pindah->ruangan->id }}" selected>
                        {{ $pindah->ruangan_tujuan }}
                    </option>
                    @endif
                </select>
            </div>


            <div>
                <label>Lama Dirawat</label>
                <input type="number" name="lama_dirawat" class="w-full p-2 bg-gray-300 rounded border" id="lamaDirawat"
                    value="{{ $pindah->lama_dirawat }}" readonly>
            </div>

            <div class="col-span-2">
                <label>Ruangan Sebelumnya</label>
                <input type="text" class="w-full bg-gray-300 p-2 rounded" value="{{ $pindah->shri->ruang_perawatan }}"
                    readonly />
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-6">
            <a href="{{ route('register-shri.pindah.view') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded">Batal</a>
            <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Perbarui</button>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
    $(function() {
        let kelasData = @json($kelas);

        // Populate ruangan saat halaman dibuka
        let selectedKelas = $("#kelasSelect").val();
        if (selectedKelas) {
            loadRuangan(selectedKelas);
        }

        // Jika kelas berubah → load ruangan
        $("#kelasSelect").on("change", function() {
            let kelasId = $(this).val();
            loadRuangan(kelasId);
        });

        // Hitung lama dirawat
        $("#tanggalPindah").on("change", function() {
            calculateLamaDirawat();
        });

        function loadRuangan(kelasId) {
            $("#ruanganSelect").empty().append('<option value="">Pilih salah satu</option>');
            if (!kelasId) return;

            let kelas = kelasData.find(k => k.id == kelasId);
            if (kelas && kelas.ruangans) {
                $.each(kelas.ruangans, function(i, ruangan) {
                    $("#ruanganSelect").append(
                        `<option value="${ruangan.id}">${ruangan.nama_ruangan}</option>`
                    );
                });

                let selectedRuanganId = "{{ $pindah->ruangan->id ?? '' }}";
                if (selectedRuanganId) {
                    $("#ruanganSelect").val(selectedRuanganId);
                }
            }
        }

        function calculateLamaDirawat() {
            let masuk = new Date($("#tanggalMasuk").val());
            let pindah = new Date($("#tanggalPindah").val());

            if (isNaN(masuk) || isNaN(pindah)) {
                $("#lamaDirawat").val('');
                return;
            }

            let diffTime = pindah.getTime() - masuk.getTime();
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            $("#lamaDirawat").val(diffDays > 0 ? diffDays : 0);
        }
    });
</script>
@endpush