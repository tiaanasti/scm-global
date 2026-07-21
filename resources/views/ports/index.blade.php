@extends('layouts.app')

@section('title', 'Pelabuhan dan Tracking - Supply Chain Management')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/ports.css') }}?v={{ file_exists(public_path('css/ports.css')) ? filemtime(public_path('css/ports.css')) : time() }}"
    >

    <style>
        /*
         * Pengaman ikon kartu statistik Pelabuhan.
         * Dibuat lebih spesifik agar tidak tertimpa ports.css.
         */
        .port-page .port-stat-icon {
            display: grid !important;
            flex: 0 0 50px;
            place-items: center !important;
            width: 50px;
            height: 50px;
            overflow: visible;
            border-radius: 14px;
        }

        .port-page .port-stat-icon i {
            display: inline-block !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
            color: inherit !important;
            font-size: 23px !important;
            line-height: 1 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .port-page .port-stat-total {
            background: rgba(53, 124, 165, 0.14) !important;
            color: var(--scm-blue, #357ca5) !important;
        }

        .port-page .port-stat-safe {
            background: var(--scm-green-soft, #e1f1e8) !important;
            color: var(--scm-green, #3c8c68) !important;
        }

        .port-page .port-stat-warning {
            background: var(--scm-amber-soft, #fff0ce) !important;
            color: var(--scm-amber, #d99a2b) !important;
        }

        .port-page .port-stat-alert {
            background: var(--scm-coral-soft, #fce8e2) !important;
            color: var(--scm-coral, #e76f51) !important;
        }

        .port-page .port-stat-copy {
            min-width: 0;
        }
    </style>
@endpush

@section('content')
    @php
        $safeCount = collect($ports)
            ->filter(function ($port) {
                return in_array($port->status, ['Aman', 'Normal'], true);
            })
            ->count();

        $warningCount = collect($ports)
            ->where('status', 'Waspada')
            ->count();

        $alertCount = collect($ports)
            ->filter(function ($port) {
                return in_array($port->status, ['Siaga', 'Darurat'], true);
            })
            ->count();
    @endphp

    <div class="port-page">
        {{-- TOPBAR --}}
        <div class="topbar d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="page-title">
                <h1>Pelabuhan dan Tracking Global</h1>

                <p>
                    Monitoring data pelabuhan sekaligus menampilkan
                    koneksi rute antar-pelabuhan.
                </p>
            </div>

            <form
                action="{{ route('ports.index') }}"
                method="GET"
                class="port-country-form"
            >
                <select
                    name="country_id"
                    class="form-select port-country-filter"
                    onchange="this.form.submit()"
                    aria-label="Filter negara"
                >
                    <option value="">
                        Semua Negara
                    </option>

                    @foreach ($countries as $country)
                        <option
                            value="{{ $country->id }}"
                            {{ (string) $selectedCountryId === (string) $country->id ? 'selected' : '' }}
                        >
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="content">
            {{-- KARTU STATISTIK --}}
            <div class="row g-3 mb-4">
                {{-- TOTAL PELABUHAN --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card-clean metric-card h-100">
                        <div
                            class="metric-icon port-stat-icon port-stat-total"
                            aria-hidden="true"
                        >
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>

                        <div class="port-stat-copy">
                            <div class="metric-label">
                                Total Pelabuhan
                            </div>

                            <div class="metric-value">
                                {{ number_format($ports->count()) }}
                            </div>

                            <div class="metric-sub">
                                {{ $selectedCountry->name ?? 'Semua negara' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- NORMAL / AMAN --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card-clean metric-card h-100">
                        <div
                            class="metric-icon port-stat-icon port-stat-safe"
                            aria-hidden="true"
                        >
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                        <div class="port-stat-copy">
                            <div class="metric-label">
                                Normal/Aman
                            </div>

                            <div class="metric-value">
                                {{ number_format($safeCount) }}
                            </div>

                            <div class="metric-sub">
                                Beroperasi normal
                            </div>
                        </div>
                    </div>
                </div>

                {{-- WASPADA --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card-clean metric-card h-100">
                        <div
                            class="metric-icon port-stat-icon port-stat-warning"
                            aria-hidden="true"
                        >
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>

                        <div class="port-stat-copy">
                            <div class="metric-label">
                                Waspada
                            </div>

                            <div class="metric-value">
                                {{ number_format($warningCount) }}
                            </div>

                            <div class="metric-sub">
                                Perlu dipantau
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SIAGA / DARURAT --}}
                <div class="col-xl-3 col-md-6">
                    <div class="card-clean metric-card h-100">
                        <div
                            class="metric-icon port-stat-icon port-stat-alert"
                            aria-hidden="true"
                        >
                            <i class="bi bi-bell-fill"></i>
                        </div>

                        <div class="port-stat-copy">
                            <div class="metric-label">
                                Siaga/Darurat
                            </div>

                            <div class="metric-value">
                                {{ number_format($alertCount) }}
                            </div>

                            <div class="metric-sub">
                                Risiko meningkat
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TRACKING RUTE --}}
            <div class="card-clean mb-4">
                <div class="section-title mb-3">
                    Tracking Rute Pelabuhan
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-xl-4 col-lg-5">
                        <label
                            for="originPort"
                            class="form-label fw-semibold"
                        >
                            Pelabuhan Asal
                        </label>

                        <select
                            id="originPort"
                            class="form-select"
                        >
                            <option value="">
                                Pilih pelabuhan asal
                            </option>
                        </select>
                    </div>

                    <div class="col-xl-4 col-lg-5">
                        <label
                            for="destinationPort"
                            class="form-label fw-semibold"
                        >
                            Pelabuhan Tujuan
                        </label>

                        <select
                            id="destinationPort"
                            class="form-select"
                        >
                            <option value="">
                                Pilih pelabuhan tujuan
                            </option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-6">
                        <button
                            type="button"
                            id="drawRouteButton"
                            class="btn btn-primary w-100"
                        >
                            <i class="bi bi-signpost-split me-1"></i>
                            Tampilkan Rute
                        </button>
                    </div>

                    <div class="col-xl-2 col-lg-6">
                        <button
                            type="button"
                            id="playTrackingButton"
                            class="btn btn-outline-danger w-100"
                            disabled
                        >
                            <i class="bi bi-play-circle me-1"></i>
                            Jalankan Tracking
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button
                        type="button"
                        id="swapRouteButton"
                        class="btn btn-sm btn-outline-primary"
                    >
                        <i class="bi bi-arrow-left-right me-1"></i>
                        Tukar Asal dan Tujuan
                    </button>

                    <button
                        type="button"
                        id="resetRouteButton"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset Rute
                    </button>
                </div>
            </div>

            {{-- PETA DAN INFORMASI FILTER --}}
            <div class="row g-4 mb-4 align-items-stretch">
                <div class="col-xl-9">
                    <div class="card-clean h-100">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div>
                                <div class="section-title mb-1">
                                    Peta Lokasi dan Rute Pelabuhan
                                </div>

                                <div class="metric-sub">
                                    Dua marker biru dihubungkan garis merah
                                    putus-putus.
                                </div>
                            </div>

                            <span class="risk-badge risk-medium">
                                Leaflet.js + OpenStreetMap
                            </span>
                        </div>

                        <div class="port-map-frame">
                            <div id="trackingMap"></div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-lg-4">
                                <div class="route-summary-card h-100">
                                    <small>
                                        Pelabuhan Asal
                                    </small>

                                    <strong id="originInfo">
                                        -
                                    </strong>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="route-summary-card h-100">
                                    <small>
                                        Pelabuhan Tujuan
                                    </small>

                                    <strong id="destinationInfo">
                                        -
                                    </strong>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="route-summary-card route-distance-card h-100">
                                    <small>
                                        Jarak Garis Lurus
                                    </small>

                                    <strong id="distanceInfo">
                                        - km
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="port-map-note">
                            Garis merah putus-putus menunjukkan hubungan
                            langsung antar-pelabuhan. Titik merah bergerak
                            merupakan simulasi tracking, bukan posisi kapal
                            secara real-time.
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="card-clean h-100">
                        <div class="section-title">
                            Informasi Filter
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                Negara
                            </div>

                            <div class="info-value text-end">
                                {{ $selectedCountry->name ?? 'Semua Negara' }}
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                Wilayah
                            </div>

                            <div class="info-value text-end">
                                {{ $selectedCountry->region ?? '-' }}
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                Mata Uang
                            </div>

                            <div class="info-value text-end">
                                {{ $selectedCountry->currency_code ?? '-' }}
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                Jumlah Pelabuhan
                            </div>

                            <div class="info-value text-end">
                                {{ number_format($ports->count()) }}
                            </div>
                        </div>

                        <div class="recommendation-box mt-4">
                            <strong>
                                Catatan Sistem:
                            </strong>

                            <div class="mt-2">
                                Pilih pelabuhan asal dan tujuan untuk
                                melihat koneksi rute pada peta. Data
                                pelabuhan lengkap tersedia pada tabel
                                di bawah.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL PELABUHAN --}}
            <div class="card-clean">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                    <div>
                        <div class="section-title mb-1">
                            Daftar Data Pelabuhan
                        </div>

                        <div class="metric-sub">
                            Data nama, kota, negara, koordinat, status,
                            dan risiko pelabuhan.
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-wrap gap-2 port-table-tools">
                        <div class="input-group port-table-search">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="search"
                                id="portTableSearch"
                                class="form-control"
                                placeholder="Cari pelabuhan, kota, atau negara..."
                                aria-label="Cari data pelabuhan"
                            >
                        </div>

                        <span
                            class="risk-badge risk-low"
                            id="tablePortCounter"
                        >
                            {{ number_format($ports->count()) }} data
                        </span>
                    </div>
                </div>

                <div class="port-table-wrapper">
                    <table
                        class="table table-hover align-middle mb-0"
                        id="portsTable"
                    >
                        <thead>
                        <tr>
                            <th>Pelabuhan</th>
                            <th>Kota</th>
                            <th>Negara</th>
                            <th>Koordinat</th>
                            <th>Status</th>
                            <th>Skor Risiko</th>
                            <th>Aksi Tracking</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse ($ports as $port)
                            @php
                                $statusGroup = in_array(
                                    $port->status,
                                    ['Aman', 'Normal'],
                                    true
                                )
                                    ? 'safe'
                                    : ($port->status === 'Waspada'
                                        ? 'warning'
                                        : 'alert');

                                $statusClass = $statusGroup === 'safe'
                                    ? 'risk-low'
                                    : ($statusGroup === 'warning'
                                        ? 'risk-medium'
                                        : 'risk-high');

                                $countryLabel = $port->country_real_name
                                    ?? $port->country_name
                                    ?? '-';
                            @endphp

                            <tr
                                class="port-row"
                                data-port-id="{{ $port->id }}"
                                data-search="{{ strtolower($port->name . ' ' . ($port->city ?? '') . ' ' . $countryLabel) }}"
                            >
                                <td>
                                    <i class="bi bi-geo-alt-fill text-primary me-1"></i>

                                    <strong>
                                        {{ $port->name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $port->city ?? '-' }}
                                </td>

                                <td>
                                    {{ $countryLabel }}
                                </td>

                                <td class="port-coordinate">
                                    {{ $port->latitude ?? '-' }},
                                    {{ $port->longitude ?? '-' }}
                                </td>

                                <td>
                                    <span class="risk-badge {{ $statusClass }}">
                                        {{ $port->status ?? 'Normal' }}
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format((float) ($port->port_risk_score ?? 0), 0) }}/100
                                    </strong>
                                </td>

                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary set-origin-button"
                                            data-port-id="{{ $port->id }}"
                                        >
                                            <i class="bi bi-geo-alt me-1"></i>
                                            Asal
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger set-destination-button"
                                            data-port-id="{{ $port->id }}"
                                        >
                                            <i class="bi bi-geo-alt-fill me-1"></i>
                                            Tujuan
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="7"
                                    class="text-center text-muted py-5"
                                >
                                    Belum ada data pelabuhan untuk
                                    filter ini.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="footer">
                © {{ date('Y') }} Supply Chain Management.
                Semua hak dilindungi.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.portTrackingData =
            {{ Illuminate\Support\Js::from($ports) }};
    </script>

    <script
        src="{{ asset('js/port.js') }}?v={{ file_exists(public_path('js/port.js')) ? filemtime(public_path('js/port.js')) : time() }}"
    ></script>
@endpush