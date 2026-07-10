<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Pelabuhan - Supply Chain Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap, Icons, Leaflet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>

<body>
<div class="app">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                Supply Chain<br>Management
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="nav-link-custom">
    <i class="bi bi-house-door-fill"></i>
    Dashboard
</a>

<a href="{{ route('countries.index') }}" class="nav-link-custom">
    <i class="bi bi-globe2"></i>
    Negara
</a>

<a href="{{ route('risks.index') }}" class="nav-link-custom">
    <i class="bi bi-shield-check"></i>
    Risiko
</a>

<a href="{{ route('ports.index') }}" class="nav-link-custom active">
    <i class="bi bi-pin-map-fill"></i>
    Pelabuhan
</a>

<a href="{{ route('currencies.index') }}" class="nav-link-custom {{ request()->routeIs('currencies.index') ? 'active' : '' }}">
    <i class="bi bi-currency-dollar"></i>
    <span>Kurs</span>
</a>

<a href="{{ route('news.index') }}" class="nav-link-custom {{ request()->routeIs('news.index') ? 'active' : '' }}">
    <i class="bi bi-newspaper"></i>
    <span>Berita</span>
</a>

<a href="#" class="nav-link-custom">
    <i class="bi bi-bar-chart-line"></i>
    Perbandingan
</a>

<a href="#" class="nav-link-custom">
    <i class="bi bi-star"></i>
    Watchlist
</a>

<a href="#" class="nav-link-custom">
    <i class="bi bi-person-gear"></i>
    Admin
</a>

        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div style="font-weight: 700;">Admin</div>
                <div style="font-size: 13px; color: #bfdbfe;">Administrator</div>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main">
        <div class="topbar">
            <div class="page-title">
                <h1>Peta Pelabuhan Global</h1>
                <p>Monitoring lokasi pelabuhan, status operasional, dan risiko logistik.</p>
            </div>

            <form action="{{ route('ports.index') }}" method="GET">
                <select name="country_id" class="form-select country-select" onchange="this.form.submit()">
                    <option value="">Semua Negara</option>

                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" {{ $selectedCountryId == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="content">
            <!-- SUMMARY CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-anchor"></i>
                        </div>
                        <div>
                            <div class="metric-label">Total Pelabuhan</div>
                            <div class="metric-value">{{ $summary['total_ports'] }}</div>
                            <div class="metric-sub">
                                {{ $selectedCountry ? $selectedCountry->name : 'Semua negara' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Status Aman</div>
                            <div class="metric-value">{{ $summary['safe_ports'] }}</div>
                            <div class="metric-sub">Pelabuhan berjalan normal</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-orange">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Waspada</div>
                            <div class="metric-value">{{ $summary['warning_ports'] }}</div>
                            <div class="metric-sub">Perlu dipantau</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div>
                            <div class="metric-label">Siaga</div>
                            <div class="metric-value">{{ $summary['alert_ports'] }}</div>
                            <div class="metric-sub">Risiko logistik meningkat</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAP + DETAIL -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card-clean">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mb-0">Peta Lokasi Pelabuhan</div>
                            <span class="risk-badge risk-medium">Leaflet.js + OpenStreetMap</span>
                        </div>

                        <div id="map" style="height: 430px;"></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-clean">
                        <div class="section-title">Informasi Filter</div>

                        <div class="info-row">
                            <div class="info-label">Negara</div>
                            <div class="info-value">{{ $selectedCountry->name ?? 'Semua Negara' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Wilayah</div>
                            <div class="info-value">{{ $selectedCountry->region ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Mata Uang</div>
                            <div class="info-value">{{ $selectedCountry->currency_code ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Jumlah Pelabuhan</div>
                            <div class="info-value">{{ $summary['total_ports'] }}</div>
                        </div>

                        <div class="recommendation-box mt-4">
                            <strong>Catatan Sistem:</strong>
                            <div class="mt-2">
                                Pelabuhan dengan status waspada atau siaga perlu diperhatikan karena dapat memengaruhi ketepatan waktu pengiriman dan biaya logistik.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="card-clean">
                <div class="section-title">Daftar Pelabuhan</div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Pelabuhan</th>
                            <th>Kota</th>
                            <th>Negara</th>
                            <th>Koordinat</th>
                            <th>Status</th>
                            <th>Skor Risiko</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($ports as $port)
                            <tr>
                                <td>
                                    <i class="bi bi-anchor text-primary"></i>
                                    <strong>{{ $port->name }}</strong>
                                </td>
                                <td>{{ $port->city ?? '-' }}</td>
                                <td>{{ $port->country_real_name ?? $port->country_name ?? '-' }}</td>
                                <td>
                                    {{ $port->latitude ?? '-' }},
                                    {{ $port->longitude ?? '-' }}
                                </td>
                                <td>
                                    <span class="risk-badge
                                        {{ $port->status === 'Aman' ? 'risk-low' : ($port->status === 'Waspada' ? 'risk-high' : 'risk-medium') }}">
                                        {{ $port->status }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $port->port_risk_score }}/100</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted">
                                    Belum ada data pelabuhan untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
            </div>
        </div>
    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const ports = @json($ports);

    const map = L.map('map').setView([-2.5, 118], 3);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'OpenStreetMap'
    }).addTo(map);

    const markerGroup = [];

    ports.forEach(function(port) {
        if (port.latitude && port.longitude) {
            const statusClass = port.status === 'Aman'
                ? 'Aman'
                : port.status === 'Waspada'
                    ? 'Waspada'
                    : 'Siaga';

            const marker = L.marker([port.latitude, port.longitude])
                .addTo(map)
                .bindPopup(`
                    <strong>${port.name}</strong><br>
                    Kota: ${port.city ?? '-'}<br>
                    Negara: ${port.country_real_name ?? port.country_name ?? '-'}<br>
                    Status: ${statusClass}<br>
                    Skor Risiko: ${port.port_risk_score}/100
                `);

            markerGroup.push(marker);
        }
    });

    if (markerGroup.length > 0) {
        const group = L.featureGroup(markerGroup);
        map.fitBounds(group.getBounds().pad(0.25));
    }
</script>
</body>
</html>