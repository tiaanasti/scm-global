<?php $__env->startSection('title', 'Cuaca Global - Supply Chain Management'); ?>

<?php $__env->startPush('styles'); ?>
    <link
        rel="stylesheet"
        href="<?php echo e(asset('css/weather-map.css')); ?>?v=<?php echo e(file_exists(public_path('css/weather-map.css')) ? filemtime(public_path('css/weather-map.css')) : time()); ?>"
    >
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="weather-page">
    <div class="topbar d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div class="page-title">
            <h1>Peta Monitoring Cuaca Global</h1>
            <p>Monitoring kondisi cuaca terbaru setiap negara untuk analisis potensi gangguan rantai pasok.</p>
        </div>
    </div>

    <div class="content">
        <?php if($weatherList->isEmpty()): ?>
            <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5"></i>
                <div>
                    Belum ada data cuaca yang dapat ditampilkan. Jalankan sinkronisasi Open-Meteo dari Panel Admin.
                </div>
            </div>
        <?php endif; ?>

        
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card h-100">
                    <div class="metric-icon icon-blue">
                        <i class="bi bi-globe"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="metric-label">Total Terpantau</div>
                        <div class="metric-value" id="metric-total"><?php echo e($weatherList->count()); ?></div>
                        <div class="metric-sub">Negara dengan koordinat</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card h-100">
                    <div class="metric-icon icon-green">
                        <i class="bi bi-sun"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="metric-label">Kondisi Normal</div>
                        <div class="metric-value" id="metric-normal">0</div>
                        <div class="metric-sub">Cuaca cerah/berawan</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card h-100">
                    <div class="metric-icon icon-blue">
                        <i class="bi bi-cloud-rain"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="metric-label">Hujan</div>
                        <div class="metric-value" id="metric-hujan">0</div>
                        <div class="metric-sub">Curah hujan terdeteksi</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card h-100">
                    <div class="metric-icon icon-red">
                        <i class="bi bi-wind"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="metric-label">Angin Kencang/Badai</div>
                        <div class="metric-value" id="metric-badai-angin">0</div>
                        <div class="metric-sub">Potensi risiko tinggi</div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card-clean mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="section-title mb-0">Peta Cuaca Dunia</div>
                
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="weather-search-input" class="form-control" placeholder="Cari negara...">
                    </div>

                    <select id="weather-category-select" class="form-select form-select-sm" style="max-width: 180px;">
                        <option value="all">Semua Kondisi</option>
                        <option value="Normal">Normal</option>
                        <option value="Hujan">Hujan</option>
                        <option value="Angin Kencang">Angin Kencang</option>
                        <option value="Badai">Badai</option>
                    </select>

                    <button type="button" id="weather-reset-btn" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </div>

            <div id="weather-map"></div>

            
            <div class="weather-legend mt-3 d-flex align-items-center justify-content-center gap-4 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <span class="weather-legend-dot dot-normal"></span>
                    <span class="small font-weight-bold">Normal</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="weather-legend-dot dot-hujan"></span>
                    <span class="small font-weight-bold">Hujan</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="weather-legend-dot dot-angin"></span>
                    <span class="small font-weight-bold">Angin Kencang</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="weather-legend-dot dot-badai"></span>
                    <span class="small font-weight-bold">Badai</span>
                </div>
            </div>
        </div>

        
        <div class="card-clean">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div class="section-title mb-0">Daftar Laporan Cuaca Terbaru</div>
                <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Kelembapan belum termasuk dalam skema database saat ini.</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Kondisi</th>
                            <th>Suhu</th>
                            <th>Kecepatan Angin</th>
                            <th>Curah Hujan</th>
                            <th>Pembaruan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="weather-table-body">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        window.weatherDataset = <?php echo json_encode($weatherList, 15, 512) ?>;
    </script>
    <script src="<?php echo e(asset('js/weather-map.js')); ?>?v=<?php echo e(file_exists(public_path('js/weather-map.js')) ? filemtime(public_path('js/weather-map.js')) : time()); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views/weather/index.blade.php ENDPATH**/ ?>