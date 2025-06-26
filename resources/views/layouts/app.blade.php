<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Sistem Pelaporan SHRI' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 w-64 bg-[#2F3E52] text-white flex flex-col justify-between transform transition-transform duration-200 lg:translate-x-0 lg:static z-50"
            :class="{ '-translate-x-full': !sidebarOpen }">
            <div>
                <!-- Logo -->
                <div class="flex items-center justify-start gap-2 p-4 border-b border-white/20">
                    <img src="{{ asset('logo.png') }}" alt="RS Logo" class="w-10 h-10" />
                    <h1 class="text-lg font-semibold leading-tight">RSUD REDA BOLO</h1>
                </div>

                <!-- Close Button (mobile only) -->
                <div class="lg:hidden flex justify-end px-4 pt-2">
                    <button @click="sidebarOpen = false" class="text-white text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Navigation Menu -->
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
                            <a href="/laporan/kunjungan"
                                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                                <i class="fas fa-notes-medical mr-2"></i> Rekapitulasi Kunjungan RI
                            </a>
                            <a href="/laporan/shri"
                                class="block py-2 px-4 rounded hover:bg-white hover:text-[#2F3E52] transition">
                                <i class="fas fa-procedures mr-2"></i> Rekapitulasi SHRI
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="p-4 border-t border-white/20">
                <a href="/logout" class="flex items-center gap-2 text-sm hover:text-red-500 transition">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </aside>

        <div class="fixed inset-0  bg-opacity-10 z-40 lg:hidden" x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition.opacity></div>

        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-[#2F3E52] text-white flex justify-between items-center px-6 py-3 shadow">
                <button class="lg:hidden text-2xl" @click="sidebarOpen = true">
                    <i class="fas fa-bars"></i>
                </button>

                <h2 class="hidden lg:block text-md font-semibold">
                    Sistem Pelaporan Sensus Harian Rawat Inap
                </h2>
                <!-- User Info -->
                <div class="flex items-center gap-4">
                    <button class="hover:text-gray-300">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="hover:text-gray-300">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <img src="https://i.pravatar.cc/32?u=natalia" alt="User" class="rounded-full w-8 h-8" />
                        <div>
                            <p class="text-sm font-medium">Natalia</p>
                            <p class="text-xs text-gray-300">Perawat-01</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-scroll p-6">
                @yield('content')
            </main>
        </div>
    </div>


    @stack('js')

</body>

</html>
