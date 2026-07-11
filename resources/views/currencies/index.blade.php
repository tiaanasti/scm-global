<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dampak Kurs - Supply Chain Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap, Icons, Chart -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>

<body>
<div class="app">
    <!-- SIDEBAR -->
   <body>
<div class="app">

    @include('partials.sidebar')

    <!-- MAIN -->
    <main class="main">
        <div class="topbar">
            <div class="page-title">
                <h1>Dampak Kurs</h1>
                <p>Monitoring nilai tukar mata uang dan pengaruhnya terhadap biaya impor.</p>
            </div>

            <form action="{{ route('currencies.index') }}" method="GET">
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
            <!-- TOP CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-purple">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <div>
                            <div class="metric-label">Kurs USD/{{ $currency->target_currency ?? '-' }}</div>
                            <div class="metric-value">
                                {{ number_format($currency->exchange_rate ?? 0, 2) }}
                            </div>
                            <div class="metric-sub">Nilai tukar terbaru</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-orange">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <div class="metric-label">Perubahan Kurs</div>
                            <div class="metric-value">
                                {{ $currency->change_percentage ?? 0 }}%
                            </div>
                            <div class="metric-sub">Perubahan periode terakhir</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Risiko Kurs</div>
                            <div class="metric-value">
                                {{ $currency->currency_risk_score ?? 0 }}/100
                            </div>
                            <div class="metric-sub">Kontribusi terhadap risiko</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-flag"></i>
                        </div>
                        <div>
                            <div class="metric-label">Negara</div>
                            <div class="metric-value">{{ $country->name ?? '-' }}</div>
                            <div class="metric-sub">{{ $country->currency_code ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHART + INTERPRETATION -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card-clean">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mb-0">Grafik Perubahan Kurs</div>
                            <span class="risk-badge risk-medium">6 Bulan Terakhir</span>
                        </div>

                        <canvas id="currencyChart" height="125"></canvas>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-clean">
                        <div class="section-title">Interpretasi Dampak Kurs</div>

                        <div class="info-row">
                            <div class="info-label">Base Currency</div>
                            <div class="info-value">{{ $currency->base_currency ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Target Currency</div>
                            <div class="info-value">{{ $currency->target_currency ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Tanggal Data</div>
                            <div class="info-value">{{ $currency->rate_date ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">GDP Negara</div>
                            <div class="info-value">
                                ${{ number_format(($economic->gdp ?? 0) / 1000000000000, 2) }}T
                            </div>
                        </div>

                        <div class="recommendation-box mt-4">
                            <strong>Catatan Sistem:</strong>
                            <div class="mt-2">
                                Fluktuasi kurs dapat memengaruhi biaya impor. Jika nilai tukar melemah, perusahaan perlu mempertimbangkan penyesuaian harga dan waktu pembelian.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="card-clean">
                <div class="section-title">Perbandingan Kurs Antar Negara</div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Wilayah</th>
                            <th>Mata Uang</th>
                            <th>Base</th>
                            <th>Kurs</th>
                            <th>Perubahan</th>
                            <th>Risiko Kurs</th>
                            <th>Tanggal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($currencyRows as $row)
                            <tr>
                                <td><strong>{{ $row->country_name }}</strong></td>
                                <td>{{ $row->region ?? '-' }}</td>
                                <td>{{ $row->target_currency }}</td>
                                <td>{{ $row->base_currency }}</td>
                                <td>{{ number_format($row->exchange_rate, 2) }}</td>
                                <td>
                                    <span class="{{ $row->change_percentage >= 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $row->change_percentage }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="risk-badge
                                        {{ $row->currency_risk_score >= 60 ? 'risk-high' : ($row->currency_risk_score >= 35 ? 'risk-medium' : 'risk-low') }}">
                                        {{ $row->currency_risk_score }}/100
                                    </span>
                                </td>
                                <td>{{ $row->rate_date }}</td>
                            </tr>
                        @endforeach
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const currencyTrend = @json($currencyTrend);
    const targetCurrency = @json($currency->target_currency ?? '');

    new Chart(document.getElementById('currencyChart'), {
        type: 'line',
        data: {
            labels: ['Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
            datasets: [{
                label: 'USD/' + targetCurrency,
                data: currencyTrend,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.12)',
                pointBackgroundColor: '#8b5cf6',
                borderWidth: 3,
                pointRadius: 4,
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
            }
        }
    });
</script>
</body>
</html>