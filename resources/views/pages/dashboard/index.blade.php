@extends('layouts.app')


@section('content')
    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <!-- Total Pasien Masuk -->
        <div
            class="bg-[#2ECC7170] rounded-xl border border-black flex flex-col justify-between h-32 transform transition-all hover:shadow-xl hover:scale-[1.02] cursor-pointer">
            <div class="p-4 text-center font-semibold">Total Pasien Masuk</div>
            <span class="font-bold text-lg text-center">20</span>

            <div class="bg-green-500/60 text-black rounded-b-xl px-4 py-2 flex justify-center items-center gap-2">
            </div>
        </div>

        <!-- Total Pasien Keluar -->
        <div
            class="bg-red-300 rounded-xl border border-black flex flex-col justify-between h-32 transform transition-all hover:shadow-xl hover:scale-[1.02] cursor-pointer">
            <div class="p-4 text-center font-semibold">Total Pasien Keluar</div>
            <span class="font-bold text-lg text-center">20</span>

            <div class="bg-red-600/60 text-black rounded-b-xl px-4 py-2 flex justify-center items-center gap-2">
            </div>
        </div>

        <!-- Tempat Tidur Tersedia -->
        <div
            class="bg-blue-200 rounded-xl border border-black flex flex-col justify-between h-32 transform transition-all hover:shadow-xl hover:scale-[1.02] cursor-pointer">
            <div class="p-4 text-center font-semibold">Tempat Tidur Tersedia</div>
            <div class="flex items-center justify-center gap-2">
                <span class="font-bold text-lg">20</span>
            </div>
            <div class="bg-blue-600/60 text-black rounded-b-xl px-4 py-2 flex justify-between items-center">
                <div class="font-semibold flex items-center gap-1">
                    <a href="{{ url('tempat-tidur', []) }}">
                        Lihat <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Waktu -->
        <div
            class="bg-yellow-200 rounded-xl border border-black flex flex-col justify-between h-32 transform transition-all hover:shadow-xl hover:scale-[1.02] cursor-pointer">
            <div class="p-4 text-center font-semibold">Waktu</div>
            <span class="text-sm bg-yellow-300/80 px-2 text-center rounded">Jun 10, 2024</span>
            <div class="bg-yellow-600/60 text-black rounded-b-xl px-4 py-2 flex flex-col items-center">

                <span class="text-lg font-semibold bg-yellow-500/70 px-3 rounded mt-1">9:41 AM</span>
            </div>
        </div>
    </div>
    <div class="mt-10 bg-white rounded-xl p-4 shadow-md w-full">
        <h2 class="text-xl font-bold mb-4 text-center">Perbandingan BOR, Alvos, BTO, TOI per Ruangan</h2>

        <!-- Responsive container -->
        <div class="relative w-full aspect-[2/1]">
            <canvas id="comparisonChart" class="w-full h-full absolute left-0 top-0"></canvas>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('comparisonChart').getContext('2d');
            const comparisonChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Ruang Interna', 'Ruang Anak', 'Ruang Materna', 'Ruang Bedah', 'NICU', 'ICU'],
                    datasets: [
                        {
                            label: 'BOR',
                            data: [60, 70, 55, 40, 80, 75],
                            backgroundColor: 'rgba(46, 204, 113, 0.7)'
                        },
                        {
                            label: 'Alvos',
                            data: [10, 12, 9, 6, 15, 13],
                            backgroundColor: 'rgba(231, 76, 60, 0.7)'
                        },
                        {
                            label: 'BTO',
                            data: [5, 4, 6, 3, 8, 7],
                            backgroundColor: 'rgba(52, 152, 219, 0.7)'
                        },
                        {
                            label: 'TOI',
                            data: [2, 1.5, 2.5, 3, 1, 1.2],
                            backgroundColor: 'rgba(241, 196, 15, 0.7)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        </script>
    @endpush

@endsection
