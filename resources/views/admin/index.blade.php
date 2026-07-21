@extends('layouts.app')

@section('title', 'Panel Admin - Supply Chain Management')

@push('styles')
<style>
    #users-section,
    #countries-section,
    #ports-section,
    #articles-section {
        scroll-margin-top: 24px;
    }
</style>
@endpush

@section('content')
<div class="topbar">
    <div class="page-title">
        <h1>Panel Admin</h1>
        <p>
            Kelola ringkasan data sistem, user, negara, pelabuhan,
            artikel, API log, dan kamus sentimen.
        </p>
    </div>
</div>

<div class="content">
    <div id="adminAjaxAlert"></div>

    {{-- PESAN BERHASIL --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif

    {{-- PESAN ERROR --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Data belum dapat disimpan.</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- API SYNC NEGARA WATCHLIST --}}
    <div class="card-clean mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="section-title mb-1">
                    Sinkronisasi API Eksternal
                </div>

                <div class="metric-sub">
                    Ambil data terbaru Open-Meteo, ExchangeRate-API,
                    dan GNews untuk negara yang terdapat di Watchlist,
                    kemudian hitung ulang skor risiko.
                </div>

                <div class="metric-sub mt-2">
                    Sinkronisasi otomatis dijalankan melalui Laravel Scheduler.
                    Tombol ini digunakan untuk pembaruan manual.
                </div>
            </div>

            <form
                action="{{ route('admin.api.sync') }}"
                method="POST"
                onsubmit="return confirm('Sinkronkan data API untuk negara di Watchlist sekarang?')"
            >
                @csrf

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-arrow-down me-1"></i>
                    Sync API Negara Watchlist
                </button>
            </form>
        </div>
    </div>

    {{-- WORLD BANK SYNC --}}
    <div class="card-clean mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="section-title mb-1">
                    Sinkronisasi Data Ekonomi
                </div>

                <div class="metric-sub">
                    Ambil GDP, inflasi, populasi, ekspor, dan impor
                    terbaru dari World Bank API.
                </div>
            </div>

            <form
                action="{{ route('admin.world_bank.sync') }}"
                method="POST"
                onsubmit="return confirm('Sinkronkan data ekonomi semua negara dari World Bank sekarang?')"
            >
                @csrf

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-bank me-1"></i>
                    Sync World Bank
                </button>
            </form>
        </div>
    </div>

    {{-- RISK SCORING NEGARA --}}
    <div class="card-clean mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="section-title mb-1">
                    Risk Scoring Otomatis
                </div>

                <div class="metric-sub">
                    Hitung ulang skor risiko berdasarkan cuaca,
                    inflasi, kurs, dan sentimen berita.
                </div>
            </div>

            <form
                action="{{ route('admin.risk.recalculate') }}"
                method="POST"
                onsubmit="return confirm('Hitung ulang skor risiko seluruh negara sekarang?')"
            >
                @csrf

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Hitung Ulang Skor Risiko
                </button>
            </form>
        </div>
    </div>

    {{-- SYNC NEGARA DARI REST COUNTRIES --}}
    <div class="card-clean mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="section-title mb-1">
                    Sinkronisasi Data Negara
                </div>

                <div class="metric-sub">
                    Ambil daftar negara dari REST Countries API agar
                    negara tidak perlu ditambahkan satu per satu.
                </div>
            </div>

            <form
                action="{{ route('admin.countries.sync_api') }}"
                method="POST"
                onsubmit="return confirm('Sinkronkan data negara dari REST Countries API sekarang?')"
            >
                @csrf

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-globe2 me-1"></i>
                    Sync Negara dari API
                </button>
            </form>
        </div>
    </div>

    {{-- SYNC WORLD PORT INDEX --}}
    <div class="card-clean mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="section-title mb-1">
                    Sinkronisasi World Port Index
                </div>

                <div class="metric-sub">
                    Ambil data pelabuhan dunia dari ArcGIS World Port Index.
                </div>
            </div>

            <form
                action="{{ route('admin.ports.sync_world_port_index') }}"
                method="POST"
                onsubmit="return confirm('Sinkronkan data World Port Index sekarang?')"
            >
                @csrf

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-anchor me-1"></i>
                    Sync World Port Index
                </button>
            </form>
        </div>
    </div>

    {{-- HITUNG RISIKO PELABUHAN --}}
    <div class="card-clean mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="section-title mb-1">
                    Risk Scoring Pelabuhan
                </div>

                <div class="metric-sub">
                    Hitung ulang status dan skor risiko seluruh pelabuhan.
                </div>
            </div>

            <form
                action="{{ route('admin.ports.recalculate_risk') }}"
                method="POST"
                onsubmit="return confirm('Hitung ulang risiko seluruh pelabuhan sekarang?')"
            >
                @csrf

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Hitung Risiko Pelabuhan
                </button>
            </form>
        </div>
    </div>

    {{-- TAMBAH NEGARA --}}
    <div class="card-clean mb-4">
        <div class="section-title">
            Tambah Negara Baru
        </div>

        <form action="{{ route('admin.countries.store') }}" method="POST" class="data-ajax-add-form" data-refresh-section="countries-section">
            @csrf

            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Kode Negara
                    </label>

                    <input
                        type="text"
                        name="country_code"
                        class="form-control"
                        value="{{ old('country_code') }}"
                        placeholder="JP"
                        maxlength="3"
                        required
                    >

                    <div class="metric-sub mt-1">
                        Contoh: JP, SG, TH
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Nama Negara
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Japan"
                        required
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Ibu Kota
                    </label>

                    <input
                        type="text"
                        name="capital"
                        class="form-control"
                        value="{{ old('capital') }}"
                        placeholder="Tokyo"
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Region
                    </label>

                    <input
                        type="text"
                        name="region"
                        class="form-control"
                        value="{{ old('region') }}"
                        placeholder="Asia Timur"
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Kode Mata Uang
                    </label>

                    <input
                        type="text"
                        name="currency_code"
                        class="form-control"
                        value="{{ old('currency_code') }}"
                        placeholder="JPY"
                        maxlength="3"
                        required
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Nama Mata Uang
                    </label>

                    <input
                        type="text"
                        name="currency_name"
                        class="form-control"
                        value="{{ old('currency_name') }}"
                        placeholder="Yen"
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Bahasa
                    </label>

                    <input
                        type="text"
                        name="language"
                        class="form-control"
                        value="{{ old('language') }}"
                        placeholder="Japanese"
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Exchange Rate
                    </label>

                    <input
                        type="number"
                        step="0.000001"
                        min="0"
                        name="exchange_rate"
                        class="form-control"
                        value="{{ old('exchange_rate') }}"
                        placeholder="157.35"
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Latitude
                    </label>

                    <input
                        type="number"
                        step="0.0000001"
                        min="-90"
                        max="90"
                        name="latitude"
                        class="form-control"
                        value="{{ old('latitude') }}"
                        placeholder="35.6762"
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Longitude
                    </label>

                    <input
                        type="number"
                        step="0.0000001"
                        min="-180"
                        max="180"
                        name="longitude"
                        class="form-control"
                        value="{{ old('longitude') }}"
                        placeholder="139.6503"
                    >
                </div>

                <div class="col-lg-6 col-md-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Negara ke Sistem
                    </button>
                </div>
            </div>
        </form>

        <div class="recommendation-box mt-4">
            <strong>Catatan:</strong>

            <div class="mt-2">
                Setelah negara ditambahkan, sistem otomatis membuat
                data awal agar negara dapat digunakan pada halaman sistem.
            </div>
        </div>
    </div>

    {{-- SUMMARY BARIS PERTAMA --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-blue">
                    <i class="bi bi-people"></i>
                </div>

                <div>
                    <div class="metric-label">User</div>

                    <div class="metric-value">
                        {{ $summary['users_count'] ?? 0 }}
                    </div>

                    <div class="metric-sub">
                        Pengguna sistem
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-green">
                    <i class="bi bi-globe2"></i>
                </div>

                <div>
                    <div class="metric-label">Negara</div>

                    <div class="metric-value">
                        {{ $summary['countries_count'] ?? 0 }}
                    </div>

                    <div class="metric-sub">
                        Data negara
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-orange">
                    <i class="bi bi-pin-map-fill"></i>
                </div>

                <div>
                    <div class="metric-label">Pelabuhan</div>

                    <div class="metric-value">
                        {{ $summary['ports_count'] ?? 0 }}
                    </div>

                    <div class="metric-sub">
                        Dataset pelabuhan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-red">
                    <i class="bi bi-newspaper"></i>
                </div>

                <div>
                    <div class="metric-label">Berita</div>

                    <div class="metric-value">
                        {{ $summary['news_count'] ?? 0 }}
                    </div>

                    <div class="metric-sub">
                        News cache
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SUMMARY BARIS KEDUA --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-purple">
                    <i class="bi bi-file-text"></i>
                </div>

                <div>
                    <div class="metric-label">Artikel</div>

                    <div class="metric-value">
                        {{ $summary['articles_count'] ?? 0 }}
                    </div>

                    <div class="metric-sub">
                        Artikel analisis
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-blue">
                    <i class="bi bi-star"></i>
                </div>

                <div>
                    <div class="metric-label">Watchlist</div>

                    <div class="metric-value">
                        {{ $summary['watchlists_count'] ?? 0 }}
                    </div>

                    <div class="metric-sub">
                        Negara dipantau
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-green">
                    <i class="bi bi-plus-circle"></i>
                </div>

                <div>
                    <div class="metric-label">Positive Words</div>

                    <div class="metric-value">
                        {{ $summary['positive_words_count'] ?? 0 }}
                    </div>

                    <div class="metric-sub">
                        Kamus positif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-red">
                    <i class="bi bi-dash-circle"></i>
                </div>

                <div>
                    <div class="metric-label">Negative Words</div>

                    <div class="metric-value">
                        {{ $summary['negative_words_count'] ?? 0 }}
                    </div>

                    <div class="metric-sub">
                        Kamus negatif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KELOLA USER --}}
    <div id="users-section" class="card-clean mb-4">
        <div class="section-title">
            Kelola User
        </div>

        <form
            action="{{ route('admin.users.store') }}"
            method="POST"
            class="mb-4 data-ajax-add-form"
            data-refresh-section="users-section"
        >
            @csrf

            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Nama
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">
                        Role
                    </label>

                    <select name="role" class="form-select" required>
                        <option
                            value="user"
                            {{ old('role', 'user') === 'user' ? 'selected' : '' }}
                        >
                            User
                        </option>

                        <option
                            value="admin"
                            {{ old('role') === 'admin' ? 'selected' : '' }}
                        >
                            Admin
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        required
                    >
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>
                        Tambah User
                    </button>
                </div>
            </div>
        </form>

        <form action="{{ route('admin.index') }}#users-section" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-4">
                    <input
                        type="text"
                        name="user_search"
                        class="form-control"
                        placeholder="Cari nama atau email user..."
                        value="{{ request('user_search') }}"
                    >
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.index') }}#users-section" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong>

                            @if ((int) $user->id === (int) auth()->id())
                                <span class="risk-badge risk-low ms-1">
                                    Anda
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $user->email }}
                        </td>

                        <td>
                            <span
                                class="risk-badge {{ ($user->role ?? 'user') === 'admin' ? 'risk-high' : 'risk-low' }}"
                            >
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </td>

                        <td>
                            {{ $user->created_at
                                ? \Carbon\Carbon::parse($user->created_at)->format('d M Y H:i')
                                : '-'
                            }}
                        </td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a
                                    href="{{ route('admin.users.edit', $user->id) }}"
                                    class="btn btn-sm btn-outline-primary ajax-edit-btn"
                                >
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Edit
                                </a>

                                @if ((int) $user->id !== (int) auth()->id())
                                    <form
                                        action="{{ route('admin.users.destroy', $user->id) }}"
                                        method="POST"
                                        class="data-ajax-delete-form"
                                        data-refresh-section="users-section"
                                        data-confirm="Yakin ingin menghapus user ini?"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            <i class="bi bi-trash me-1"></i>
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users
                ->onEachSide(1)
                ->withQueryString()
                ->fragment('users-section')
                ->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- API LOG --}}
    <div class="card-clean mb-4">
        <div class="section-title">
            API Logs
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>API</th>
                    <th>Status</th>
                    <th>Pesan</th>
                    <th>Waktu</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($apiLogs as $log)
                    <tr>
                        <td>
                            <strong>{{ $log->api_name }}</strong>

                            <div class="metric-sub">
                                {{ $log->endpoint ?? '-' }}
                            </div>
                        </td>

                        <td>
                            <span
                                class="risk-badge {{ $log->status === 'Success' ? 'risk-low' : 'risk-high' }}"
                            >
                                {{ $log->status }}
                            </span>
                        </td>

                        <td>
                            <div class="metric-sub">
                                {{ $log->message ?? '-' }}
                            </div>
                        </td>

                        <td>
                            {{ $log->requested_at
                                ? \Carbon\Carbon::parse($log->requested_at)->format('d M Y H:i') . ' WIB'
                                : '-'
                            }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada log API.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DATASET NEGARA --}}
    <div id="countries-section" class="card-clean mb-4">
        <div class="section-title">
            Dataset Negara dan Risiko
        </div>

        <form action="{{ route('admin.index') }}#countries-section" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-4">
                    <input
                        type="text"
                        name="country_search"
                        class="form-control"
                        placeholder="Cari negara, kode, region, atau mata uang..."
                        value="{{ request('country_search') }}"
                    >
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.index') }}#countries-section" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Negara</th>
                    <th>Wilayah</th>
                    <th>Mata Uang</th>
                    <th>Skor Risiko</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($countries as $country)
                    <tr>
                        <td>
                            <strong>{{ $country->name }}</strong>
                        </td>

                        <td>
                            {{ $country->region ?? '-' }}
                        </td>

                        <td>
                            {{ $country->currency_code ?? '-' }}
                        </td>

                        <td>
                            {{ $country->total_score ?? 0 }}/100
                        </td>

                        <td>
                            @php
                                $countryRiskScore = (float) ($country->total_score ?? 0);

                                $countryRiskClass = $countryRiskScore >= 60
                                    ? 'risk-high'
                                    : ($countryRiskScore >= 35
                                        ? 'risk-medium'
                                        : 'risk-low');
                            @endphp

                            <span class="risk-badge {{ $countryRiskClass }}">
                                {{ $country->risk_level ?? 'Belum dihitung' }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a
                                    href="{{ route('admin.countries.edit', $country->id) }}"
                                    class="btn btn-sm btn-outline-primary ajax-edit-btn"
                                >
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Edit
                                </a>

                                <form
                                    action="{{ route('admin.countries.destroy', $country->id) }}"
                                    method="POST"
                                    class="data-ajax-delete-form"
                                    data-refresh-section="countries-section"
                                    data-confirm="Yakin ingin menghapus negara ini? Semua data terkait negara ini juga akan dihapus."
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        <i class="bi bi-trash me-1"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $countries
                ->onEachSide(1)
                ->withQueryString()
                ->fragment('countries-section')
                ->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- TAMBAH PELABUHAN --}}
    <div class="card-clean mb-4">
        <div class="section-title">
            Tambah Pelabuhan Baru
        </div>

        <div class="metric-sub mb-3">
            Pelabuhan yang ditambahkan akan langsung tersedia pada
            halaman Pelabuhan dan peta tracking.
        </div>

        <form action="{{ route('admin.ports.store') }}" method="POST" class="data-ajax-add-form" data-refresh-section="ports-section">
            @csrf

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">
                        Nama Pelabuhan
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Pelabuhan Tanjung Priok"
                        required
                    >
                </div>

                <div class="col-lg-4 col-md-6">
                    <label class="form-label">
                        Kota
                    </label>

                    <input
                        type="text"
                        name="city"
                        class="form-control"
                        value="{{ old('city') }}"
                        placeholder="Jakarta"
                    >
                </div>

                <div class="col-lg-4 col-md-6">
                    <label class="form-label">
                        Negara
                    </label>

                    <select
                        name="country_id"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Pilih negara
                        </option>

                        @foreach ($countryOptions as $country)
                            <option
                                value="{{ $country->id }}"
                                {{ (string) old('country_id') === (string) $country->id ? 'selected' : '' }}
                            >
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Latitude
                    </label>

                    <input
                        type="number"
                        name="latitude"
                        class="form-control"
                        value="{{ old('latitude') }}"
                        min="-90"
                        max="90"
                        step="0.0000001"
                        placeholder="-6.1040"
                        required
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Longitude
                    </label>

                    <input
                        type="number"
                        name="longitude"
                        class="form-control"
                        value="{{ old('longitude') }}"
                        min="-180"
                        max="180"
                        step="0.0000001"
                        placeholder="106.8800"
                        required
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Status
                    </label>

                    <select name="status" class="form-select" required>
                        <option
                            value="Aman"
                            {{ old('status', 'Aman') === 'Aman' ? 'selected' : '' }}
                        >
                            Aman
                        </option>

                        <option
                            value="Waspada"
                            {{ old('status') === 'Waspada' ? 'selected' : '' }}
                        >
                            Waspada
                        </option>

                        <option
                            value="Siaga"
                            {{ old('status') === 'Siaga' ? 'selected' : '' }}
                        >
                            Siaga
                        </option>

                        <option
                            value="Darurat"
                            {{ old('status') === 'Darurat' ? 'selected' : '' }}
                        >
                            Darurat
                        </option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Skor Risiko
                    </label>

                    <input
                        type="number"
                        name="port_risk_score"
                        class="form-control"
                        value="{{ old('port_risk_score', 0) }}"
                        min="0"
                        max="100"
                        step="0.01"
                        required
                    >
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Pelabuhan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- DATASET PELABUHAN --}}
    <div id="ports-section" class="card-clean mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <div class="section-title mb-1">
                    Dataset Pelabuhan
                </div>

                <div class="metric-sub">
                    Kelola data pelabuhan yang digunakan pada halaman
                    peta dan tracking.
                </div>
            </div>

            <span class="risk-badge risk-low">
                {{ $ports->total() }} pelabuhan
            </span>
        </div>

        <form action="{{ route('admin.index') }}#ports-section" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-4 col-lg-3">
                    <input
                        type="text"
                        name="port_search"
                        class="form-control"
                        placeholder="Cari pelabuhan, kota, negara..."
                        value="{{ request('port_search') }}"
                    >
                </div>
                <div class="col-md-3 col-lg-3">
                    <select name="port_country_id" class="form-select">
                        <option value="">Semua Negara</option>
                        @foreach ($countryOptions as $countryOpt)
                            <option
                                value="{{ $countryOpt->id }}"
                                {{ (string) request('port_country_id') === (string) $countryOpt->id ? 'selected' : '' }}
                            >
                                {{ $countryOpt->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-3">
                    <select name="port_status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach (['Aman', 'Normal', 'Waspada', 'Siaga', 'Darurat'] as $statusOpt)
                            <option
                                value="{{ $statusOpt }}"
                                {{ request('port_status') === $statusOpt ? 'selected' : '' }}
                            >
                                {{ $statusOpt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.index') }}#ports-section" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Pelabuhan</th>
                    <th>Negara</th>
                    <th>Status</th>
                    <th>Risiko</th>
                    <th>Aksi</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($ports as $port)
                    @php
                        $portStatus = $port->status ?? 'Aman';

                        $portStatusClass = in_array(
                            $portStatus,
                            ['Aman', 'Normal'],
                            true
                        )
                            ? 'risk-low'
                            : ($portStatus === 'Waspada'
                                ? 'risk-medium'
                                : 'risk-high');
                    @endphp

                    <tr>
                        <td>
                            <i class="bi bi-pin-map-fill text-primary me-1"></i>

                            <strong>
                                {{ $port->name }}
                            </strong>

                            <div class="metric-sub">
                                {{ $port->city ?? '-' }}
                            </div>
                        </td>

                        <td>
                            {{ $port->country_name ?? '-' }}
                        </td>

                        <td>
                            <span class="risk-badge {{ $portStatusClass }}">
                                {{ $portStatus }}
                            </span>
                        </td>

                        <td>
                            <strong>
                                {{ number_format((float) ($port->port_risk_score ?? 0), 0) }}/100
                            </strong>
                        </td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a
                                    href="{{ route('admin.ports.edit', $port->id) }}"
                                    class="btn btn-sm btn-outline-primary ajax-edit-btn"
                                >
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Edit
                                </a>

                                <form
                                    action="{{ route('admin.ports.destroy', $port->id) }}"
                                    method="POST"
                                    class="data-ajax-delete-form"
                                    data-refresh-section="ports-section"
                                    data-confirm="Yakin ingin menghapus pelabuhan {{ addslashes($port->name) }}?"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        <i class="bi bi-trash me-1"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $ports
                ->onEachSide(1)
                ->withQueryString()
                ->fragment('ports-section')
                ->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- TAMBAH ARTIKEL --}}
    <div class="card-clean mb-4">
        <div class="section-title">
            Tambah Artikel Analisis
        </div>

        <form action="{{ route('admin.articles.store') }}" method="POST" class="data-ajax-add-form" data-refresh-section="articles-section">
            @csrf

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">
                        Judul Artikel
                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="{{ old('title') }}"
                        placeholder="Analisis Risiko Rantai Pasok Asia"
                        required
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Kategori
                    </label>

                    <input
                        type="text"
                        name="category"
                        class="form-control"
                        value="{{ old('category') }}"
                        placeholder="Analisis / Ekonomi / Logistik"
                    >
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Status
                    </label>

                    <select name="status" class="form-select" required>
                        <option
                            value="Draft"
                            {{ old('status', 'Draft') === 'Draft' ? 'selected' : '' }}
                        >
                            Draft
                        </option>

                        <option
                            value="Published"
                            {{ old('status') === 'Published' ? 'selected' : '' }}
                        >
                            Published
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Artikel
                    </button>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Isi Artikel
                    </label>

                    <textarea
                        name="content"
                        rows="5"
                        class="form-control"
                        placeholder="Tulis ringkasan analisis risiko rantai pasok di sini..."
                        required
                    >{{ old('content') }}</textarea>
                </div>
            </div>
        </form>
    </div>

    {{-- ARTIKEL ANALISIS --}}
    <div id="articles-section" class="card-clean mb-4">
        <div class="section-title">
            Artikel Analisis
        </div>

        <form action="{{ route('admin.index') }}#articles-section" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-4">
                    <input
                        type="text"
                        name="article_search"
                        class="form-control"
                        placeholder="Cari judul, kategori, atau status artikel..."
                        value="{{ request('article_search') }}"
                    >
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.index') }}#articles-section" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        @forelse ($articles as $article)
            <div class="news-item">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span
                        class="risk-badge {{ $article->status === 'Published' ? 'risk-low' : 'risk-medium' }}"
                    >
                        {{ $article->status }}
                    </span>

                    <small class="text-muted">
                        {{ $article->category ?? '-' }}
                    </small>
                </div>

                <div class="news-title">
                    {{ $article->title }}
                </div>

                <div class="news-desc mb-3">
                    Penulis: {{ $article->author_name ?? 'Admin' }}
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a
                        href="{{ route('admin.articles.edit', $article->id) }}"
                        class="btn btn-sm btn-outline-primary ajax-edit-btn"
                    >
                        <i class="bi bi-pencil-square me-1"></i>
                        Edit
                    </a>

                    <form
                        action="{{ route('admin.articles.destroy', $article->id) }}"
                        method="POST"
                        class="data-ajax-delete-form"
                        data-refresh-section="articles-section"
                        data-confirm="Yakin ingin menghapus artikel ini?"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-sm btn-outline-danger"
                        >
                            <i class="bi bi-trash me-1"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">
                Data tidak ditemukan.
            </p>
        @endforelse

        <div class="mt-3">
            {{ $articles
                ->onEachSide(1)
                ->withQueryString()
                ->fragment('articles-section')
                ->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- KAMUS SENTIMEN --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card-clean h-100">
                <div class="section-title">
                    Kamus Kata Positif
                </div>

                @forelse ($positiveWords as $word)
                    <span class="risk-badge risk-low me-1 mb-2">
                        {{ $word->word }}
                    </span>
                @empty
                    <p class="text-muted mb-0">
                        Belum ada kata positif.
                    </p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-clean h-100">
                <div class="section-title">
                    Kamus Kata Negatif
                </div>

                @forelse ($negativeWords as $word)
                    <span class="risk-badge risk-high me-1 mb-2">
                        {{ $word->word }}
                    </span>
                @empty
                    <p class="text-muted mb-0">
                        Belum ada kata negatif.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="footer">
        © {{ date('Y') }} Supply Chain Management.
        Semua hak dilindungi.
    </div>

    <!-- Admin Edit Modal -->
    <div class="modal fade" id="adminEditModal" tabindex="-1" aria-labelledby="adminEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminEditModalLabel">Edit Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="adminEditModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-ajax.js') }}?v={{ file_exists(public_path('js/admin-ajax.js')) ? filemtime(public_path('js/admin-ajax.js')) : time() }}"></script>
@endpush
