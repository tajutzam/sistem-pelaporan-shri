@extends('layouts.app')

@section('content')
<div class="mb-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    <!-- Total Pasien Masuk -->
    <div
        class="bg-[#2ECC7170] rounded-xl border border-black flex flex-col justify-between h-32 transform transition-all hover:shadow-xl hover:scale-[1.02] cursor-pointer">
        <div class="p-4 text-center font-semibold">Total Pasien Masuk</div>
        <span class="font-bold text-lg text-center">{{$pasienMasuk}}</span>

        <div class="bg-green-500/60 text-black rounded-b-xl px-4 py-2 flex justify-center items-center gap-2">
            <span class="text-sm">Hari Ini</span>
        </div>
    </div>

    <!-- Total Pasien Keluar -->
    <div
        class="bg-red-300 rounded-xl border border-black flex flex-col justify-between h-32 transform transition-all hover:shadow-xl hover:scale-[1.02] cursor-pointer">
        <div class="p-4 text-center font-semibold">Total Pasien Keluar</div>
        <span class="font-bold text-lg text-center">{{$pasienKeluar}}</span>

        <div class="bg-red-600/60 text-black rounded-b-xl px-4 py-2 flex justify-center items-center gap-2">
            <span class="text-sm">Hari Ini</span>
        </div>
    </div>

    <!-- Tempat Tidur Tersedia -->
    <div
        class="bg-blue-200 rounded-xl border border-black flex flex-col justify-between h-32 transform transition-all hover:shadow-xl hover:scale-[1.02] cursor-pointer">
        <div class="p-4 text-center font-semibold">Tempat Tidur Tersedia</div>
        <div class="flex items-center justify-center gap-2">
            <span class="font-bold text-lg">{{$tempatTidurTersedia}}</span>
        </div>
        <div class="bg-blue-600/60 text-black rounded-b-xl px-4 py-2 flex justify-between items-center">
            <div class="font-semibold flex items-center gap-1">
                <a href="{{ url('tempat-tidur', []) }}">
                    Lihat <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Waktu dengan Clock Real-time -->
    <div
        class="bg-yellow-200 rounded-xl border border-black flex flex-col justify-between h-32 transform transition-all hover:shadow-xl hover:scale-[1.02] cursor-pointer">
        <div class="p-4 text-center font-semibold">Waktu</div>
        <span id="currentDate" class="text-sm bg-yellow-300/80 px-2 text-center rounded">Loading...</span>
        <div class="bg-yellow-600/60 text-black rounded-b-xl px-4 py-2 flex flex-col items-center">
            <span id="currentTime" class="text-lg font-semibold bg-yellow-500/70 px-3 rounded mt-1">Loading...</span>
        </div>
    </div>
</div>

<!-- Filter untuk Chart -->
<div class="mt-6 bg-white rounded-xl p-4 shadow-md">
    <div class="flex flex-wrap gap-4 items-center mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter Periode:</label>
            <select id="monthFilter"
                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Bulan</option>
                <option value="1" {{date('n') == 1 ? 'selected' : ''}}>Januari</option>
                <option value="2" {{date('n') == 2 ? 'selected' : ''}}>Februari</option>
                <option value="3" {{date('n') == 3 ? 'selected' : ''}}>Maret</option>
                <option value="4" {{date('n') == 4 ? 'selected' : ''}}>April</option>
                <option value="5" {{date('n') == 5 ? 'selected' : ''}}>Mei</option>
                <option value="6" {{date('n') == 6 ? 'selected' : ''}}>Juni</option>
                <option value="7" {{date('n') == 7 ? 'selected' : ''}}>Juli</option>
                <option value="8" {{date('n') == 8 ? 'selected' : ''}}>Agustus</option>
                <option value="9" {{date('n') == 9 ? 'selected' : ''}}>September</option>
                <option value="10" {{date('n') == 10 ? 'selected' : ''}}>Oktober</option>
                <option value="11" {{date('n') == 11 ? 'selected' : ''}}>November</option>
                <option value="12" {{date('n') == 12 ? 'selected' : ''}}>Desember</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun:</label>
            <select id="yearFilter"
                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @for($year = date('Y'); $year >= 2019; $year--)
                <option value="{{$year}}" {{date('Y') == $year ? 'selected' : ''}}>{{$year}}</option>
                @endfor
            </select>
        </div>
        <div class="flex items-end">
            <button id="updateChart"
                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                Update Grafik
            </button>
        </div>
        <div class="flex items-end">
            <button id="refreshChart"
                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
        </div>
    </div>
</div>

<div class="mt-4 bg-white rounded-xl p-4 shadow-md w-full">
    <h2 class="text-xl font-bold mb-4 text-center">Indikator Pelayanan Rumah Sakit</h2>
    <p class="text-center text-gray-600 mb-4" id="chartPeriod">Data periode: <span
            id="currentPeriod">{{date('F Y')}}</span></p>

    <!-- Loading indicator -->
    <div id="chartLoading" class="hidden text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>

    <div class="relative w-full aspect-[2/1]">
        <canvas id="comparisonChart" class="w-full h-full absolute left-0 top-0"></canvas>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let comparisonChart;
    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    // Real-time Clock Function
    function updateClock() {
        const now = new Date();

        const dateOptions = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            weekday: 'short'
        };
        const formattedDate = now.toLocaleDateString('id-ID', dateOptions);

        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        const formattedTime = now.toLocaleTimeString('id-ID', timeOptions);

        document.getElementById('currentDate').textContent = formattedDate;
        document.getElementById('currentTime').textContent = formattedTime;
    }

    // Update clock setiap detik
    setInterval(updateClock, 1000);
    updateClock();

    // Initialize chart with data from server
    function initChart() {
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        const chartData = @json($chartData);

        comparisonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                        label: 'BOR (%)',
                        data: chartData.bor,
                        backgroundColor: 'rgba(46, 204, 113, 0.7)',
                        borderColor: 'rgba(46, 204, 113, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'AvLOS (Hari)',
                        data: chartData.avlos,
                        backgroundColor: 'rgba(231, 76, 60, 0.7)',
                        borderColor: 'rgba(231, 76, 60, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'BTO (Kali)',
                        data: chartData.bto,
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'TOI (Hari)',
                        data: chartData.toi,
                        backgroundColor: 'rgba(241, 196, 15, 0.7)',
                        borderColor: 'rgba(241, 196, 15, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y.toFixed(2);
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Update chart with new data
    async function updateChartData() {
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;

        // Show loading
        document.getElementById('chartLoading').classList.remove('hidden');
        document.getElementById('comparisonChart').style.opacity = '0.5';

        try {
            const response = await fetch('/api/dashboard-chart-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    month: month,
                    year: year
                })
            });

            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }

            const chartData = await response.json();

            // Update chart data
            comparisonChart.data.labels = chartData.labels;
            comparisonChart.data.datasets[0].data = chartData.bor;
            comparisonChart.data.datasets[1].data = chartData.avlos;
            comparisonChart.data.datasets[2].data = chartData.bto;
            comparisonChart.data.datasets[3].data = chartData.toi;

            comparisonChart.update('active');

            // Update period display
            let periodText = year;
            if (month) {
                periodText = `${monthNames[parseInt(month)]} ${year}`;
            }
            document.getElementById('currentPeriod').textContent = periodText;

        } catch (error) {
            console.error('Error updating chart:', error);
            alert('Gagal memperbarui grafik. Silakan coba lagi.');
        } finally {
            // Hide loading
            document.getElementById('chartLoading').classList.add('hidden');
            document.getElementById('comparisonChart').style.opacity = '1';
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        initChart();

        document.getElementById('updateChart').addEventListener('click', updateChartData);

        document.getElementById('refreshChart').addEventListener('click', function() {
            // Reset to current month/year
            document.getElementById('monthFilter').value = new Date().getMonth() + 1;
            document.getElementById('yearFilter').value = new Date().getFullYear();
            updateChartData();
        });

        // Auto-update on filter change
        document.getElementById('monthFilter').addEventListener('change', updateChartData);
        document.getElementById('yearFilter').addEventListener('change', updateChartData);
    });

    // Auto refresh every 5 minutes
    setInterval(function() {
        if (document.getElementById('monthFilter').value == new Date().getMonth() + 1 &&
            document.getElementById('yearFilter').value == new Date().getFullYear()) {
            updateChartData();
        }
    }, 300000); // 5 minutes
</script>
@endpush

@endsection