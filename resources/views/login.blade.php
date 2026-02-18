<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Pelaporan SHRI' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-picture {
            background-image: url('/rs.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
    </style>
</head>

<body class="bg-picture flex items-center justify-center relative">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/40 z-0"></div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col flex-wrap items-center justify-evenly text-white px-4 w-full  text-center">
        <div class="mb-6 space-y-5">
            <img src="/logo.png" alt="Logo RS" class="mx-auto w-20 h-20">
            <h1 class="text-5xl font-bold">SISTEM PELAPORAN SENSUS HARIAN RAWAT INAP <br><span class="text-white">RUMAH
                    SAKIT UMUM DAERAH REDA BOLO</span></h1>
            <p class="mt-1 text-lg">Jl. Wee Londa, Desa Watukawula, Kecamatan Kota Tambolaka
                Kabupaten Sumba Barat Daya</p>
        </div>

        <!-- Login Box -->
        <div class="bg-white/15 rounded-lg px-20 py-2 h-[500px] flex flex-col items-center justify-center">
            <h2 class="text-lg font-semibold mb-6">SISTEM PELAPORAN <br> SENSUS HARIAN RAWAT INAP</h2>

            <div class="bg-white text-gray-800 rounded-xl p-6 w-full max-w-sm shadow-md">
                <form action="/login" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block mb-1 text-left">Username</label>
                        <div class="relative">
                            <input type="text" name="username"
                                class="w-full border rounded px-4 py-2 pr-10 focus:outline-none" required>
                            <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-user"></i></span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 text-left">Password</label>
                        <div class="relative">
                            <input type="password" name="password"
                                class="w-full border rounded px-4 py-2 pr-10 focus:outline-none" required>
                            <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-lock"></i></span>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-[#2F3E52] text-white py-2 rounded hover:bg-[#1c2a3a] transition">
                        Sign In
                    </button>
                    <a href="">
                        Lupa Password? | Hubungi Admin
                    </a>
                </form>
            </div>
        </div>
    </div>

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
