

<?php $__env->startSection('title', 'Panel Admin - Supply Chain Management'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    #users-section,
    #countries-section,
    #ports-section,
    #articles-section {
        scroll-margin-top: 24px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
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

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    <?php endif; ?>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    <?php endif; ?>

    
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <strong>Data belum dapat disimpan.</strong>

            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
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
                action="<?php echo e(route('admin.api.sync')); ?>"
                method="POST"
                onsubmit="return confirm('Sinkronkan data API untuk negara di Watchlist sekarang?')"
            >
                <?php echo csrf_field(); ?>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-arrow-down me-1"></i>
                    Sync API Negara Watchlist
                </button>
            </form>
        </div>
    </div>

    
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
                action="<?php echo e(route('admin.world_bank.sync')); ?>"
                method="POST"
                onsubmit="return confirm('Sinkronkan data ekonomi semua negara dari World Bank sekarang?')"
            >
                <?php echo csrf_field(); ?>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-bank me-1"></i>
                    Sync World Bank
                </button>
            </form>
        </div>
    </div>

    
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
                action="<?php echo e(route('admin.risk.recalculate')); ?>"
                method="POST"
                onsubmit="return confirm('Hitung ulang skor risiko seluruh negara sekarang?')"
            >
                <?php echo csrf_field(); ?>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Hitung Ulang Skor Risiko
                </button>
            </form>
        </div>
    </div>

    
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
                action="<?php echo e(route('admin.countries.sync_api')); ?>"
                method="POST"
                onsubmit="return confirm('Sinkronkan data negara dari REST Countries API sekarang?')"
            >
                <?php echo csrf_field(); ?>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-globe2 me-1"></i>
                    Sync Negara dari API
                </button>
            </form>
        </div>
    </div>

    
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
                action="<?php echo e(route('admin.ports.sync_world_port_index')); ?>"
                method="POST"
                onsubmit="return confirm('Sinkronkan data World Port Index sekarang?')"
            >
                <?php echo csrf_field(); ?>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-anchor me-1"></i>
                    Sync World Port Index
                </button>
            </form>
        </div>
    </div>

    
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
                action="<?php echo e(route('admin.ports.recalculate_risk')); ?>"
                method="POST"
                onsubmit="return confirm('Hitung ulang risiko seluruh pelabuhan sekarang?')"
            >
                <?php echo csrf_field(); ?>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Hitung Risiko Pelabuhan
                </button>
            </form>
        </div>
    </div>

    
    <div class="card-clean mb-4">
        <div class="section-title">
            Tambah Negara Baru
        </div>

        <form action="<?php echo e(route('admin.countries.store')); ?>" method="POST" class="data-ajax-add-form" data-refresh-section="countries-section">
            <?php echo csrf_field(); ?>

            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Kode Negara
                    </label>

                    <input
                        type="text"
                        name="country_code"
                        class="form-control"
                        value="<?php echo e(old('country_code')); ?>"
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
                        value="<?php echo e(old('name')); ?>"
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
                        value="<?php echo e(old('capital')); ?>"
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
                        value="<?php echo e(old('region')); ?>"
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
                        value="<?php echo e(old('currency_code')); ?>"
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
                        value="<?php echo e(old('currency_name')); ?>"
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
                        value="<?php echo e(old('language')); ?>"
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
                        value="<?php echo e(old('exchange_rate')); ?>"
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
                        value="<?php echo e(old('latitude')); ?>"
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
                        value="<?php echo e(old('longitude')); ?>"
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

    
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-blue">
                    <i class="bi bi-people"></i>
                </div>

                <div>
                    <div class="metric-label">User</div>

                    <div class="metric-value">
                        <?php echo e($summary['users_count'] ?? 0); ?>

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
                        <?php echo e($summary['countries_count'] ?? 0); ?>

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
                        <?php echo e($summary['ports_count'] ?? 0); ?>

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
                        <?php echo e($summary['news_count'] ?? 0); ?>

                    </div>

                    <div class="metric-sub">
                        News cache
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card-clean metric-card h-100">
                <div class="metric-icon icon-purple">
                    <i class="bi bi-file-text"></i>
                </div>

                <div>
                    <div class="metric-label">Artikel</div>

                    <div class="metric-value">
                        <?php echo e($summary['articles_count'] ?? 0); ?>

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
                        <?php echo e($summary['watchlists_count'] ?? 0); ?>

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
                        <?php echo e($summary['positive_words_count'] ?? 0); ?>

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
                        <?php echo e($summary['negative_words_count'] ?? 0); ?>

                    </div>

                    <div class="metric-sub">
                        Kamus negatif
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div id="users-section" class="card-clean mb-4">
        <div class="section-title">
            Kelola User
        </div>

        <form
            action="<?php echo e(route('admin.users.store')); ?>"
            method="POST"
            class="mb-4 data-ajax-add-form"
            data-refresh-section="users-section"
        >
            <?php echo csrf_field(); ?>

            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">
                        Nama
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?php echo e(old('name')); ?>"
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
                        value="<?php echo e(old('email')); ?>"
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
                            <?php echo e(old('role', 'user') === 'user' ? 'selected' : ''); ?>

                        >
                            User
                        </option>

                        <option
                            value="admin"
                            <?php echo e(old('role') === 'admin' ? 'selected' : ''); ?>

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

        <form action="<?php echo e(route('admin.index')); ?>#users-section" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-4">
                    <input
                        type="text"
                        name="user_search"
                        class="form-control"
                        placeholder="Cari nama atau email user..."
                        value="<?php echo e(request('user_search')); ?>"
                    >
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="<?php echo e(route('admin.index')); ?>#users-section" class="btn btn-outline-secondary">
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
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <strong><?php echo e($user->name); ?></strong>

                            <?php if((int) $user->id === (int) auth()->id()): ?>
                                <span class="risk-badge risk-low ms-1">
                                    Anda
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php echo e($user->email); ?>

                        </td>

                        <td>
                            <span
                                class="risk-badge <?php echo e(($user->role ?? 'user') === 'admin' ? 'risk-high' : 'risk-low'); ?>"
                            >
                                <?php echo e(ucfirst($user->role ?? 'user')); ?>

                            </span>
                        </td>

                        <td>
                            <?php echo e($user->created_at
                                ? \Carbon\Carbon::parse($user->created_at)->format('d M Y H:i')
                                : '-'); ?>

                        </td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a
                                    href="<?php echo e(route('admin.users.edit', $user->id)); ?>"
                                    class="btn btn-sm btn-outline-primary ajax-edit-btn"
                                >
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Edit
                                </a>

                                <?php if((int) $user->id !== (int) auth()->id()): ?>
                                    <form
                                        action="<?php echo e(route('admin.users.destroy', $user->id)); ?>"
                                        method="POST"
                                        class="data-ajax-delete-form"
                                        data-refresh-section="users-section"
                                        data-confirm="Yakin ingin menghapus user ini?"
                                    >
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            <i class="bi bi-trash me-1"></i>
                                            Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <?php echo e($users
                ->onEachSide(1)
                ->withQueryString()
                ->fragment('users-section')
                ->links('pagination::bootstrap-5')); ?>

        </div>
    </div>

    
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
                <?php $__empty_1 = true; $__currentLoopData = $apiLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <strong><?php echo e($log->api_name); ?></strong>

                            <div class="metric-sub">
                                <?php echo e($log->endpoint ?? '-'); ?>

                            </div>
                        </td>

                        <td>
                            <span
                                class="risk-badge <?php echo e($log->status === 'Success' ? 'risk-low' : 'risk-high'); ?>"
                            >
                                <?php echo e($log->status); ?>

                            </span>
                        </td>

                        <td>
                            <div class="metric-sub">
                                <?php echo e($log->message ?? '-'); ?>

                            </div>
                        </td>

                        <td>
                            <?php echo e($log->requested_at
                                ? \Carbon\Carbon::parse($log->requested_at)->format('d M Y H:i') . ' WIB'
                                : '-'); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada log API.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div id="countries-section" class="card-clean mb-4">
        <div class="section-title">
            Dataset Negara dan Risiko
        </div>

        <form action="<?php echo e(route('admin.index')); ?>#countries-section" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-4">
                    <input
                        type="text"
                        name="country_search"
                        class="form-control"
                        placeholder="Cari negara, kode, region, atau mata uang..."
                        value="<?php echo e(request('country_search')); ?>"
                    >
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="<?php echo e(route('admin.index')); ?>#countries-section" class="btn btn-outline-secondary">
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
                <?php $__empty_1 = true; $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <strong><?php echo e($country->name); ?></strong>
                        </td>

                        <td>
                            <?php echo e($country->region ?? '-'); ?>

                        </td>

                        <td>
                            <?php echo e($country->currency_code ?? '-'); ?>

                        </td>

                        <td>
                            <?php echo e($country->total_score ?? 0); ?>/100
                        </td>

                        <td>
                            <?php
                                $countryRiskScore = (float) ($country->total_score ?? 0);

                                $countryRiskClass = $countryRiskScore >= 60
                                    ? 'risk-high'
                                    : ($countryRiskScore >= 35
                                        ? 'risk-medium'
                                        : 'risk-low');
                            ?>

                            <span class="risk-badge <?php echo e($countryRiskClass); ?>">
                                <?php echo e($country->risk_level ?? 'Belum dihitung'); ?>

                            </span>
                        </td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a
                                    href="<?php echo e(route('admin.countries.edit', $country->id)); ?>"
                                    class="btn btn-sm btn-outline-primary ajax-edit-btn"
                                >
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Edit
                                </a>

                                <form
                                    action="<?php echo e(route('admin.countries.destroy', $country->id)); ?>"
                                    method="POST"
                                    class="data-ajax-delete-form"
                                    data-refresh-section="countries-section"
                                    data-confirm="Yakin ingin menghapus negara ini? Semua data terkait negara ini juga akan dihapus."
                                >
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>

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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <?php echo e($countries
                ->onEachSide(1)
                ->withQueryString()
                ->fragment('countries-section')
                ->links('pagination::bootstrap-5')); ?>

        </div>
    </div>

    
    <div class="card-clean mb-4">
        <div class="section-title">
            Tambah Pelabuhan Baru
        </div>

        <div class="metric-sub mb-3">
            Pelabuhan yang ditambahkan akan langsung tersedia pada
            halaman Pelabuhan dan peta tracking.
        </div>

        <form action="<?php echo e(route('admin.ports.store')); ?>" method="POST" class="data-ajax-add-form" data-refresh-section="ports-section">
            <?php echo csrf_field(); ?>

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">
                        Nama Pelabuhan
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?php echo e(old('name')); ?>"
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
                        value="<?php echo e(old('city')); ?>"
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

                        <?php $__currentLoopData = $countryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($country->id); ?>"
                                <?php echo e((string) old('country_id') === (string) $country->id ? 'selected' : ''); ?>

                            >
                                <?php echo e($country->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        value="<?php echo e(old('latitude')); ?>"
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
                        value="<?php echo e(old('longitude')); ?>"
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
                            <?php echo e(old('status', 'Aman') === 'Aman' ? 'selected' : ''); ?>

                        >
                            Aman
                        </option>

                        <option
                            value="Waspada"
                            <?php echo e(old('status') === 'Waspada' ? 'selected' : ''); ?>

                        >
                            Waspada
                        </option>

                        <option
                            value="Siaga"
                            <?php echo e(old('status') === 'Siaga' ? 'selected' : ''); ?>

                        >
                            Siaga
                        </option>

                        <option
                            value="Darurat"
                            <?php echo e(old('status') === 'Darurat' ? 'selected' : ''); ?>

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
                        value="<?php echo e(old('port_risk_score', 0)); ?>"
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
                <?php echo e($ports->total()); ?> pelabuhan
            </span>
        </div>

        <form action="<?php echo e(route('admin.index')); ?>#ports-section" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-4 col-lg-3">
                    <input
                        type="text"
                        name="port_search"
                        class="form-control"
                        placeholder="Cari pelabuhan, kota, negara..."
                        value="<?php echo e(request('port_search')); ?>"
                    >
                </div>
                <div class="col-md-3 col-lg-3">
                    <select name="port_country_id" class="form-select">
                        <option value="">Semua Negara</option>
                        <?php $__currentLoopData = $countryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $countryOpt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($countryOpt->id); ?>"
                                <?php echo e((string) request('port_country_id') === (string) $countryOpt->id ? 'selected' : ''); ?>

                            >
                                <?php echo e($countryOpt->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 col-lg-3">
                    <select name="port_status" class="form-select">
                        <option value="">Semua Status</option>
                        <?php $__currentLoopData = ['Aman', 'Normal', 'Waspada', 'Siaga', 'Darurat']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOpt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($statusOpt); ?>"
                                <?php echo e(request('port_status') === $statusOpt ? 'selected' : ''); ?>

                            >
                                <?php echo e($statusOpt); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="<?php echo e(route('admin.index')); ?>#ports-section" class="btn btn-outline-secondary">
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
                <?php $__empty_1 = true; $__currentLoopData = $ports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $port): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
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
                    ?>

                    <tr>
                        <td>
                            <i class="bi bi-pin-map-fill text-primary me-1"></i>

                            <strong>
                                <?php echo e($port->name); ?>

                            </strong>

                            <div class="metric-sub">
                                <?php echo e($port->city ?? '-'); ?>

                            </div>
                        </td>

                        <td>
                            <?php echo e($port->country_name ?? '-'); ?>

                        </td>

                        <td>
                            <span class="risk-badge <?php echo e($portStatusClass); ?>">
                                <?php echo e($portStatus); ?>

                            </span>
                        </td>

                        <td>
                            <strong>
                                <?php echo e(number_format((float) ($port->port_risk_score ?? 0), 0)); ?>/100
                            </strong>
                        </td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a
                                    href="<?php echo e(route('admin.ports.edit', $port->id)); ?>"
                                    class="btn btn-sm btn-outline-primary ajax-edit-btn"
                                >
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Edit
                                </a>

                                <form
                                    action="<?php echo e(route('admin.ports.destroy', $port->id)); ?>"
                                    method="POST"
                                    class="data-ajax-delete-form"
                                    data-refresh-section="ports-section"
                                    data-confirm="Yakin ingin menghapus pelabuhan <?php echo e(addslashes($port->name)); ?>?"
                                >
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>

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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <?php echo e($ports
                ->onEachSide(1)
                ->withQueryString()
                ->fragment('ports-section')
                ->links('pagination::bootstrap-5')); ?>

        </div>
    </div>

    
    <div class="card-clean mb-4">
        <div class="section-title">
            Tambah Artikel Analisis
        </div>

        <form action="<?php echo e(route('admin.articles.store')); ?>" method="POST" class="data-ajax-add-form" data-refresh-section="articles-section">
            <?php echo csrf_field(); ?>

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">
                        Judul Artikel
                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="<?php echo e(old('title')); ?>"
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
                        value="<?php echo e(old('category')); ?>"
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
                            <?php echo e(old('status', 'Draft') === 'Draft' ? 'selected' : ''); ?>

                        >
                            Draft
                        </option>

                        <option
                            value="Published"
                            <?php echo e(old('status') === 'Published' ? 'selected' : ''); ?>

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
                    ><?php echo e(old('content')); ?></textarea>
                </div>
            </div>
        </form>
    </div>

    
    <div id="articles-section" class="card-clean mb-4">
        <div class="section-title">
            Artikel Analisis
        </div>

        <form action="<?php echo e(route('admin.index')); ?>#articles-section" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-4">
                    <input
                        type="text"
                        name="article_search"
                        class="form-control"
                        placeholder="Cari judul, kategori, atau status artikel..."
                        value="<?php echo e(request('article_search')); ?>"
                    >
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="<?php echo e(route('admin.index')); ?>#articles-section" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="news-item">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span
                        class="risk-badge <?php echo e($article->status === 'Published' ? 'risk-low' : 'risk-medium'); ?>"
                    >
                        <?php echo e($article->status); ?>

                    </span>

                    <small class="text-muted">
                        <?php echo e($article->category ?? '-'); ?>

                    </small>
                </div>

                <div class="news-title">
                    <?php echo e($article->title); ?>

                </div>

                <div class="news-desc mb-3">
                    Penulis: <?php echo e($article->author_name ?? 'Admin'); ?>

                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a
                        href="<?php echo e(route('admin.articles.edit', $article->id)); ?>"
                        class="btn btn-sm btn-outline-primary ajax-edit-btn"
                    >
                        <i class="bi bi-pencil-square me-1"></i>
                        Edit
                    </a>

                    <form
                        action="<?php echo e(route('admin.articles.destroy', $article->id)); ?>"
                        method="POST"
                        class="data-ajax-delete-form"
                        data-refresh-section="articles-section"
                        data-confirm="Yakin ingin menghapus artikel ini?"
                    >
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>

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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-muted">
                Data tidak ditemukan.
            </p>
        <?php endif; ?>

        <div class="mt-3">
            <?php echo e($articles
                ->onEachSide(1)
                ->withQueryString()
                ->fragment('articles-section')
                ->links('pagination::bootstrap-5')); ?>

        </div>
    </div>

    
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card-clean h-100">
                <div class="section-title">
                    Kamus Kata Positif
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $positiveWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <span class="risk-badge risk-low me-1 mb-2">
                        <?php echo e($word->word); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted mb-0">
                        Belum ada kata positif.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-clean h-100">
                <div class="section-title">
                    Kamus Kata Negatif
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $negativeWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <span class="risk-badge risk-high me-1 mb-2">
                        <?php echo e($word->word); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted mb-0">
                        Belum ada kata negatif.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="footer">
        © <?php echo e(date('Y')); ?> Supply Chain Management.
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/admin-ajax.js')); ?>?v=<?php echo e(file_exists(public_path('js/admin-ajax.js')) ? filemtime(public_path('js/admin-ajax.js')) : time()); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views/admin/index.blade.php ENDPATH**/ ?>