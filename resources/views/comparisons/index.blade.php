@extends('layouts.app')

@section('title', 'Perbandingan Negara - Supply Chain Management')

@section('content')
    <div class="topbar">
        <div class="page-title">
            <h1>Perbandingan Negara</h1>
            <p>Bandingkan risiko rantai pasok antar negara untuk mendukung keputusan impor.</p>
        </div>

        <form action="{{ route('comparisons.index') }}" method="GET" class="d-flex gap-2">
            <select name="first_country_id" class="form-select country-select" onchange="this.form.submit()">
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ $firstCountryId == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>

            <select name="second_country_id" class="form-select country-select" onchange="this.form.submit()">
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ $secondCountryId == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="content">
        <!-- COMPARISON HEADER -->
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card-clean text-center">
                    <div class="metric-label">Negara Pertama</div>
                    <div class="metric-value">{{ $firstCountry->name ?? '-' }}</div>
                    <div class="metric-sub">{{ $firstCountry->region ?? '-' }}</div>

                    <div class="mt-3">
                        <span class="risk-badge
                            {{ ($firstRisk->total_score ?? 0) >= 60 ? 'risk-high' : (($firstRisk->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                            {{ $firstRisk->risk_level ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="card-clean text-center d-flex align-items-center justify-content-center">
                    <div>
                        <div style="font-size: 28px; font-weight: 800;">VS</div>
                        <div class="metric-sub">Comparison</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-clean text-center">
                    <div class="metric-label">Negara Kedua</div>
                    <div class="metric-value">{{ $secondCountry->name ?? '-' }}</div>
                    <div class="metric-sub">{{ $secondCountry->region ?? '-' }}</div>

                    <div class="mt-3">
                        <span class="risk-badge
                            {{ ($secondRisk->total_score ?? 0) >= 60 ? 'risk-high' : (($secondRisk->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                            {{ $secondRisk->risk_level ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN METRICS -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card-clean">
                    <div class="metric-label">Skor Risiko</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-sub">{{ $firstCountry->name ?? '-' }}</div>
                            <div class="metric-value">{{ $firstRisk->total_score ?? 0 }}/100</div>
                        </div>

                        <div class="text-end">
                            <div class="metric-sub">{{ $secondCountry->name ?? '-' }}</div>
                            <div class="metric-value">{{ $secondRisk->total_score ?? 0 }}/100</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean">
                    <div class="metric-label">Inflasi</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-sub">{{ $firstCountry->name ?? '-' }}</div>
                            <div class="metric-value">{{ $firstEconomic->inflation_rate ?? 0 }}%</div>
                        </div>

                        <div class="text-end">
                            <div class="metric-sub">{{ $secondCountry->name ?? '-' }}</div>
                            <div class="metric-value">{{ $secondEconomic->inflation_rate ?? 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean">
                    <div class="metric-label">Cuaca</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-sub">{{ $firstCountry->name ?? '-' }}</div>
                            <div class="metric-value">{{ $firstWeather->temperature ?? 0 }}°C</div>
                        </div>

                        <div class="text-end">
                            <div class="metric-sub">{{ $secondCountry->name ?? '-' }}</div>
                            <div class="metric-value">{{ $secondWeather->temperature ?? 0 }}°C</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean">
                    <div class="metric-label">Kurs</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-sub">USD/{{ $firstCurrency->target_currency ?? '-' }}</div>
                            <div class="metric-value">
                                {{ number_format($firstCurrency->exchange_rate ?? 0, 2) }}
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="metric-sub">USD/{{ $secondCurrency->target_currency ?? '-' }}</div>
                            <div class="metric-value">
                                {{ number_format($secondCurrency->exchange_rate ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART + RECOMMENDATION -->
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card-clean">
                    <div class="section-title">Grafik Perbandingan Risiko</div>
                    <canvas id="comparisonChart" height="150"></canvas>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-clean">
                    <div class="section-title">Rekomendasi Impor</div>

                    <div class="recommendation-box">
                        <strong>Pilihan Lebih Aman:</strong>
                        <div class="metric-value mt-2">{{ $saferCountry }}</div>
                        <div class="mt-2">
                            {{ $recommendation }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="info-row">
                            <div class="info-label">Skor {{ $firstCountry->name ?? '-' }}</div>
                            <div class="info-value">{{ $firstRisk->total_score ?? 0 }}/100</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Skor {{ $secondCountry->name ?? '-' }}</div>
                            <div class="info-value">{{ $secondRisk->total_score ?? 0 }}/100</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Indikator Utama</div>
                            <div class="info-value">Risk Score</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAIL TABLE -->
        <div class="card-clean mb-4">
            <div class="section-title">Detail Perbandingan Indikator</div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Indikator</th>
                        <th>{{ $firstCountry->name ?? '-' }}</th>
                        <th>{{ $secondCountry->name ?? '-' }}</th>
                        <th>Lebih Baik</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td>Skor Risiko</td>
                        <td>{{ $firstRisk->total_score ?? 0 }}/100</td>
                        <td>{{ $secondRisk->total_score ?? 0 }}/100</td>
                        <td>
                            {{ ($firstRisk->total_score ?? 0) <= ($secondRisk->total_score ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-') }}
                        </td>
                    </tr>

                    <tr>
                        <td>Inflasi</td>
                        <td>{{ $firstEconomic->inflation_rate ?? 0 }}%</td>
                        <td>{{ $secondEconomic->inflation_rate ?? 0 }}%</td>
                        <td>
                            {{ ($firstEconomic->inflation_rate ?? 0) <= ($secondEconomic->inflation_rate ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-') }}
                        </td>
                    </tr>

                    <tr>
                        <td>Risiko Cuaca</td>
                        <td>{{ $firstRisk->weather_score ?? 0 }}%</td>
                        <td>{{ $secondRisk->weather_score ?? 0 }}%</td>
                        <td>
                            {{ ($firstRisk->weather_score ?? 0) <= ($secondRisk->weather_score ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-') }}
                        </td>
                    </tr>

                    <tr>
                        <td>Risiko Kurs</td>
                        <td>{{ $firstRisk->currency_score ?? 0 }}%</td>
                        <td>{{ $secondRisk->currency_score ?? 0 }}%</td>
                        <td>
                            {{ ($firstRisk->currency_score ?? 0) <= ($secondRisk->currency_score ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-') }}
                        </td>
                    </tr>

                    <tr>
                        <td>Sentimen Berita</td>
                        <td>{{ $firstRisk->news_score ?? 0 }}%</td>
                        <td>{{ $secondRisk->news_score ?? 0 }}%</td>
                        <td>
                            {{ ($firstRisk->news_score ?? 0) <= ($secondRisk->news_score ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-') }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- NEWS -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-clean">
                    <div class="section-title">Berita {{ $firstCountry->name ?? '-' }}</div>

                    @forelse ($firstNews as $item)
                        <div class="news-item">
                            <span class="risk-badge
                                {{ $item->sentiment === 'Negative' ? 'risk-high' : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium') }}">
                                {{ $item->sentiment }}
                            </span>

                            <div class="news-title mt-2">{{ $item->title }}</div>
                            <div class="news-desc">{{ $item->description }}</div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada berita.</p>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-clean">
                    <div class="section-title">Berita {{ $secondCountry->name ?? '-' }}</div>

                    @forelse ($secondNews as $item)
                        <div class="news-item">
                            <span class="risk-badge
                                {{ $item->sentiment === 'Negative' ? 'risk-high' : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium') }}">
                                {{ $item->sentiment }}
                            </span>

                            <div class="news-title mt-2">{{ $item->title }}</div>
                            <div class="news-desc">{{ $item->description }}</div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada berita.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const firstCountryName = @json($firstCountry->name ?? '-');
    const secondCountryName = @json($secondCountry->name ?? '-');

    const firstData = [
        Number(@json($firstRisk->total_score ?? 0)),
        Number(@json($firstRisk->weather_score ?? 0)),
        Number(@json($firstRisk->inflation_score ?? 0)),
        Number(@json($firstRisk->currency_score ?? 0)),
        Number(@json($firstRisk->news_score ?? 0))
    ];

    const secondData = [
        Number(@json($secondRisk->total_score ?? 0)),
        Number(@json($secondRisk->weather_score ?? 0)),
        Number(@json($secondRisk->inflation_score ?? 0)),
        Number(@json($secondRisk->currency_score ?? 0)),
        Number(@json($secondRisk->news_score ?? 0))
    ];

    new Chart(document.getElementById('comparisonChart'), {
        type: 'bar',
        data: {
            labels: ['Total', 'Cuaca', 'Inflasi', 'Kurs', 'Berita'],
            datasets: [
                {
                    label: firstCountryName,
                    data: firstData,
                    backgroundColor: '#2563eb'
                },
                {
                    label: secondCountryName,
                    data: secondData,
                    backgroundColor: '#f59e0b'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endpush