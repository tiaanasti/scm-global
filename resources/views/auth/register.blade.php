@extends('layouts.guest')

@section('title', 'Daftar - Supply Chain Risk Intelligence')

@section('content')
    <div class="login-wrapper">
        <div class="login-card card-clean">
            <div class="login-brand">
                <div class="brand-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h1>Buat Akun Baru</h1>
                    <p>Daftar untuk memantau risiko rantai pasok dan mengelola watchlist pribadi.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="login-form">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        required
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
                        autocomplete="new-password"
                    >
                    <div class="form-text">Minimal 8 karakter.</div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-check"></i>
                    Daftar
                </button>
            </form>

            <div class="auth-switch mt-4">
                <small class="text-muted">
                    Sudah punya akun?
                    <a href="{{ route('login') }}">Masuk di sini</a>
                </small>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @include('auth.partials.guest-styles')
@endpush
