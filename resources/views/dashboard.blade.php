<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Supply Chain Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet Map -->
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --navy: #06204a;
            --navy-dark: #031531;
            --blue: #2563eb;
            --soft-blue: #eff6ff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e5e7eb;
            --green: #22c55e;
            --yellow: #f59e0b;
            --red: #ef4444;
            --purple: #8b5cf6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fb;
            color: var(--text-main);
            font-family: Arial, Helvetica, sans-serif;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
    width: 260px;
    background: linear-gradient(180deg, var(--navy-dark), var(--navy));
    color: white;
    padding: 24px 18px;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 21px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 34px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

       .nav-link-custom {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 11px 14px;
    border-radius: 12px;
    color: #dbeafe;
    text-decoration: none;
    margin-bottom: 6px;
    font-size: 15px;
    transition: 0.2s;
}

        .nav-link-custom:hover,
        .nav-link-custom.active {
            background: var(--blue);
            color: white;
        }

     .sidebar-user {
    margin-top: 24px;
    padding: 16px;
    background: rgba(255,255,255,0.09);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e0ecff;
            color: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
        }

        .topbar {
            height: 88px;
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 20px 34px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title h1 {
            font-size: 27px;
            font-weight: 800;
            margin: 0;
            color: #0b1f44;
        }

        .page-title p {
            margin: 5px 0 0;
            color: var(--text-muted);
            font-size: 15px;
        }

        .country-select {
            min-width: 250px;
            border-radius: 12px;
            padding: 11px 14px;
            border: 1px solid var(--border);
        }

        .content {
            padding: 28px 34px;
        }

        .card-clean {
            background: white;
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
            padding: 22px;
            height: 100%;
        }

        .metric-card {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .metric-icon {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .icon-blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .icon-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-orange {
            background: #ffedd5;
            color: #f97316;
        }

        .icon-purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .icon-red {
            background: #fee2e2;
            color: #ef4444;
        }

        .metric-label {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .metric-value {
            font-size: 25px;
            font-weight: 800;
            color: #0b1f44;
            margin-bottom: 2px;
        }

        .metric-sub {
            color: var(--text-muted);
            font-size: 13px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 800;
            color: #0b1f44;
            margin-bottom: 18px;
        }

        #map {
            height: 315px;
            width: 100%;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .risk-score-box {
            text-align: center;
        }

        .risk-number {
            font-size: 38px;
            font-weight: 800;
            color: #0b1f44;
            line-height: 1;
        }

        .risk-level {
            font-size: 15px;
            font-weight: 700;
            color: var(--yellow);
            margin-top: 8px;
        }

        .risk-row {
            margin-bottom: 17px;
        }

        .risk-row-top {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .progress {
            height: 8px;
            background: #e5e7eb;
            border-radius: 999px;
        }

        .progress-bar {
            border-radius: 999px;
        }

        .news-item {
            display: flex;
            gap: 12px;
            padding: 13px 0;
            border-bottom: 1px solid var(--border);
        }

        .news-item:last-child {
            border-bottom: none;
        }

        .news-icon {
            min-width: 42px;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--soft-blue);
            color: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .news-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .news-desc {
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.35;
        }

        .badge-soft {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-low {
            background: #dcfce7;
            color: #166534;
        }

        .badge-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .recommendation-box {
            background: #eff6ff;
            border-radius: 16px;
            padding: 22px;
            text-align: center;
            color: #1e3a8a;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .recommendation-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: white;
            color: var(--blue);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 32px;
            margin: 0 auto 16px;
        }

        .footer {
            color: var(--text-muted);
            text-align: center;
            font-size: 13px;
            margin-top: 18px;
        }

        @media (max-width: 992px) {
            .sidebar {
                position: relative;
                width: 100%;
                bottom: auto;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            .app {
                display: block;
            }

            .sidebar-user {
                position: static;
                margin-top: 30px;
            }

            .topbar {
                height: auto;
                align-items: flex-start;
                gap: 18px;
                flex-direction: column;
            }

            .country-select {
                min-width: 100%;
            }
        }
    </style>
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

        <a href="#" class="nav-link-custom active">
            <i class="bi bi-house-door-fill"></i>
            Dashboard
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-globe2"></i>
            Negara
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-shield-check"></i>
            Risiko
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-anchor"></i>
            Pelabuhan
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-currency-dollar"></i>
            Kurs
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-newspaper"></i>
            Berita
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

    <!-- MAIN CONTENT -->
    <main class="main">
        <div class="topbar">
            <div class="page-title">
                <h1>Dashboard Risiko Rantai Pasok Global</h1>
                <p>Monitoring risiko ekonomi, cuaca, kurs, berita, dan pelabuhan secara global.</p>
            </div>

            <form action="{{ route('dashboard') }}" method="GET">
                <select name="country_id" class="form-select country-select" onchange="this.form.submit()">
                    @foreach ($countries as $item)
                        <option value="{{ $item->id }}" {{ $selectedCountryId == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="content">
            <!-- METRICS -->
            <div class="row g-3 mb-4">
                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>
                        <div>
                            <div class="metric-label">GDP</div>
                            <div class="metric-value">
                                ${{ number_format(($economic->gdp ?? 0) / 1000000000000, 2) }}T
                            </div>
                            <div class="metric-sub">Data ekonomi negara</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div>
                            <div class="metric-label">Inflasi</div>
                            <div class="metric-value">{{ $economic->inflation_rate ?? 0 }}%</div>
                            <div class="metric-sub">Perubahan harga tahunan</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-cloud-sun"></i>
                        </div>
                        <div>
                            <div class="metric-label">Cuaca Saat Ini</div>
                            <div class="metric-value">{{ $weather->temperature ?? 0 }}°C</div>
                            <div class="metric-sub">{{ $weather->weather_status ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-purple">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <div>
                            <div class="metric-label">Kurs USD/{{ $currency->target_currency ?? '-' }}</div>
                            <div class="metric-value">
                                {{ number_format($currency->exchange_rate ?? 0, 2) }}
                            </div>
                            <div class="metric-sub">
                                Perubahan {{ $currency->change_percentage ?? 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="metric-label">Skor Risiko</div>
                            <div class="metric-value">
                                {{ $risk->total_score ?? 0 }}<span style="font-size: 16px;">/100</span>
                            </div>
                            <div class="metric-sub" style="color: #f59e0b; font-weight: 700;">
                                {{ $risk->risk_level ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mb-0">Tren Risiko</div>
                            <select class="form-select form-select-sm" style="width: 170px;">
                                <option>6 Bulan Terakhir</option>
                                <option>12 Bulan Terakhir</option>
                            </select>
                        </div>
                        <canvas id="trendChart" height="135"></canvas>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Komponen Risiko</div>

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <canvas id="riskChart" height="190"></canvas>
                                <div class="risk-score-box mt-2">
                                    <div class="risk-number">{{ $risk->total_score ?? 0 }}</div>
                                    <div style="color: #64748b;">/100</div>
                                    <div class="risk-level">{{ $risk->risk_level ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="risk-row">
                                    <div class="risk-row-top">
                                        <span><i class="bi bi-circle-fill text-primary"></i> Cuaca</span>
                                        <strong>{{ $risk->weather_score ?? 0 }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: {{ $risk->weather_score ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <div class="risk-row">
                                    <div class="risk-row-top">
                                        <span><i class="bi bi-circle-fill text-success"></i> Inflasi</span>
                                        <strong>{{ $risk->inflation_score ?? 0 }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ $risk->inflation_score ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <div class="risk-row">
                                    <div class="risk-row-top">
                                        <span><i class="bi bi-circle-fill text-warning"></i> Kurs</span>
                                        <strong>{{ $risk->currency_score ?? 0 }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: {{ $risk->currency_score ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <div class="risk-row">
                                    <div class="risk-row-top">
                                        <span><i class="bi bi-circle-fill" style="color:#8b5cf6;"></i> Berita</span>
                                        <strong>{{ $risk->news_score ?? 0 }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $risk->news_score ?? 0 }}%; background:#8b5cf6;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- MAP AND COUNTRY INFO -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="section-title">Peta Pelabuhan Global</div>
                        <div id="map"></div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Ringkasan Negara: {{ $country->name ?? '-' }}</div>

                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted">Ibu Kota</td>
                                <td class="fw-bold text-end">{{ $country->capital ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Wilayah</td>
                                <td class="fw-bold text-end">{{ $country->region ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Mata Uang</td>
                                <td class="fw-bold text-end">{{ $country->currency_code ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Populasi</td>
                                <td class="fw-bold text-end">
                                    {{ number_format($economic->population ?? 0) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ekspor</td>
                                <td class="fw-bold text-end">
                                    ${{ number_format(($economic->exports ?? 0) / 1000000000, 2) }}B
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Impor</td>
                                <td class="fw-bold text-end">
                                    ${{ number_format(($economic->imports ?? 0) / 1000000000, 2) }}B
                                </td>
                            </tr>
                        </table>

                        <div class="recommendation-box mt-3">
                            <div class="recommendation-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <strong>Rekomendasi Sistem</strong>
                            <p class="mt-2 mb-0">
                                {{ $risk->recommendation ?? 'Belum ada rekomendasi untuk negara ini.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NEWS AND PORTS -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Berita Terbaru</div>

                        @forelse ($news as $item)
                            <div class="news-item">
                                <div class="news-icon">
                                    @if ($item->category === 'Logistik')
                                        <i class="bi bi-cloud-rain"></i>
                                    @elseif ($item->category === 'Ekonomi')
                                        <i class="bi bi-graph-up-arrow"></i>
                                    @else
                                        <i class="bi bi-newspaper"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge-soft
                                            {{ $item->sentiment === 'Negative' ? 'badge-high' : ($item->sentiment === 'Positive' ? 'badge-low' : 'badge-medium') }}">
                                            {{ $item->category ?? 'Umum' }}
                                        </span>
                                        <small class="text-muted">{{ $item->sentiment }}</small>
                                    </div>
                                    <div class="news-title">{{ $item->title }}</div>
                                    <div class="news-desc">{{ $item->description }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada berita untuk negara ini.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Pelabuhan Utama</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Pelabuhan</th>
                                    <th>Kota</th>
                                    <th>Status</th>
                                    <th>Risiko</th>
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
                                        <td>
                                            <span class="badge-soft
                                                {{ $port->status === 'Aman' ? 'badge-low' : ($port->status === 'Waspada' ? 'badge-high' : 'badge-medium') }}">
                                                {{ $port->status }}
                                            </span>
                                        </td>
                                        <td>{{ $port->port_risk_score }}/100</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">Belum ada data pelabuhan.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
            </div>
        </div>
    </main>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const riskScore = Number(@json($risk->total_score ?? 0));
    const weatherScore = Number(@json($risk->weather_score ?? 0));
    const inflationScore = Number(@json($risk->inflation_score ?? 0));
    const currencyScore = Number(@json($risk->currency_score ?? 0));
    const newsScore = Number(@json($risk->news_score ?? 0));

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: ['Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
            datasets: [{
                label: 'Skor Risiko',
                data: [
                    Math.max(riskScore - 7, 0),
                    Math.max(riskScore - 3, 0),
                    riskScore,
                    Math.min(riskScore + 9, 100),
                    Math.min(riskScore + 3, 100),
                    riskScore
                ],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.10)',
                pointBackgroundColor: '#2563eb',
                pointRadius: 4,
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    new Chart(document.getElementById('riskChart'), {
        type: 'doughnut',
        data: {
            labels: ['Cuaca', 'Inflasi', 'Kurs', 'Berita'],
            datasets: [{
                data: [weatherScore, inflationScore, currencyScore, newsScore],
                backgroundColor: ['#2563eb', '#22c55e', '#f59e0b', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    const map = L.map('map').setView([5, 110], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'OpenStreetMap'
    }).addTo(map);

    const allPorts = @json($allPorts);

    allPorts.forEach(function(port) {
        if (port.latitude && port.longitude) {
            L.marker([port.latitude, port.longitude])
                .addTo(map)
                .bindPopup(`
                    <strong>${port.name}</strong><br>
                    Negara: ${port.country_name}<br>
                    Kota: ${port.city ?? '-'}<br>
                    Status: ${port.status}<br>
                    Risiko: ${port.port_risk_score}/100
                `);
        }
    });
</script>

</body>
</html>