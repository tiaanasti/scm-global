@php
    $primaryMenu = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'active' => 'dashboard',
            'icon' => 'bi-house-door-fill',
        ],
        [
            'label' => 'Negara',
            'route' => 'countries.index',
            'active' => 'countries.*',
            'icon' => 'bi-globe2',
        ],
        [
            'label' => 'Cuaca Global',
            'route' => 'weather.index',
            'active' => 'weather.*',
            'icon' => 'bi-cloud-sun-fill',
        ],
        [
            'label' => 'Risiko',
            'route' => 'risks.index',
            'active' => 'risks.*',
            'icon' => 'bi-shield-check',
        ],
        [
            'label' => 'Pelabuhan',
            'route' => 'ports.index',
            'active' => 'ports.*',
            'icon' => 'bi-pin-map-fill',
        ],
    ];

    $secondaryMenu = [
        [
            'label' => 'Kurs',
            'route' => 'currencies.index',
            'active' => 'currencies.*',
            'icon' => 'bi-currency-dollar',
        ],
        [
            'label' => 'Berita',
            'route' => 'news.index',
            'active' => 'news.*',
            'icon' => 'bi-newspaper',
        ],
        [
            'label' => 'Perbandingan',
            'route' => 'comparisons.index',
            'active' => 'comparisons.*',
            'icon' => 'bi-bar-chart-line-fill',
        ],
        [
            'label' => 'Watchlist',
            'route' => 'watchlists.index',
            'active' => 'watchlists.*',
            'icon' => 'bi-star-fill',
        ],
        [
            'label' => 'Laporan',
            'route' => 'reports.index',
            'active' => 'reports.*',
            'icon' => 'bi-file-earmark-bar-graph-fill',
        ],
    ];

    if (
        auth()->check()
        && (auth()->user()->role ?? 'user') === 'admin'
    ) {
        $secondaryMenu[] = [
            'label' => 'Admin',
            'route' => 'admin.index',
            'active' => 'admin.*',
            'icon' => 'bi-person-gear',
        ];
    }

    $allMenu = array_merge(
        $primaryMenu,
        $secondaryMenu
    );

    $hasSecondaryActive = collect($secondaryMenu)
        ->contains(function ($item) {
            return request()->routeIs($item['active']);
        });
@endphp

{{-- NAVBAR UTAMA --}}
<nav
    class="scm-navbar navbar navbar-expand-lg"
    aria-label="Navigasi utama"
>
    <div class="container-fluid scm-navbar-container">

        {{-- BRAND --}}
        <a
            href="{{ route('dashboard') }}"
            class="scm-navbar-brand"
            aria-label="Kembali ke Dashboard"
        >
            <span
                class="scm-navbar-brand-icon"
                aria-hidden="true"
            >
                <i class="bi bi-box-seam"></i>
            </span>

            <span class="scm-navbar-brand-text">
                <span>Supply Chain</span>
                <span>Risk Intelligence</span>
            </span>
        </a>

        {{-- MENU DESKTOP --}}
        <div
            class="scm-navbar-menu d-none d-lg-flex"
            role="menubar"
        >
            @foreach ($primaryMenu as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="scm-navbar-link
                        {{ request()->routeIs($item['active'])
                            ? 'active'
                            : ''
                        }}"
                    @if (request()->routeIs($item['active']))
                        aria-current="page"
                    @endif
                >
                    <i
                        class="bi {{ $item['icon'] }}"
                        aria-hidden="true"
                    ></i>

                    <span>
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach

            {{-- DROPDOWN MENU LAINNYA --}}
            <div class="dropdown">
                <button
                    class="scm-navbar-link
                        scm-navbar-dropdown-toggle
                        {{ $hasSecondaryActive
                            ? 'active'
                            : ''
                        }}"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i
                        class="bi bi-grid-1x2-fill"
                        aria-hidden="true"
                    ></i>

                    <span>Lainnya</span>

                    <i
                        class="bi bi-chevron-down small"
                        aria-hidden="true"
                    ></i>
                </button>

                <ul
                    class="dropdown-menu
                        scm-navbar-dropdown"
                >
                    @foreach ($secondaryMenu as $item)
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                class="dropdown-item
                                    {{ request()->routeIs(
                                        $item['active']
                                    )
                                        ? 'active'
                                        : ''
                                    }}"
                                @if (request()->routeIs(
                                    $item['active']
                                ))
                                    aria-current="page"
                                @endif
                            >
                                <i
                                    class="bi
                                        {{ $item['icon'] }}
                                        me-2"
                                    aria-hidden="true"
                                ></i>

                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- BAGIAN AKUN DAN TOMBOL MOBILE --}}
        <div class="scm-navbar-actions">

            {{-- AKUN DESKTOP --}}
            @auth
                <div class="dropdown d-none d-lg-block">
                    <button
                        class="scm-account-chip"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-label="Buka menu akun"
                    >
                        <span
                            class="user-avatar"
                            aria-hidden="true"
                        >
                            <i class="bi bi-person-fill"></i>
                        </span>

                        <span class="scm-account-copy">
                            <span>
                                {{ auth()->user()->name }}
                            </span>

                            <small>
                                {{ ucfirst(
                                    auth()->user()->role
                                        ?? 'user'
                                ) }}
                            </small>
                        </span>

                        <i
                            class="bi bi-chevron-down"
                            aria-hidden="true"
                        ></i>
                    </button>

                    <ul
                        class="dropdown-menu
                            dropdown-menu-end
                            scm-navbar-dropdown"
                    >
                        <li>
                            <form
                                action="{{ route('logout') }}"
                                method="POST"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="dropdown-item"
                                >
                                    <i
                                        class="bi
                                            bi-box-arrow-right
                                            me-2"
                                        aria-hidden="true"
                                    ></i>

                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth

            {{-- TOMBOL MENU MOBILE --}}
            <button
                class="scm-navbar-toggler d-lg-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#scmMobileNav"
                aria-controls="scmMobileNav"
                aria-expanded="false"
                aria-label="Buka menu navigasi"
            >
                <i
                    class="bi bi-list"
                    aria-hidden="true"
                ></i>
            </button>
        </div>
    </div>
</nav>

{{-- OFFCANVAS MOBILE --}}
<div
    class="offcanvas
        offcanvas-end
        scm-mobile-nav"
    tabindex="-1"
    id="scmMobileNav"
    aria-labelledby="scmMobileNavLabel"
>
    {{-- HEADER MOBILE --}}
    <div class="offcanvas-header">
        <a
            href="{{ route('dashboard') }}"
            class="scm-mobile-brand
                text-decoration-none"
            id="scmMobileNavLabel"
            aria-label="Kembali ke Dashboard"
        >
            <span
                class="scm-navbar-brand-icon"
                aria-hidden="true"
            >
                <i class="bi bi-box-seam"></i>
            </span>

            <span>
                Supply Chain<br>
                Risk Intelligence
            </span>
        </a>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
            aria-label="Tutup menu"
        ></button>
    </div>

    {{-- ISI MENU MOBILE --}}
    <div class="offcanvas-body">
        <nav
            class="scm-mobile-menu"
            aria-label="Navigasi mobile"
        >
            @foreach ($allMenu as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="scm-mobile-link
                        js-scm-mobile-link
                        {{ request()->routeIs(
                            $item['active']
                        )
                            ? 'active'
                            : ''
                        }}"
                    @if (request()->routeIs(
                        $item['active']
                    ))
                        aria-current="page"
                    @endif
                >
                    <i
                        class="bi {{ $item['icon'] }}"
                        aria-hidden="true"
                    ></i>

                    <span>
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        {{-- INFORMASI AKUN MOBILE --}}
        @auth
            <div class="scm-mobile-account">
                <div
                    class="d-flex
                        align-items-center
                        gap-2"
                >
                    <span
                        class="user-avatar"
                        aria-hidden="true"
                    >
                        <i class="bi bi-person-fill"></i>
                    </span>

                    <span class="min-w-0">
                        <strong class="d-block">
                            {{ auth()->user()->name }}
                        </strong>

                        <small>
                            {{ ucfirst(
                                auth()->user()->role
                                    ?? 'user'
                            ) }}
                        </small>
                    </span>
                </div>

                <form
                    action="{{ route('logout') }}"
                    method="POST"
                    class="mt-3"
                >
                    @csrf

                    <button
                        type="submit"
                        class="btn
                            btn-outline-light
                            w-100"
                    >
                        <i
                            class="bi
                                bi-box-arrow-right
                                me-1"
                            aria-hidden="true"
                        ></i>

                        Keluar
                    </button>
                </form>
            </div>
        @endauth
    </div>
</div>

{{-- JAVASCRIPT KHUSUS NAVIGASI MOBILE --}}
@once
    @push('scripts')
        <script>
            document.addEventListener(
                'DOMContentLoaded',
                function () {
                    const mobileNav =
                        document.getElementById(
                            'scmMobileNav'
                        );

                    if (
                        !mobileNav
                        || typeof bootstrap === 'undefined'
                    ) {
                        return;
                    }

                    const mobileLinks =
                        mobileNav.querySelectorAll(
                            'a.js-scm-mobile-link[href]'
                        );

                    mobileLinks.forEach(function (link) {
                        link.addEventListener(
                            'click',
                            function (event) {
                                const rawHref =
                                    this.getAttribute(
                                        'href'
                                    );

                                const destination =
                                    this.href;

                                if (
                                    !rawHref
                                    || rawHref === '#'
                                    || rawHref.startsWith(
                                        'javascript:'
                                    )
                                ) {
                                    event.preventDefault();
                                    return;
                                }

                                /*
                                 * Biarkan browser menangani klik
                                 * untuk membuka tab baru.
                                 */
                                if (
                                    event.ctrlKey
                                    || event.metaKey
                                    || event.shiftKey
                                    || event.altKey
                                    || event.button !== 0
                                ) {
                                    return;
                                }

                                event.preventDefault();
                                event.stopPropagation();

                                const currentUrl =
                                    new URL(
                                        window.location.href
                                    );

                                const targetUrl =
                                    new URL(destination);

                                const currentLocation =
                                    currentUrl.origin
                                    + currentUrl.pathname
                                    + currentUrl.search;

                                const targetLocation =
                                    targetUrl.origin
                                    + targetUrl.pathname
                                    + targetUrl.search;

                                const offcanvas =
                                    bootstrap
                                        .Offcanvas
                                        .getOrCreateInstance(
                                            mobileNav
                                        );

                                /*
                                 * Jika menu yang ditekan adalah
                                 * halaman yang sedang dibuka,
                                 * cukup tutup offcanvas.
                                 */
                                if (
                                    currentLocation
                                    === targetLocation
                                ) {
                                    offcanvas.hide();
                                    return;
                                }

                                let navigationStarted =
                                    false;

                                function navigate() {
                                    if (
                                        navigationStarted
                                    ) {
                                        return;
                                    }

                                    navigationStarted = true;

                                    window.location.assign(
                                        destination
                                    );
                                }

                                /*
                                 * Pindah halaman setelah
                                 * offcanvas tertutup.
                                 */
                                mobileNav.addEventListener(
                                    'hidden.bs.offcanvas',
                                    navigate,
                                    {
                                        once: true
                                    }
                                );

                                offcanvas.hide();

                                /*
                                 * Fallback untuk browser HP
                                 * jika event Bootstrap gagal.
                                 */
                                window.setTimeout(
                                    navigate,
                                    500
                                );
                            }
                        );
                    });

                    /*
                     * Membersihkan backdrop apabila
                     * pengguna kembali dengan tombol
                     * back browser.
                     */
                    window.addEventListener(
                        'pageshow',
                        function () {
                            document
                                .querySelectorAll(
                                    '.offcanvas-backdrop'
                                )
                                .forEach(
                                    function (backdrop) {
                                        backdrop.remove();
                                    }
                                );

                            mobileNav.classList.remove(
                                'show',
                                'showing',
                                'hiding'
                            );

                            mobileNav.removeAttribute(
                                'aria-modal'
                            );

                            mobileNav.setAttribute(
                                'aria-hidden',
                                'true'
                            );

                            document.body.style
                                .removeProperty(
                                    'overflow'
                                );

                            document.body.style
                                .removeProperty(
                                    'padding-right'
                                );
                        }
                    );
                }
            );
        </script>
    @endpush
@endonce