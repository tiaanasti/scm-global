<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Supply Chain Risk Intelligence')
    </title>

    {{-- Bootstrap 5 CSS --}}
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

    {{-- CSS utama --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}?v={{
            file_exists(public_path('css/style.css'))
                ? filemtime(public_path('css/style.css'))
                : time()
        }}"
    >

    {{-- CSS tambahan dari masing-masing halaman --}}
    @stack('styles')
</head>

<body>
    <div class="app-shell">
        {{-- Navbar desktop dan mobile --}}
        @include('partials.navbar')

        {{-- Konten utama --}}
        <main
            class="main"
            id="mainContent"
        >
            @yield('content')
        </main>
    </div>

    {{-- Bootstrap 5 Bundle --}}
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

    {{-- JavaScript halaman --}}
    @stack('scripts')

    {{-- Perbaikan navigasi sidebar mobile --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileSidebar =
                document.getElementById('mobileSidebar');

            if (!mobileSidebar) {
                return;
            }

            const mobileLinks =
                mobileSidebar.querySelectorAll(
                    'a.nav-link-custom[href]'
                );

            mobileLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    const destination = this.href;
                    const rawHref = this.getAttribute('href');

                    if (
                        !rawHref ||
                        rawHref === '#' ||
                        rawHref.startsWith('javascript:')
                    ) {
                        event.preventDefault();
                        return;
                    }

                    /*
                     * Jangan menangani klik pembuka tab baru.
                     */
                    if (
                        event.ctrlKey ||
                        event.metaKey ||
                        event.shiftKey ||
                        event.altKey
                    ) {
                        return;
                    }

                    event.preventDefault();

                    /*
                     * Apabila pengguna menekan menu halaman yang
                     * sedang dibuka, cukup tutup sidebar.
                     */
                    if (destination === window.location.href) {
                        const currentOffcanvas =
                            bootstrap.Offcanvas.getInstance(
                                mobileSidebar
                            );

                        currentOffcanvas?.hide();
                        return;
                    }

                    /*
                     * Ambil atau buat instance Bootstrap offcanvas.
                     */
                    const offcanvas =
                        bootstrap.Offcanvas.getOrCreateInstance(
                            mobileSidebar
                        );

                    /*
                     * Pindah halaman setelah sidebar benar-benar
                     * ditutup agar tidak tertahan backdrop.
                     */
                    mobileSidebar.addEventListener(
                        'hidden.bs.offcanvas',
                        function () {
                            window.location.assign(destination);
                        },
                        {
                            once: true
                        }
                    );

                    offcanvas.hide();

                    /*
                     * Fallback jika event hidden tidak berjalan.
                     */
                    window.setTimeout(function () {
                        if (
                            window.location.href !== destination &&
                            mobileSidebar.classList.contains('show')
                        ) {
                            window.location.assign(destination);
                        }
                    }, 700);
                });
            });

            /*
             * Membersihkan backdrop yang tertinggal setelah
             * perpindahan halaman atau penggunaan tombol kembali.
             */
            window.addEventListener('pageshow', function () {
                document
                    .querySelectorAll('.offcanvas-backdrop')
                    .forEach(function (backdrop) {
                        backdrop.remove();
                    });

                document.body.classList.remove(
                    'modal-open',
                    'offcanvas-open'
                );

                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            });
        });
    </script>
</body>
</html>