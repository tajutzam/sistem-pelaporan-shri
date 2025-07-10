@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6 underline">Grafik Kunjungan Pasien</h2>

    <div class="space-y-6">
        <!-- Section Filter -->
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">Filter</h3>
        </div>

        <!-- Filter Form -->
        <form class="flex flex-wrap items-end gap-4">
            <!-- Ruangan -->
            <div class="flex flex-col">
                <label for="ruangan" class="mb-1 text-sm font-medium text-gray-700">
                    Ruangan
                </label>
                <input type="text" id="ruangan" name="ruangan" placeholder="Masukkan ruangan"
                    class="bg-white border border-gray-300 rounded px-3 py-2 w-48 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Tahun -->
            <div class="flex flex-col">
                <label for="tahun" class="mb-1 text-sm font-medium text-gray-700">
                    Tahun
                </label>
                <select id="tahun" name="tahun"
                    class="bg-white border border-gray-300 rounded px-3 py-2 w-32 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @for ($i = now()->year; $i >= 2000; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <!-- Tombol Tampilkan -->
            <div>
                <button type="submit"
                    class="bg-[#34495E] text-white rounded-lg px-5 py-2.5 hover:bg-[#2c3e50] transition font-medium">
                    Tampilkan
                </button>
            </div>
        </form>

        <!-- Grafik Kunjungan -->
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">Grafik Kunjungan Pasien (Dummy)</h3>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <canvas id="kunjunganChart" height="100"></canvas>
        </div>

        <!-- Tombol Cetak -->
        <div class="flex justify-end">
            <button class="bg-[#34B3AE] text-white px-5 py-2 rounded-lg hover:bg-[#2ca8a3] transition">
                Cetak Grafik
            </button>
        </div>
    </div>
@endsection



@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('kunjunganChart').getContext('2d');
        const kunjunganChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
                ],
                datasets: [
                    {
                        label: 'Pasien Baru',
                        data: [12, 19, 10, 14, 18, 20, 22, 19, 15, 17, 16, 21],
                        backgroundColor: '#3498db'
                    },
                    {
                        label: 'Pasien Umum',
                        data: [22, 25, 18, 20, 27, 30, 28, 26, 24, 25, 29, 31],
                        backgroundColor: '#2ecc71'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
