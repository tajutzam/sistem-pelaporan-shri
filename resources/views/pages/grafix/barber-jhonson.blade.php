@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Grafik Barber Johnson</h1>

        <div class="bg-slate-600 rounded-lg p-4 mb-6">
            <h2 class="text-white font-medium mb-4">Filter :</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-white text-sm mb-2">Ruangan :</label>
                    <select id="ruanganSelect"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Ruangan --</option>
                        @foreach ($ruangans as $ruangan)
                            <option value="{{ $ruangan->nama_ruangan }}">{{ $ruangan->nama_ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-white text-sm mb-2">Tahun :</label>
                    <select id="tahunSelect"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Tahun</option>
                        @for ($year = date('Y'); $year >= 2019; $year--)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-white text-sm mb-2">Bulan</label>
                    <select id="bulanSelect"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Bulan</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div>
                    <button id="tampilkanBtn"
                        class="w-full bg-slate-700 hover:bg-slate-800 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Load Data
                    </button>
                </div>
            </div>
        </div>

        <div id="loadingIndicator" class="hidden text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-slate-600"></div>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>

        <div id="mainContent" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-slate-600 rounded-lg p-4">
                    <h3 class="text-white font-medium mb-4">Indikator Pelayanan Rumah Sakit</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-white text-sm mb-1">BOR (%)</label>
                            <input type="number" id="borInput" step="0.01"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0">
                            <small class="text-gray-300 text-xs">Bed Occupancy Rate</small>
                        </div>

                        <div>
                            <label class="block text-white text-sm mb-1">AvLOS (Hari)</label>
                            <input type="number" id="avlosInput" step="0.01"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0">
                            <small class="text-gray-300 text-xs">Average Length of Stay</small>
                        </div>

                        <div>
                            <label class="block text-white text-sm mb-1">BTO (Kali)</label>
                            <input type="number" id="btoInput" step="0.01"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0">
                            <small class="text-gray-300 text-xs">Bed Turn Over</small>
                        </div>

                        <div>
                            <label class="block text-white text-sm mb-1">TOI (Hari)</label>
                            <input type="number" id="toiInput" step="0.01"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0">
                            <small class="text-gray-300 text-xs">Turn Over Interval</small>
                        </div>
                    </div>

                    <div class="space-y-2 mt-6">
                        <button id="previewChart"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Preview Chart
                        </button>
                        <button id="resetValues"
                            class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Reset Values
                        </button>

                        <button id="printChart"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            <i class="fas fa-print mr-2"></i>Cetak Laporan
                        </button>
                    </div>

                    <div class="mt-4 p-2 bg-slate-700 rounded">
                        <p class="text-white text-xs text-center">
                            Status: <span id="dataStatus" class="font-medium text-yellow-400">Manual Input</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="bg-slate-600 rounded-lg p-4">
                    <h3 id="chartTitle" class="text-white font-medium text-center mb-4">Grafik Barber Johnson RSUD Reda Bolo
                    </h3>

                    <div class="bg-gray-800 rounded-lg p-4">
                        <h4 id="chartSubtitle" class="text-white text-center mb-4">Masukkan nilai indikator dan klik Preview
                            Chart</h4>

                        <div class="relative bg-gray-700 rounded h-96 p-4">
                            <div class="ml-8 mr-20 mt-4 mb-12 h-full relative">
                                <canvas id="barberChart" class="w-full h-full"></canvas>
                                <div id="efisiensiPoint"
                                    class="absolute top-4 right-4 bg-gray-800 bg-opacity-90 text-center p-3 rounded-lg border border-gray-600">
                                    <p class="text-white text-sm mb-1 font-medium">Koordinat Efisiensi</p>
                                    <div id="efisiensiValue" class="text-red-400 font-bold text-sm">-</div>
                                    <div id="efisiensiStatus" class="text-xs mt-1">
                                        <span id="statusText" class="text-gray-300">Status: Belum dianalisis</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap justify-center gap-4 text-xs">
                            <div class="flex items-center">
                                <div class="w-4 h-0.5 bg-orange-400 mr-2"></div>
                                <span class="text-white">BOR Line (Horizontal)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-0.5 bg-blue-400 mr-2"></div>
                                <span class="text-white">BTO Line (Vertical)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-white">Titik Efisiensi</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-0.5 bg-green-400 mr-2"></div>
                                <span class="text-white">Zona Efisiensi Ideal</span>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-slate-700 rounded">
                            <p class="text-gray-300 text-xs text-center">
                                💡 <strong>Tips:</strong> Zona efisiensi ideal berada pada TOI: 1-3 hari dan AvLOS: 2-6
                                hari. Titik merah menunjukkan posisi efisiensi aktual berdasarkan input Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <script>
        let barberChart;
        let currentData = null;
        let isManualMode = true;
        const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        document.addEventListener('DOMContentLoaded', function() {
            initChart();
            // Event listeners
            document.getElementById('tampilkanBtn').addEventListener('click', loadDataAndUpdateChart);
            document.getElementById('previewChart').addEventListener('click', previewChart);
            document.getElementById('resetValues').addEventListener('click', resetValues);
            document.getElementById('printChart').addEventListener('click', printChart);
            // Real-time input change listeners
            ['borInput', 'avlosInput', 'btoInput', 'toiInput'].forEach(id => {
                document.getElementById(id).addEventListener('input', handleInputChange);
            });

            // Initialize with default values for demo
            resetValues();
        });

        // Handle input changes
        function handleInputChange() {
            if (isManualMode === false) {
                updateDataStatus('Manual Modified');
            }
        }

        // Update data status
        function updateDataStatus(status) {
            const statusElement = document.getElementById('dataStatus');
            statusElement.textContent = status;
            if (status.includes('Database')) {
                statusElement.className = 'font-medium text-green-400';
            } else if (status.includes('Modified')) {
                statusElement.className = 'font-medium text-orange-400';
            } else {
                statusElement.className = 'font-medium text-yellow-400';
            }
        }

        // Calculate dynamic chart bounds
        function calculateChartBounds(indikator) {
            const toi = parseFloat(indikator.toi) || 0;
            const avlos = parseFloat(indikator.avlos) || 0;
            const xPadding = Math.max(Math.abs(toi) * 0.4, 2);
            const yPadding = Math.max(Math.abs(avlos) * 0.4, 2);
            const xMin = Math.min(toi - xPadding, 0);
            const xMax = Math.max(toi + xPadding, 5);
            const yMin = Math.min(avlos - yPadding, 0);
            const yMax = Math.max(avlos + yPadding, 8);
            return {
                xMin,
                xMax,
                yMin,
                yMax
            };
        }

        // Initialize empty chart
        function initChart() {
            const ctx = document.getElementById('barberChart').getContext('2d');

            barberChart = new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'point',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    return `${label}: (${context.parsed.x.toFixed(2)}, ${context.parsed.y.toFixed(2)})`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            title: {
                                display: true,
                                text: 'TOI (Hari)',
                                color: 'white',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                color: 'white',
                                callback: function(value) {
                                    return value.toFixed(1);
                                }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'AvLOS (Hari)',
                                color: 'white',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                color: 'white',
                                callback: function(value) {
                                    return value.toFixed(1);
                                }
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });
        }

        async function loadDataAndUpdateChart() {
            const ruanganName = document.getElementById('ruanganSelect').value;
            const tahun = document.getElementById('tahunSelect').value;
            const bulan = document.getElementById('bulanSelect').value;

            if (!tahun) {
                alert('Pilih tahun terlebih dahulu');
                return;
            }

            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('mainContent').style.opacity = '0.5';

            try {
                const response = await fetch('/api/barber-johnson-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ruangan: ruanganName,
                        tahun: tahun,
                        bulan: bulan
                    })
                });

                // 1. Ambil JSON hanya satu kali
                const data = await response.json();

                // 2. Cek status respon
                if (!response.ok) {
                    console.error('Server Error:', data);
                    throw new Error(data.message || 'Gagal memuat data');
                }

                // Debugging untuk melihat data yang masuk (khususnya saat "Semua Ruangan")
                console.log("Data diterima:", data);

                currentData = data;
                isManualMode = false;

                // 3. Update input fields (Gunakan pengecekan optional chaining agar tidak crash jika null)
                document.getElementById('borInput').value = data.indikator?.bor?.toFixed(2) || 0;
                document.getElementById('avlosInput').value = data.indikator?.avlos?.toFixed(2) || 0;
                document.getElementById('btoInput').value = data.indikator?.bto?.toFixed(2) || 0;
                document.getElementById('toiInput').value = data.indikator?.toi?.toFixed(2) || 0;

                // 4. Update chart
                updateChart(data.indikator);

                const displayRuangan = ruanganName || 'Seluruh Ruangan';
                updateChartTitle(displayRuangan, tahun, bulan);
                updateDataStatus('Database Data');

            } catch (error) {
                console.error('Error detail:', error);
                alert('Terjadi kesalahan saat memuat data: ' + error.message);
            } finally {
                document.getElementById('loadingIndicator').classList.add('hidden');
                document.getElementById('mainContent').style.opacity = '1';
            }
        }

        function previewChart() {
            const bor = parseFloat(document.getElementById('borInput').value) || 0;
            const avlos = parseFloat(document.getElementById('avlosInput').value) || 0;
            const bto = parseFloat(document.getElementById('btoInput').value) || 0;
            const toi = parseFloat(document.getElementById('toiInput').value) || 0;

            if (bor === 0 && avlos === 0 && bto === 0 && toi === 0) {
                alert('Masukkan setidaknya satu nilai indikator');
                return;
            }

            const indikator = {
                bor,
                avlos,
                bto,
                toi
            };
            updateChart(indikator);
            updateChartTitle('Manual Input', '', '');
            updateDataStatus('Manual Modified');
        }

        // Reset values to default
        function resetValues() {
            document.getElementById('borInput').value = '65';
            document.getElementById('avlosInput').value = '4';
            document.getElementById('btoInput').value = '45';
            document.getElementById('toiInput').value = '2';
            isManualMode = true;
            updateDataStatus('Manual Input');
            updateChartTitle('Default Values', '', '');
            const indikator = {
                bor: 65,
                avlos: 4,
                bto: 45,
                toi: 2
            };
            updateChart(indikator);
        }

        // Update chart with dynamic bounds
        function updateChart(indikator) {
            const avlos = parseFloat(indikator.avlos) || 0;
            const toi = parseFloat(indikator.toi) || 0;

            // Calculate bounds
            const bounds = calculateChartBounds(indikator);

            // Update scales
            barberChart.options.scales.x.min = bounds.xMin;
            barberChart.options.scales.x.max = bounds.xMax;
            barberChart.options.scales.y.min = bounds.yMin;
            barberChart.options.scales.y.max = bounds.yMax;

            // Efficiency zone fixed constants
            const effZone = {
                xMin: 1,
                xMax: 3,
                yMin: 2,
                yMax: 6
            };

            // Update datasets
            barberChart.data.datasets = [
                // BOR Line (Horizontal)
                {
                    label: 'BOR Line',
                    data: [{
                        x: bounds.xMin,
                        y: avlos
                    }, {
                        x: bounds.xMax,
                        y: avlos
                    }],
                    borderColor: '#FB923C',
                    borderWidth: 3,
                    pointRadius: 0,
                    showLine: true,
                    tension: 0
                },
                // TOI Line (Vertical)
                {
                    label: 'TOI Line',
                    data: [{
                        x: toi,
                        y: bounds.yMin
                    }, {
                        x: toi,
                        y: bounds.yMax
                    }],
                    borderColor: '#60A5FA',
                    borderWidth: 3,
                    pointRadius: 0,
                    showLine: true,
                    tension: 0
                },
                // Efficiency Zone Box
                {
                    label: 'Zona Efisiensi',
                    data: [{
                            x: effZone.xMin,
                            y: effZone.yMin
                        },
                        {
                            x: effZone.xMax,
                            y: effZone.yMin
                        },
                        {
                            x: effZone.xMax,
                            y: effZone.yMax
                        },
                        {
                            x: effZone.xMin,
                            y: effZone.yMax
                        },
                        {
                            x: effZone.xMin,
                            y: effZone.yMin
                        }
                    ],
                    borderColor: '#4ADE80',
                    backgroundColor: 'rgba(74, 222, 128, 0.1)',
                    borderWidth: 2,
                    pointRadius: 0,
                    showLine: true,
                    fill: true,
                    tension: 0
                },
                // Efficiency Point
                {
                    label: 'Titik Efisiensi',
                    data: [{
                        x: toi,
                        y: avlos
                    }],
                    borderColor: '#EF4444',
                    backgroundColor: '#EF4444',
                    pointRadius: 10,
                    pointHoverRadius: 12,
                    showLine: false
                }
            ];

            barberChart.update('none');

            // Update efficiency display
            updateEfficiencyDisplay(toi, avlos, effZone);
        }

        // Update efficiency display
        function updateEfficiencyDisplay(toi, avlos, effZone) {
            const efisiensiEl = document.getElementById('efisiensiValue');
            const statusEl = document.getElementById('statusText');
            efisiensiEl.textContent = `(${toi.toFixed(2)}, ${avlos.toFixed(2)})`;

            const isEfficient = (
                toi >= effZone.xMin && toi <= effZone.xMax &&
                avlos >= effZone.yMin && avlos <= effZone.yMax
            );

            if (isEfficient) {
                efisiensiEl.className = 'text-green-400 font-bold text-sm';
                statusEl.innerHTML = '<span class="text-green-400">Status: Efisien ✓</span>';
            } else {
                efisiensiEl.className = 'text-red-400 font-bold text-sm';
                statusEl.innerHTML = '<span class="text-red-400">Status: Tidak Efisien ✗</span>';
            }
        }

        // Update chart title
        function updateChartTitle(ruanganNama, tahun, bulan) {
            const chartTitle = document.getElementById('chartTitle');
            const chartSubtitle = document.getElementById('chartSubtitle');

            if (ruanganNama === 'Manual Input' || ruanganNama === 'Default Values') {
                chartTitle.textContent = `Grafik Barber Johnson - ${ruanganNama}`;
                chartSubtitle.textContent = 'Preview berdasarkan input manual';
            } else {
                chartTitle.textContent = `Grafik Barber Johnson - ${ruanganNama}`;
                let periode = tahun;
                if (bulan) {
                    periode = `${monthNames[parseInt(bulan)]} ${tahun}`;
                }
                chartSubtitle.textContent = `Periode: ${periode}`;
            }
        }

        function printChart() {
            if (!barberChart || barberChart.data.datasets.length === 0) {
                alert('Tidak ada grafik untuk dicetak. Silakan preview chart terlebih dahulu.');
                return;
            }

            const canvas = document.getElementById('barberChart');
            const dataURL = canvas.toDataURL('image/png', 1.0);
            const chartTitle = document.getElementById('chartTitle').textContent;
            const chartSubtitle = document.getElementById('chartSubtitle').textContent;

            const bor = document.getElementById('borInput').value;
            const avlos = document.getElementById('avlosInput').value;
            const bto = document.getElementById('btoInput').value;
            const toi = document.getElementById('toiInput').value;
            const status = document.getElementById('dataStatus').textContent;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                        <html>
                            <head>
                                <title>${chartTitle}</title>
                                <style>
                                    body {
                                        margin: 0;
                                        padding: 20px;
                                        text-align: center;
                                        font-family: Arial, sans-serif;
                                    }
                                    h1 {
                                        color: #333;
                                        margin-bottom: 10px;
                                        font-size: 24px;
                                    }
                                    h2 {
                                        color: #666;
                                        margin-bottom: 20px;
                                        font-size: 18px;
                                        font-weight: normal;
                                    }
                                    .status {
                                        background: #f0f9ff;
                                        border: 1px solid #0ea5e9;
                                        padding: 8px;
                                        margin: 10px 0;
                                        border-radius: 5px;
                                        color: #0ea5e9;
                                        font-weight: bold;
                                    }
                                    img {
                                        max-width: 100%;
                                        height: auto;
                                        margin: 20px 0;
                                        border: 1px solid #ddd;
                                        border-radius: 8px;
                                    }
                                    .indicators {
                                        display: flex;
                                        justify-content: center;
                                        gap: 30px;
                                        margin: 20px 0;
                                        flex-wrap: wrap;
                                    }
                                    .indicator {
                                        text-align: center;
                                        padding: 15px;
                                        border: 2px solid #ddd;
                                        border-radius: 8px;
                                        min-width: 100px;
                                        background: #f8fafc;
                                    }
                                    .indicator-label {
                                        font-weight: bold;
                                        margin-bottom: 8px;
                                        color: #475569;
                                    }
                                    .indicator-value {
                                        font-size: 20px;
                                        color: #1e293b;
                                        font-weight: bold;
                                    }
                                    .footer {
                                        margin-top: 30px;
                                        color: #666;
                                        font-size: 12px;
                                        border-top: 2px solid #ddd;
                                        padding-top: 15px;
                                        text-align: center;
                                    }
                                    @media print {
                                        body { padding: 10px; }
                                        .status { background: white !important; }
                                    }
                                </style>
                            </head>
                            <body>
                                <h1>${chartTitle}</h1>
                                <h2>${chartSubtitle}</h2>
                                <div class="status">Sumber Data: ${status}</div>

                                <div class="indicators">
                                    <div class="indicator">
                                        <div class="indicator-label">BOR (%)</div>
                                        <div class="indicator-value">${bor}</div>
                                    </div>
                                    <div class="indicator">
                                        <div class="indicator-label">AvLOS (Hari)</div>
                                        <div class="indicator-value">${avlos}</div>
                                    </div>
                                    <div class="indicator">
                                        <div class="indicator-label">BTO (Kali)</div>
                                        <div class="indicator-value">${bto}</div>
                                    </div>
                                    <div class="indicator">
                                        <div class="indicator-label">TOI (Hari)</div>
                                        <div class="indicator-value">${toi}</div>
                                    </div>
                                </div>

                                <img src="${dataURL}" alt="Barber Johnson Chart">

                                <div class="footer">
                                    <p><strong>Dicetak pada:</strong> ${new Date().toLocaleString('id-ID')}</p>
                                    <p>RSUD Reda Bolo - Sistem Informasi Manajemen Rumah Sakit</p>
                                    <p><em>Zona efisiensi ideal: TOI 1-3 hari, AvLOS 2-6 hari</em></p>
                                </div>
                            </body>
                        </html>
                    `);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
            }, 1000);
        }
    </script>
@endsection
