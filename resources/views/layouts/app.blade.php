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
                @if (checkRole('perawat'))
                    @include('layouts.sidebar-perawat')
                @endif

                @if (checkRole('kepala'))
                    @include('layouts.sidebar-kepala')
                @endif

                @if (checkRole('pelaporan'))
                    @include('layouts.sidebar-pelaporan')
                @endif

            </div>

            <div class="p-4 border-t border-white/20">
                <form action="/logout" method="POST"
                    class="flex items-center gap-2 text-sm hover:text-red-500 transition">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 text-sm hover:text-red-500 transition bg-transparent border-none p-0 cursor-pointer">
                        <i class="fas fa-sign-out-alt"></i> Log Out
                    </button>
                </form>
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
                            <p class="text-sm font-medium">
                                {{auth()->user()->name}}
                            </p>
                            <p class="text-xs text-gray-300">
                                {{auth()->user()->role}} Puskesmas
                            </p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                let errors = "";
                @foreach ($errors->all() as $error)
                    errors += "• {{ $error }}\n";
                @endforeach
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errors.replace(/\n/g, '<br>'),
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                });
            @endif

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: "{{ session('success') }}",
                    timer: 2500,
                    showConfirmButton: false,
                });
            @endif
    });
    </script>


</body>

</html>
