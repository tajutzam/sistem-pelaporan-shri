<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Sistem Pelaporan SHRI' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE dan Edge lama */
            scrollbar-width: none;
            /* Firefox */
        }

        .notification-dropdown {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .notification-item {
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f3f4f6;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-unread {
            background-color: #fef3c7;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800" x-data="{
    sidebarOpen: false,
    notificationOpen: false,
    notifications: [],
    notificationCount: 0,
    init() {
        this.loadNotifications();
        // Refresh notifications every 5 minutes
        setInterval(() => {
            this.loadNotifications();
        }, 300000);
    },
    async loadNotifications() {
        try {
            const today = new Date().toISOString().split('T')[0];
            const response = await fetch(`/api/notifications?date=${today}`);
            const data = await response.json();

            if (data.status) {
                this.notifications = data.data || [];
                // Hitung notifikasi yang belum dibaca (status_read = 0 atau null)
                this.notificationCount = this.notifications.filter(n => n.status_read === 0 || n.status_read === null).length;
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    },
    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',

                }
            });

            if (response.ok) {
                // Update local notification status_read
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.status_read = 1;
                    this.notificationCount = this.notifications.filter(n => n.status_read === 0 || n.status_read === null).length;
                }
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    },
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 w-64 bg-[#2F3E52] text-white flex flex-col justify-between transform transition-transform duration-200 lg:translate-x-0 lg:static z-50 overflow-y-scroll no-scrollbar"
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

        <div class="fixed inset-0 bg-opacity-10 z-40 lg:hidden" x-show="sidebarOpen" @click="sidebarOpen = false"
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
                    <button onclick="window.location.reload()" class="hover:text-gray-300">
                        <i class="fas fa-sync-alt"></i>
                    </button>

                    <!-- Notification Bell -->
                    <div class="relative">
                        <button @click="notificationOpen = !notificationOpen" class="hover:text-gray-300 relative"
                            @click.away="notificationOpen = false">
                            <i class="fas fa-bell"></i>
                            <span x-show="notificationCount > 0" x-text="notificationCount" class="notification-badge"
                                x-transition></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notificationOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">

                            <!-- Header -->
                            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                                <h3 class="text-sm font-semibold text-gray-800">Notifikasi</h3>
                            </div>

                            <!-- Notification List -->
                            <div class="notification-dropdown">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                        <i class="fas fa-bell-slash mb-2 text-gray-400"></i>
                                        <p>Tidak ada notifikasi</p>
                                    </div>
                                </template>

                                <template x-for="notification in notifications" :key="notification.id">
                                    <div class="notification-item px-4 py-3 cursor-pointer"
                                        :class="{
                                            'notification-unread': notification.status_read === 0 || notification
                                                .status_read === null
                                        }"
                                        @click="markAsRead(notification.id)">

                                        <div class="flex items-start gap-3">
                                            <!-- Icon -->
                                            <div class="flex-shrink-0 mt-1">
                                                <i class="fas fa-exclamation-circle text-yellow-500"
                                                    x-show="notification.type === 'usersensus_check'"></i>
                                                <i class="fas fa-info-circle text-blue-500"
                                                    x-show="notification.type !== 'usersensus_check'"></i>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-800 mb-1" x-text="notification.description">
                                                </p>
                                                <p class="text-xs text-gray-500"
                                                    x-text="formatDate(notification.created_at)"></p>
                                            </div>

                                            <!-- Unread Indicator -->
                                            <div x-show="notification.status_read === 0 || notification.status_read === null"
                                                class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Footer -->
                            <template x-if="notifications.length > 0">
                                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                                    <button @click="notifications.forEach(n => markAsRead(n.id))"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        Tandai semua sudah dibaca
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <a href="{{ route('profile') }}" class="flex items-center gap-2">
                        <img src="{{ asset('profile.png') }}" alt="User" class="rounded-full w-8 h-8" />
                        <div>
                            <p class="text-sm font-medium">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-gray-300">
                                {{ auth()->user()->username }}
                            </p>
                        </div>
                    </a>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-scroll p-6">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('js')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
