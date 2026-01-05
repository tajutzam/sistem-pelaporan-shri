@extends('layouts.app')

@section('content')
    <h2 class="font-bold text-2xl mb-4 bg-yellow-400 text-black px-3 py-1 inline-block rounded">
        Tempat Tidur Tersedia
    </h2>

    {{-- Filter Ruangan --}}
    <form method="GET" action="{{ route('admin.tempat-tidur') }}" class="flex items-end gap-4 mb-4">
        <div class="flex flex-col">
            <label for="ruangan" class="text-sm font-semibold mb-1">Ruangan</label>
            <select id="ruangan" name="ruangan"
                class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#34495E]">
                <option value="all" {{ $selectedRuangan == 'all' ? 'selected' : '' }}>Semua Ruangan</option>
                @foreach ($ruanganList as $r)
                    <option value="{{ $r->nama_ruangan }}" {{ $selectedRuangan == $r->nama_ruangan ? 'selected' : '' }}>
                        {{ $r->nama_ruangan }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-[#34495E] text-white px-4 py-2 rounded hover:bg-[#2c3e50] transition">
            Tampilkan
        </button>
    </form>


    <div class="overflow-auto mt-4">
        <table class="min-w-max table-auto border-collapse border border-black text-center text-sm w-full">
            <thead>
                <tr>
                    <th rowspan="2" class="border border-black px-2 py-1 bg-gray-200">Jumlah</th>
                    <th colspan="{{ $kelasList->count() }}" class="border border-black px-2 py-1 bg-gray-200">
                        Kelas Perawatan ({{ $selectedRuangan }})
                    </th>
                </tr>
                <tr>
                    @foreach ($kelasList as $kelas)
                        <th class="border border-black px-2 py-1">{{ $kelas->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-black px-2 py-1 font-semibold">Total</td>
                    @foreach ($kelasList as $kelas)
                        <td class="border border-black px-2 py-1">{{ $data[$kelas->id]['total'] ?? 0 }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="border border-black px-2 py-1 font-semibold">Terisi</td>
                    @foreach ($kelasList as $kelas)
                        <td class="border border-black px-2 py-1">{{ $data[$kelas->id]['terisi'] ?? 0 }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="border border-black px-2 py-1 font-semibold">Tersedia</td>
                    @foreach ($kelasList as $kelas)
                        <td class="border border-black px-2 py-1">{{ $data[$kelas->id]['tersedia'] ?? 0 }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>

        <div class="flex justify-end">
            <a href="{{ url('dashboard') }}" class="bg-[#34495E] text-white px-4 py-2 rounded-lg mt-3">
                Kembali
            </a>
        </div>
    </div>
@endsection
