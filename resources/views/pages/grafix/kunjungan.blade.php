@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6 underline">Grafik Kunjungan Pasien</h2>

    <div class="space-y-6">
        <!-- Section Filter -->
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">Filter</h3>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('grafik.kunjungan') }}" class="flex flex-wrap items-end gap-4">
            <!-- Ruangan -->
            <div class="flex flex-col">
                <label for="ruangan" class="mb-1 text-sm font-medium text-gray-700">
                    Ruangan
                </label>
                <select id="ruangan" name="ruangan"
                    class="bg-white border border-gray-300 rounded px-3 py-2 w-48 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Semua Ruangan</option>
                    @foreach ($ruangans as $r)
                        <option value="{{ $r->nama_ruangan }}" {{ request('ruangan') == $r->nama_ruangan ? 'selected' : '' }}>
                            {{ $r->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tahun -->
            <div class="flex flex-col">
                <label for="tahun" class="mb-1 text-sm font-medium text-gray-700">
                    Tahun
                </label>
                <select id="tahun" name="tahun"
                    class="bg-white border border-gray-300 rounded px-3 py-2 w-32 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @for ($i = now()->year; $i >= 2000; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}
                        </option>
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

            <!-- Tombol Reset -->
            <div>
                <a href="{{ route('grafik.kunjungan') }}"
                    class="bg-gray-500 text-white rounded-lg px-5 py-2.5 hover:bg-gray-600 transition font-medium inline-block">
                    Reset
                </a>
            </div>
        </form>

        <!-- Summary Statistics -->
        @if (isset($dataKunjungan['summary']))
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-100 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800">Total Pasien Baru</h4>
                    <p class="text-2xl font-bold text-blue-600">{{ $dataKunjungan['summary']['total_baru'] }}</p>
                </div>
                <div class="bg-green-100 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-800">Total Pasien Lama</h4>
                    <p class="text-2xl font-bold text-green-600">{{ $dataKunjungan['summary']['total_lama'] }}</p>
                </div>
                <div class="bg-purple-100 border border-purple-200 rounded-lg p-4">
                    <h4 class="font-semibold text-purple-800">Total Kunjungan</h4>
                    <p class="text-2xl font-bold text-purple-600">{{ $dataKunjungan['summary']['total_keseluruhan'] }}</p>
                </div>
                <div class="bg-orange-100 border border-orange-200 rounded-lg p-4">
                    <h4 class="font-semibold text-orange-800">Rata-rata Bulanan</h4>
                    <p class="text-2xl font-bold text-orange-600">{{ $dataKunjungan['summary']['rata_rata_bulanan'] }}</p>
                </div>
            </div>
        @endif

        <!-- Info Filter -->
        @if (request('ruangan') || request('tahun'))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 mb-2">Filter Aktif:</h4>
                <div class="text-sm text-blue-700">
                    @if (request('ruangan'))
                        <span class="inline-block bg-blue-200 px-2 py-1 rounded mr-2">
                            Ruangan: {{ request('ruangan') }}
                        </span>
                    @endif
                    <span class="inline-block bg-blue-200 px-2 py-1 rounded">
                        Tahun: {{ $tahun }}
                    </span>
                </div>
                @if (isset($dataKunjungan['summary']['bulan_tertinggi']))
                    <div class="mt-2 text-sm text-blue-700">
                        <strong>Bulan Tertinggi:</strong> {{ $dataKunjungan['summary']['bulan_tertinggi']['bulan'] }}
                        ({{ $dataKunjungan['summary']['bulan_tertinggi']['jumlah'] }} kunjungan)
                    </div>
                @endif
            </div>
        @endif

        <!-- Grafik Kunjungan -->
        <div class="bg-[#34495E] px-4 py-3 rounded-lg">
            <h3 class="text-white text-lg font-semibold">
                Grafik Kunjungan Pasien Tahun {{ $tahun }}
                @if (request('ruangan'))
                    - {{ request('ruangan') }}
                @endif
            </h3>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <canvas id="kunjunganChart" height="100"></canvas>
        </div>
        <!-- Tombol Actions -->
        <div class="flex justify-end space-x-3">
            <button onclick="downloadChart()"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-download mr-2"></i>Download Chart
            </button>
            <button onclick="printChart()"
                class="bg-[#34B3AE] text-white px-5 py-2 rounded-lg hover:bg-[#2ca8a3] transition">
                <i class="fas fa-print mr-2"></i>Cetak Grafik
            </button>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let kunjunganChart;

        const ctx = document.getElementById('kunjunganChart').getContext('2d');
        kunjunganChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($dataKunjungan['labels']),
                datasets: [{
                    label: 'Pasien Baru',
                    data: @json($dataKunjungan['pasien_baru']),
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 1
                },
                {
                    label: 'Pasien Lama',
                    data: @json($dataKunjungan['pasien_lama']),
                    backgroundColor: '#2ecc71',
                    borderColor: '#27ae60',
                    borderWidth: 1
                },
                {
                    label: 'Laki-laki (L)',
                    data: @json($dataKunjungan['laki_laki']),
                    backgroundColor: '#818cf8',
                    stack: 'gender'
                },
                {
                    label: 'Perempuan (P)',
                    data: @json($dataKunjungan['perempuan']),
                    backgroundColor: '#fb7185',
                    stack: 'gender'
                }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Grafik Kunjungan Pasien Tahun {{ $tahun }}{{ request('ruangan') ? ' - ' . request('ruangan') : '' }}'
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                // Tooltip kustom
                plugins: {
                    tooltip: {
                        callbacks: {
                            
                        }
                    }
                }
            }
        });

        // Function untuk download chart
        function downloadChart() {
            const link = document.createElement('a');
            link.download = 'grafik-kunjungan-{{ $tahun }}.png';
            link.href = kunjunganChart.toBase64Image();
            link.click();
        }

        function printChart() {
            const canvas = document.getElementById('kunjunganChart');
            const dataURL = canvas.toDataURL('image/png', 1.0);

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("grafik.kunjungan.print") }}'; // Pastikan route ini ada
            form.target = '_blank';

            const params = {
                _token: '{{ csrf_token() }}',
                ruangan: document.getElementById('ruangan').value || 'Semua Ruangan',
                tahun: '{{ $tahun }}',
                chartImage: dataURL,
                labels: @json($dataKunjungan['labels']),
                baru: @json($dataKunjungan['pasien_baru']),
                lama: @json($dataKunjungan['pasien_lama']),
                laki: @json($dataKunjungan['laki_laki']),
                perempuan: @json($dataKunjungan['perempuan'])
            };

            for (const key in params) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = typeof params[key] === 'object' ? JSON.stringify(params[key]) : params[key];
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

    </script>
@endpush