<nav class="mt-4 space-y-2 px-4">
    <a href="/dashboard" class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
        <i class="fas fa-home mr-2"></i> Dashboard
    </a>

    <!-- Register SHRI Dropdown -->
    <div x-data="{ open: false }">
        <button @click="open = !open"
            class="w-full flex justify-between items-center py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
            <span><i class="fas fa-user-plus mr-2"></i> Register SHRI</span>
            <i :class="{ 'rotate-90': open }" class="fas fa-chevron-right transition-transform"></i>
        </button>
        <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
            <a href="/register-shri/masuk"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-sign-in-alt mr-2"></i> Pasien Masuk
            </a>
            <a href="/register-shri/pindah"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-exchange-alt mr-2"></i> Pasien Pindah
            </a>
            <a href="/register-shri/keluar"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-sign-out-alt mr-2"></i> Pasien Keluar
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
                <i class="fas fa-notes-medical mr-2"></i> Rekapitulasi Kunjungan RI
            </a>
            <a href="/laporan/rekapitulasi"
                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                <i class="fas fa-procedures mr-2"></i> Rekapitulasi SHRI
            </a>
        </div>
    </div>
</nav>
