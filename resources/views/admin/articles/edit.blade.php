@extends('layouts.app')

@section('title', 'Edit Artikel - Supply Chain Risk Intelligence')

@section('content')
    <div class="topbar">
        <div class="page-title">
            <h1>Edit Artikel Analisis</h1>
            <p>Perbarui artikel analisis yang ditampilkan pada panel admin.</p>
        </div>

        <a href="{{ route('admin.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Admin
        </a>
    </div>

    <div class="content">
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.articles.update', $article->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-clean mb-4">
                <div class="section-title">Data Artikel</div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label">Judul Artikel</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $article->title) }}" required>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category', $article->category) }}">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Draft" {{ old('status', $article->status) === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Published" {{ old('status', $article->status) === 'Published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Isi Artikel</label>
                        <textarea name="content" rows="10" class="form-control" required>{{ old('content', $article->content) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
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
            © {{ date('Y') }} Supply Chain Risk Intelligence. Semua hak dilindungi.
        </div>
    </div>
@endsection