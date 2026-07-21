@extends('layouts.app')

@section('title', 'Edit User - Supply Chain Management')

@section('content')
    <div class="topbar">
        <div class="page-title">
            <h1>Edit User</h1>
            <p>Perbarui nama, email, role, dan reset password pengguna.</p>
        </div>

        <a href="{{ route('admin.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Admin
        </a>
    </div>

    <div class="content">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-clean mb-4">
                <div class="section-title">Data User</div>

                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Nama</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $user->name) }}"
                            required
                        >
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $user->email) }}"
                            required
                        >
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="user" {{ old('role', $user->role ?? 'user') === 'user' ? 'selected' : '' }}>
                                User
                            </option>
                            <option value="admin" {{ old('role', $user->role ?? 'user') === 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-clean mb-4">
                <div class="section-title">Reset Password</div>
                <div class="metric-sub mb-3">
                    Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi.
                </div>

                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Simpan Perubahan
                </button>

                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>
            </div>
        </form>

        <div class="footer">
            © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
@endsection
