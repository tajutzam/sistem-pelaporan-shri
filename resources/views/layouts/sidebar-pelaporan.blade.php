<nav class="mt-4 space-y-2 px-4">
    <a href="/dashboard" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
        <i class="fas fa-home mr-2"></i> Dashboard
    </a>

    <!-- Data Admin Dropdown -->
    <div x-data="{ open: false }">
        <button @click="open = !open"
            class="w-full flex justify-between items-center py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
            <span><i class="fas fa-database mr-2"></i> Data Admin</span>
            <i :class="{ 'rotate-90': open }" class="fas fa-chevron-right transition-transform"></i>
        </button>
        <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
            <a href="/data/pengguna" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-users mr-2"></i> Data Pengguna
            </a>
            <a href="/data/ruangan" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-door-open mr-2"></i> Data Ruangan
            </a>
            <a href="/data/penjaminan" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-shield-alt mr-2"></i> Data Penjaminan
            </a>
            <a href="/data/pasien" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-procedures mr-2"></i> Data Pasien
            </a>
            <a href="/data/dpjp" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-user-md mr-2"></i> Data DPJP
            </a>
            <a href="/data/diagnosa" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-notes-medical mr-2"></i> Data Diagnosa
            </a>
            <a href="/data/hak-akses-perawat"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-notes-medical mr-2"></i> Data Hak Akses Perawat
            </a>
        </div>
    </div>

    <!-- Laporan Dropdown -->
    <div x-data="{ open: false }">
        <button @click="open = !open"
            class="w-full flex justify-between items-center py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
            <span><i class="fas fa-file-alt mr-2"></i> Laporan</span>
            <i :class="{ 'rotate-90': open }" class="fas fa-chevron-right transition-transform"></i>
        </button>
        <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
            <a href="/laporan/kunjungan" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-notes-medical mr-2"></i> Rekapitulasi Kunjungan
            </a>
            <a href="/laporan/rekapitulasi"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-procedures mr-2"></i> Rekapitulasi SHRI
            </a>
            <a href="/laporan/indikator-pelayanan"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-chart-line mr-2"></i> Indikator Pelayanan Rumah Sakit
            </a>
            <a href="/laporan/10-penyakit"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-list-ol mr-2"></i> 20 Besar Penyakit
            </a>
        </div>
    </div>

    <!-- Grafik Dropdown -->
    <div x-data="{ open: false }">
        <button @click="open = !open"
            class="w-full flex justify-between items-center py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
            <span><i class="fas fa-chart-bar mr-2"></i> Grafik</span>
            <i :class="{ 'rotate-90': open }" class="fas fa-chevron-right transition-transform"></i>
        </button>
        <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
            <a href="/grafik/barber-johnson"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-chart-bar mr-2"></i> Grafik Barber Johnson
            </a>
            <a href="/grafik/kunjungan-pasien"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-chart-area mr-2"></i> Grafik Kunjungan Pasien
            </a>
        </div>
    </div>
</nav>