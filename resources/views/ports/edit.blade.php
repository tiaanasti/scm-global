@extends('layouts.app')

@section('title', 'Edit Pelabuhan - Supply Chain Management')

@section('content')
<div class="topbar">
    <div class="page-title">
        <h1>Edit Pelabuhan</h1>
        <p>
            Perbarui informasi pelabuhan, lokasi, status operasional,
            dan skor risikonya.
        </p>
    </div>
</div>

<div class="content">

    {{-- PESAN ERROR --}}
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

    <div class="card-clean">
        <div
            class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"
        >
            <div>
                <div class="section-title mb-1">
                    Form Edit Pelabuhan
                </div>

                <div class="metric-sub">
                    ID Pelabuhan: {{ $port->id }}
                </div>
            </div>

            <a
                href="{{ route('admin.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Panel Admin
            </a>
        </div>

        <form
            action="{{ route('admin.ports.update', $port->id) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="row g-3">

                {{-- NAMA PELABUHAN --}}
                <div class="col-lg-6 col-md-12">
                    <label for="name" class="form-label">
                        Nama Pelabuhan
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $port->name) }}"
                        placeholder="Contoh: Pelabuhan Tanjung Priok"
                        maxlength="255"
                        required
                        autofocus
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- KOTA --}}
                <div class="col-lg-6 col-md-12">
                    <label for="city" class="form-label">
                        Kota
                    </label>

                    <input
                        type="text"
                        id="city"
                        name="city"
                        class="form-control @error('city') is-invalid @enderror"
                        value="{{ old('city', $port->city) }}"
                        placeholder="Contoh: Jakarta"
                        maxlength="255"
                    >

                    @error('city')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- NEGARA --}}
                <div class="col-lg-6 col-md-12">
                    <label for="country_id" class="form-label">
                        Negara
                    </label>

                    <select
                        id="country_id"
                        name="country_id"
                        class="form-select @error('country_id') is-invalid @enderror"
                        required
                    >
                        <option value="">
                            Pilih negara
                        </option>

                        @foreach ($countries as $country)
                            <option
                                value="{{ $country->id }}"
                                {{
                                    (string) old(
                                        'country_id',
                                        $port->country_id
                                    ) === (string) $country->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $country->name }}

                                @if (!empty($country->country_code))
                                    ({{ $country->country_code }})
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('country_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="metric-sub mt-1">
                        Nama negara pada data pelabuhan akan mengikuti negara
                        yang dipilih.
                    </div>
                </div>

                {{-- STATUS --}}
                <div class="col-lg-6 col-md-12">
                    <label for="status" class="form-label">
                        Status Pelabuhan
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >
                        <option
                            value="Aman"
                            {{
                                old('status', $port->status) === 'Aman'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Aman
                        </option>

                        <option
                            value="Normal"
                            {{
                                old('status', $port->status) === 'Normal'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Normal
                        </option>

                        <option
                            value="Waspada"
                            {{
                                old('status', $port->status) === 'Waspada'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Waspada
                        </option>

                        <option
                            value="Siaga"
                            {{
                                old('status', $port->status) === 'Siaga'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Siaga
                        </option>

                        <option
                            value="Darurat"
                            {{
                                old('status', $port->status) === 'Darurat'
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            Darurat
                        </option>
                    </select>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- LATITUDE --}}
                <div class="col-lg-4 col-md-6">
                    <label for="latitude" class="form-label">
                        Latitude
                    </label>

                    <input
                        type="number"
                        id="latitude"
                        name="latitude"
                        class="form-control @error('latitude') is-invalid @enderror"
                        value="{{ old('latitude', $port->latitude) }}"
                        placeholder="-6.1040"
                        min="-90"
                        max="90"
                        step="0.0000001"
                        required
                    >

                    @error('latitude')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="metric-sub mt-1">
                        Nilai latitude harus antara -90 sampai 90.
                    </div>
                </div>

                {{-- LONGITUDE --}}
                <div class="col-lg-4 col-md-6">
                    <label for="longitude" class="form-label">
                        Longitude
                    </label>

                    <input
                        type="number"
                        id="longitude"
                        name="longitude"
                        class="form-control @error('longitude') is-invalid @enderror"
                        value="{{ old('longitude', $port->longitude) }}"
                        placeholder="106.8800"
                        min="-180"
                        max="180"
                        step="0.0000001"
                        required
                    >

                    @error('longitude')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="metric-sub mt-1">
                        Nilai longitude harus antara -180 sampai 180.
                    </div>
                </div>

                {{-- SKOR RISIKO --}}
                <div class="col-lg-4 col-md-12">
                    <label for="port_risk_score" class="form-label">
                        Skor Risiko
                    </label>

                    <div class="input-group">
                        <input
                            type="number"
                            id="port_risk_score"
                            name="port_risk_score"
                            class="form-control @error('port_risk_score') is-invalid @enderror"
                            value="{{ old(
                                'port_risk_score',
                                $port->port_risk_score ?? 0
                            ) }}"
                            min="0"
                            max="100"
                            step="0.01"
                            required
                        >

                        <span class="input-group-text">
                            /100
                        </span>

                        @error('port_risk_score')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="metric-sub mt-1">
                        Skor risiko harus berada antara 0 sampai 100.
                    </div>
                </div>

                {{-- INFORMASI STATUS --}}
                <div class="col-12">
                    <div class="recommendation-box">
                        <strong>Keterangan status:</strong>

                        <div class="mt-2">
                            <span class="risk-badge risk-low me-1">
                                Aman/Normal
                            </span>

                            Pelabuhan beroperasi dengan risiko rendah.
                        </div>

                        <div class="mt-2">
                            <span class="risk-badge risk-medium me-1">
                                Waspada
                            </span>

                            Pelabuhan memerlukan pemantauan lebih lanjut.
                        </div>

                        <div class="mt-2">
                            <span class="risk-badge risk-high me-1">
                                Siaga/Darurat
                            </span>

                            Pelabuhan memiliki tingkat risiko yang tinggi.
                        </div>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-save me-1"></i>
                            Simpan Perubahan
                        </button>

                        <a
                            href="{{ route('admin.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Batal
                        </a>

                        <a
                            href="{{ route('ports.index', [
                                'country_id' => $port->country_id
                            ]) }}"
                            class="btn btn-outline-primary"
                            target="_blank"
                        >
                            <i class="bi bi-map me-1"></i>
                            Lihat di Halaman Pelabuhan
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- INFORMASI DATA SAAT INI --}}
    <div class="card-clean mt-4">
        <div class="section-title">
            Informasi Data Saat Ini
        </div>

        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="route-summary-card h-100">
                    <small>Nama Pelabuhan</small>
                    <strong>{{ $port->name ?? '-' }}</strong>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="route-summary-card h-100">
                    <small>Kota</small>
                    <strong>{{ $port->city ?? '-' }}</strong>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="route-summary-card h-100">
                    <small>Status</small>

                    @php
                        $currentStatus = $port->status ?? 'Aman';

                        $currentStatusClass = in_array(
                            $currentStatus,
                            ['Aman', 'Normal'],
                            true
                        )
                            ? 'risk-low'
                            : (
                                $currentStatus === 'Waspada'
                                    ? 'risk-medium'
                                    : 'risk-high'
                            );
                    @endphp

                    <span class="risk-badge {{ $currentStatusClass }}">
                        {{ $currentStatus }}
                    </span>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="route-summary-card h-100">
                    <small>Skor Risiko</small>

                    <strong>
                        {{ number_format(
                            (float) ($port->port_risk_score ?? 0),
                            0
                        ) }}/100
                    </strong>
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