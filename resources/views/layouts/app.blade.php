<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rumah Sakit Delta')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('simrs_theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @stack('styles')
</head>
<body>
    <div class="app-container">
        {{-- Dulu: <?php include 'topbar.php'; ?> --}}
        @include('partials.topbar')

        <div class="main-wrapper">
            {{-- Dulu: <?php include 'sidebar.php'; ?>, dengan $halaman_aktif dikirim manual --}}
            @include('partials.sidebar', ['halamanAktif' => $halamanAktif ?? ''])

            <main class="content content-area">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/simrs-ui.js') }}"></script>
    @if (in_array(strtolower(session('role_aktif', '')), ['kasir', 'admin kasir', 'admin_kasir', 'kasir_utama']))
        <script src="{{ asset('js/simrs-notifikasi-kasir.js') }}"></script>
    @elseif (strtolower(session('role_aktif', '')) === 'perawat')
        <script src="{{ asset('js/simrs-notifikasi-perawat.js') }}"></script>
    @elseif (strtolower(session('role_aktif', '')) === 'dokter')
        <script src="{{ asset('js/simrs-notifikasi-dokter.js') }}"></script>
    @elseif (strtolower(session('role_aktif', '')) === 'apoteker')
        <script src="{{ asset('js/simrs-notifikasi-apoteker.js') }}"></script>
    @elseif (in_array(strtolower(session('role_aktif', '')), ['loket', 'petugas_loket']))
        <script src="{{ asset('js/simrs-notifikasi-loket.js') }}"></script>
    @elseif (strtolower(session('role_aktif', '')) === 'resepsionis')
        <script src="{{ asset('js/simrs-notifikasi-resepsionis.js') }}"></script>
    @elseif (in_array(strtolower(session('role_aktif', '')), ['admin', 'administrator']))
        <script src="{{ asset('js/simrs-notifikasi-admin.js') }}"></script>
    @endif
    @stack('scripts')
</body>
</html>
