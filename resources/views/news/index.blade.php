@extends('layouts.app')

@section('title', 'Sinyal Berita - Supply Chain Management')

@section('content')
    @php
        $positivePercentage = (float) ($sentimentSummary['positive_percentage'] ?? 0);
        $neutralPercentage = (float) ($sentimentSummary['neutral_percentage'] ?? 0);
        $negativePercentage = (float) ($sentimentSummary['negative_percentage'] ?? 0);

        $positiveCount = (int) ($sentimentSummary['positive_count'] ?? 0);
        $neutralCount = (int) ($sentimentSummary['neutral_count'] ?? 0);
        $negativeCount = (int) ($sentimentSummary['negative_count'] ?? 0);

        $sentimentClass = function ($sentiment) {
            return match ($sentiment) {
                'Positive' => 'risk-low',
                'Negative' => 'risk-high',
                default => 'risk-medium',
            };
        };
    @endphp

    {{-- TOPBAR --}}
    <div class="topbar">
        <div class="page-title">
            <h1>Sinyal Berita</h1>

            <p>
                Analisis berita logistik, ekonomi, perdagangan,
                dan sentimen risiko rantai pasok.
            </p>
        </div>

        <div class="scm-topbar-tools">
            <form
                action="{{ route('news.index') }}"
                method="GET"
            >
                <select
                    name="country_id"
                    class="form-select country-select"
                    onchange="this.form.submit()"
                    aria-label="Pilih negara"
                >
                    @foreach ($countries as $item)
                        <option
                            value="{{ $item->id }}"
                            {{ (string) $selectedCountryId === (string) $item->id ? 'selected' : '' }}
                        >
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="content news-page">

        {{-- PESAN SESSION --}}
        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup"
                ></button>
            </div>
        @endif

        @if (session('error'))
            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >
                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup"
                ></button>
            </div>
        @endif

        {{-- KPI BERITA --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card h-100">
                    <div class="metric-icon icon-blue">
                        <i class="bi bi-newspaper"></i>
                    </div>

                    <div>
                        <div class="metric-label">
                            Total Berita
                        </div>

                        <div class="metric-value">
                            {{ number_format($newsItems->count()) }}
                        </div>

                        <div class="metric-sub">
                            {{ $country->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card h-100">
                    <div class="metric-icon icon-green">
                        <i class="bi bi-emoji-smile"></i>
                    </div>

                    <div>
                        <div class="metric-label">
                            Sentimen Positif
                        </div>

                        <div class="metric-value">
                            {{ number_format($positivePercentage, 1) }}%
                        </div>

                        <div class="metric-sub">
                            {{ number_format($positiveCount) }} berita
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card h-100">
                    <div class="metric-icon icon-amber">
                        <i class="bi bi-dash-circle"></i>
                    </div>

                    <div>
                        <div class="metric-label">
                            Sentimen Netral
                        </div>

                        <div class="metric-value">
                            {{ number_format($neutralPercentage, 1) }}%
                        </div>

                        <div class="metric-sub">
                            {{ number_format($neutralCount) }} berita
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card h-100">
                    <div class="metric-icon icon-coral">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>

                    <div>
                        <div class="metric-label">
                            Sentimen Negatif
                        </div>

                        <div class="metric-value">
                            {{ number_format($negativePercentage, 1) }}%
                        </div>

                        <div class="metric-sub">
                            {{ number_format($negativeCount) }} berita
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK DAN KAMUS SENTIMEN --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-5">
                <div class="card-clean h-100">
                    <div class="section-title">
                        Ringkasan Sentimen {{ $country->name ?? '-' }}
                    </div>

                    @if ($newsItems->isNotEmpty())
                        <div class="sentiment-chart-wrap">
                            <canvas id="sentimentChart"></canvas>
                        </div>
                    @else
                        <div class="scm-empty-state">
                            Belum ada data berita untuk membentuk grafik sentimen.
                        </div>
                    @endif

                    <div class="recommendation-box mt-4">
                        <strong>Interpretasi</strong>

                        <div class="mt-2">
                            Sentimen berita digunakan untuk melihat potensi
                            gangguan rantai pasok dari aspek ekonomi, logistik,
                            perdagangan, dan isu geopolitik.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="card-clean h-100">
                    <div class="section-title">
                        Kamus Sentimen Berita
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="indicator-box indicator-green">
                                <div class="metric-label">
                                    Kata Positif
                                </div>

                                <div class="mt-2 sentiment-word-list">
                                    @forelse ($positiveWords as $word)
                                        <span class="risk-badge risk-low me-1 mb-2">
                                            {{ $word->word }}
                                        </span>
                                    @empty
                                        <span class="text-muted">
                                            Belum ada kata positif.
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="indicator-box indicator-red">
                                <div class="metric-label">
                                    Kata Negatif
                                </div>

                                <div class="mt-2 sentiment-word-list">
                                    @forelse ($negativeWords as $word)
                                        <span class="risk-badge risk-high me-1 mb-2">
                                            {{ $word->word }}
                                        </span>
                                    @empty
                                        <span class="text-muted">
                                            Belum ada kata negatif.
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="recommendation-box mt-4">
                        <strong>Metode</strong>

                        <div class="mt-2">
                            Sistem menggunakan pendekatan
                            <em>lexicon-based sentiment analysis</em>,
                            yaitu menghitung kata positif dan negatif
                            untuk menentukan kecenderungan sentimen berita.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BERITA NEGARA DAN SEMUA SINYAL --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-6">
                <div class="card-clean news-panel">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div class="section-title mb-0">
                            Berita {{ $country->name ?? '-' }}
                        </div>

                        <span class="risk-badge risk-low">
                            {{ $newsItems->count() }} berita
                        </span>
                    </div>

                    <div class="news-scroll-area">
                        @forelse ($newsItems as $item)
                            <article class="news-item">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                    <span
                                        class="risk-badge {{ $sentimentClass($item->sentiment ?? null) }}"
                                    >
                                        {{ $item->sentiment ?? 'Neutral' }}
                                    </span>

                                    <small class="text-muted">
                                        {{ $item->category ?? 'Umum' }}
                                    </small>
                                </div>

                                <div class="news-title">
                                    {{ $item->title }}
                                </div>

                                <div class="news-desc">
                                    {{ $item->description ?? 'Tidak ada deskripsi.' }}
                                </div>

                                <div class="news-meta mt-2">
                                    <span>
                                        <i class="bi bi-building me-1"></i>
                                        {{ $item->source ?? '-' }}
                                    </span>

                                    <span>
                                        Positif:
                                        {{ (int) ($item->positive_score ?? 0) }}
                                    </span>

                                    <span>
                                        Negatif:
                                        {{ (int) ($item->negative_score ?? 0) }}
                                    </span>
                                </div>

                                @if (!empty($item->url))
                                    <a
                                        href="{{ $item->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-sm btn-outline-scm mt-3"
                                    >
                                        <i class="bi bi-box-arrow-up-right me-1"></i>
                                        Buka Sumber
                                    </a>
                                @endif
                            </article>
                        @empty
                            <div class="scm-empty-state">
                                Belum ada berita untuk negara ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card-clean news-panel">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div class="section-title mb-0">
                            Seluruh Sinyal Berita
                        </div>

                        <span class="risk-badge risk-low">
                            {{ $allNews->count() }} data
                        </span>
                    </div>

                    <div class="table-responsive news-table-wrap">
                        <table class="table table-hover align-middle">
                            <thead>
                            <tr>
                                <th>Negara</th>
                                <th>Kategori</th>
                                <th>Sentimen</th>
                                <th>Skor</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($allNews as $item)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $item->country_name ?? '-' }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $item->category ?? 'Umum' }}
                                    </td>

                                    <td>
                                        <span
                                            class="risk-badge {{ $sentimentClass($item->sentiment ?? null) }}"
                                        >
                                            {{ $item->sentiment ?? 'Neutral' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="text-success">
                                            +{{ (int) ($item->positive_score ?? 0) }}
                                        </span>

                                        <span class="text-muted mx-1">
                                            /
                                        </span>

                                        <span class="text-danger">
                                            -{{ (int) ($item->negative_score ?? 0) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="4"
                                        class="text-center text-muted py-4"
                                    >
                                        Belum ada sinyal berita.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} Supply Chain Management.
            Semua hak dilindungi.
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sentimentCanvas =
            document.getElementById('sentimentChart');

        if (!sentimentCanvas || typeof Chart === 'undefined') {
            return;
        }

        if (
            window.newsSentimentChart &&
            typeof window.newsSentimentChart.destroy === 'function'
        ) {
            window.newsSentimentChart.destroy();
        }

        const positivePercentage =
            Number(@json($positivePercentage));

        const neutralPercentage =
            Number(@json($neutralPercentage));

        const negativePercentage =
            Number(@json($negativePercentage));

        window.newsSentimentChart = new Chart(
            sentimentCanvas,
            {
                type: 'doughnut',

                data: {
                    labels: [
                        'Positif',
                        'Netral',
                        'Negatif'
                    ],

                    datasets: [
                        {
                            data: [
                                positivePercentage,
                                neutralPercentage,
                                negativePercentage
                            ],

                            backgroundColor: [
                                '#3C8C68',
                                '#D99A2B',
                                '#E76F51'
                            ],

                            hoverBackgroundColor: [
                                '#327A59',
                                '#C08724',
                                '#D65E41'
                            ],

                            borderColor: '#FFFFFF',
                            borderWidth: 4
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',

                    plugins: {
                        legend: {
                            position: 'bottom',

                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 18
                            }
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const value =
                                        Number(context.raw || 0);

                                    return `${context.label}: ${value.toFixed(1)}%`;
                                }
                            }
                        }
                    }
                }
            }
        );
    });
</script>
@endpush