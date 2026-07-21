<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Supply Chain Management')
    </title>

    {{-- Bootstrap 5 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    {{-- Leaflet CSS --}}
    <link
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        rel="stylesheet"
    >

    {{-- CSS utama project --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}?v={{
            file_exists(public_path('css/style.css'))
                ? filemtime(public_path('css/style.css'))
                : time()
        }}"
    >

    {{--
        Jangan muat scm-redesign.css lagi.
        Atur seluruh desain melalui style.css agar tidak terjadi benturan CSS.
    --}}

    @stack('styles')
</head>

<body>
    <div class="app-shell">

        {{-- SIDEBAR DESKTOP --}}
        <aside
            class="sidebar sidebar-desktop"
            aria-label="Navigasi utama"
        >
            @include('partials.sidebar')
        </aside>

        {{-- SIDEBAR MOBILE --}}
        <div
            class="offcanvas offcanvas-start scm-offcanvas"
            tabindex="-1"
            id="mobileSidebar"
            aria-labelledby="mobileSidebarLabel"
        >
            <div class="offcanvas-header">
                <h5
                    class="offcanvas-title text-white"
                    id="mobileSidebarLabel"
                >
                    Menu
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="offcanvas"
                    aria-label="Tutup menu"
                ></button>
            </div>

            <div class="offcanvas-body">
                <div class="sidebar">
                    @include('partials.sidebar')
                </div>
            </div>
        </div>

        {{-- AREA UTAMA --}}
        <main class="main" id="mainContent">

            {{-- HEADER MOBILE --}}
            <header class="mobile-header">
                <button
                    class="btn btn-outline-scm"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#mobileSidebar"
                    aria-controls="mobileSidebar"
                    aria-label="Buka menu"
                >
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="mobile-brand">
                    Supply Chain Management
                </div>
            </header>

            {{-- ISI HALAMAN --}}
            @yield('content')
        </main>
    </div>

    {{-- Bootstrap 5 JavaScript --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    {{-- Chart.js --}}
    <script
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>

    {{-- Leaflet JavaScript --}}
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    ></script>

    @stack('scripts')
</body>
</html>