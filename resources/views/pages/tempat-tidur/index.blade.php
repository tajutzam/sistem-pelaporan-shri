@extends('layouts.app')

@section('content')

    <h2 class="font-bold text-2xl mb-4">Tempat Tidur Tersedia</h2>

    <div class="overflow-auto">
        <table class="min-w-max table-auto border-collapse border border-black text-center text-sm w-full">
            <thead>
                <tr>
                    <th rowspan="2" class="border border-black px-2 py-1 bg-gray-200">Jumlah</th>
                    <th colspan="4" class="border border-black px-2 py-1 bg-gray-200">Interna</th>
                    <th colspan="4" class="border border-black px-2 py-1 bg-gray-200">Anak</th>
                    <th colspan="4" class="border border-black px-2 py-1 bg-gray-200">Bedah</th>
                    <th colspan="4" class="border border-black px-2 py-1 bg-gray-200">Materna</th>
                    <th rowspan="2" class="border border-black px-2 py-1 bg-gray-200">NICU</th>
                    <th rowspan="2" class="border border-black px-2 py-1 bg-gray-200">ICU</th>
                </tr>
                <tr>
                    @php $kelas = ['I', 'II', 'III', 'VIP']; @endphp
                    @for ($i = 0; $i < 4 * 4; $i++)
                        <th class="border border-black px-2 py-1">{{ $kelas[$i % 4] }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                <tr class="bg-white">
                    <td class="border border-black px-2 py-1 font-semibold">Total</td>
                    @foreach ([2, 7, 9, 0, 2, 4, 18, 0, 2, 9, 12, 0, 2, 6, 9, 0, 5, 2] as $val)
                        <td class="border border-black px-2 py-1">{{ $val }}</td>
                    @endforeach
                </tr>
                <tr class="bg-gray-50">
                    <td class="border border-black px-2 py-1 font-semibold">Terisi</td>
                    @foreach ([1, 3, 2, 0, 0, 2, 4, 0, 1, 4, 3, 0, 1, 2, 3, 0, 2, 1] as $val)
                        <td class="border border-black px-2 py-1">{{ $val }}</td>
                    @endforeach
                </tr>
                <tr class="bg-white">
                    <td class="border border-black px-2 py-1 font-semibold">Tersedia</td>
                    @foreach ([1, 4, 7, 0, 2, 2, 14, 0, 1, 5, 9, 0, 1, 4, 6, 0, 3, 1] as $val)
                        <td class="border border-black px-2 py-1">{{ $val }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        <div class="flex justify-self-end">
            <a href="{{ url('dashboard', []) }}"
                class="bg-[#34495E] text-white px-4 py-2 rounded-lg mt-3 self-end">Kembali</a>
        </div>
    </div>

@endsection
