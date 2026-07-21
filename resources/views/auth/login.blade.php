@extends('layouts.guest')

@section('title', 'Masuk - Supply Chain Risk Intelligence')

@section('content')
    <div class="login-wrapper">
        <div class="login-card card-clean">
            <div class="login-brand">
                <div class="brand-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h1>Supply Chain Risk Intelligence</h1>
                    <p>Masuk untuk mengakses dashboard risiko rantai pasok global.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="login-form">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <div class="form-check mb-4">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remember"
                        id="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk
                </button>
            </form>

            <div class="auth-switch mt-4">
                <small class="text-muted">
                    Belum punya akun?
                    <a href="{{ route('register') }}">Daftar sekarang</a>
                </small>
            </div>

            <div class="login-hint mt-3">
                <small class="text-muted">
                    Demo admin: <strong>admin@supplyrisk.test</strong> / <strong>password</strong>
                </small>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @include('auth.partials.guest-styles')
@endpush
