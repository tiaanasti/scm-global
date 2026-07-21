

<?php $__env->startSection('title', 'Daftar Pantauan - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="topbar">
        <div class="page-title">
            <h1>Daftar Pantauan</h1>
            <p>Negara yang dipantau untuk monitoring risiko rantai pasok secara berkelanjutan.</p>
        </div>
    </div>

    <div class="content">
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <div class="card-clean mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="section-title mb-1">Tambah Negara ke Watchlist</div>
                    <div class="metric-sub">
                        Pilih negara yang ingin dipantau secara berkala.
                    </div>
                </div>
            </div>

            <form action="<?php echo e(route('watchlists.store')); ?>" method="POST" class="row g-3">
                <?php echo csrf_field(); ?>

                <div class="col-lg-9">
                    <select name="country_id" class="form-select country-select" required>
                        <option value="">Pilih negara</option>

                        <?php $__currentLoopData = $availableCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($country->id); ?>">
                                <?php echo e($country->name); ?> - <?php echo e($country->region); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i>
                        Tambah Watchlist
                    </button>
                </div>
            </form>

            <?php if($availableCountries->count() === 0): ?>
                <div class="metric-sub mt-3">
                    Semua negara sudah masuk ke dalam watchlist.
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-blue">
                        <i class="bi bi-star"></i>
                    </div>
                    <div>
                        <div class="metric-label">Total Watchlist</div>
                        <div class="metric-value"><?php echo e($summary['total_watchlist']); ?></div>
                        <div class="metric-sub">Negara dipantau</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-red">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Tinggi</div>
                        <div class="metric-value"><?php echo e($summary['high_risk']); ?></div>
                        <div class="metric-sub">Butuh perhatian utama</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-orange">
                        <i class="bi bi-dash-circle"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Sedang</div>
                        <div class="metric-value"><?php echo e($summary['medium_risk']); ?></div>
                        <div class="metric-sub">Perlu dipantau</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Rendah</div>
                        <div class="metric-value"><?php echo e($summary['low_risk']); ?></div>
                        <div class="metric-sub">Relatif aman</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-clean mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title mb-0">Negara dalam Daftar Pantauan</div>
                <span class="risk-badge risk-medium">Monitoring aktif</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Wilayah</th>
                        <th>Kurs</th>
                        <th>Cuaca</th>
                        <th>Skor Risiko</th>
                        <th>Status</th>
                        <th>Mulai Dipantau</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $watchlistRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr id="watchlist-country-<?php echo e($item->country_id); ?>">
                            <td>
                                <strong><?php echo e($item->country_name); ?></strong>
                                <div class="metric-sub"><?php echo e($item->currency_code); ?></div>
                            </td>
                            <td><?php echo e($item->region ?? '-'); ?></td>
                            <td>
                                USD/<?php echo e($item->target_currency ?? '-'); ?>

                                <div class="metric-sub">
                                    <?php echo e(number_format($item->exchange_rate ?? 0, 2)); ?>

                                </div>
                            </td>
                            <td>
                                <?php echo e($item->temperature ?? 0); ?>°C
                                <div class="metric-sub"><?php echo e($item->weather_status ?? '-'); ?></div>
                            </td>
                            <td>
                                <strong><?php echo e($item->total_score ?? 0); ?>/100</strong>
                            </td>
                            <td>
                                <span class="risk-badge
                                    <?php echo e(($item->total_score ?? 0) >= 60 ? 'risk-high' : (($item->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low')); ?>">
                                    <?php echo e($item->risk_level ?? '-'); ?>

                                </span>
                            </td>
                            <td>
                                <?php echo e(\Carbon\Carbon::parse($item->created_at)->format('d M Y')); ?>

                            </td>
                            <td>
                                <form
                                    action="<?php echo e(route('watchlists.destroy', $item->watchlist_id)); ?>"
                                    method="POST"
                                    onsubmit="return confirm('Hapus negara ini dari watchlist?')"
                                >
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-muted">
                                Belum ada negara dalam daftar pantauan.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-4">
            <?php $__currentLoopData = $watchlistRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="section-title mb-1"><?php echo e($item->country_name); ?></div>
                                <div class="metric-sub"><?php echo e($item->region ?? '-'); ?></div>
                            </div>

                            <span class="risk-badge
                                <?php echo e(($item->total_score ?? 0) >= 60 ? 'risk-high' : (($item->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low')); ?>">
                                <?php echo e($item->risk_level ?? '-'); ?>

                            </span>
                        </div>

                        <div class="recommendation-box">
                            <strong>Catatan Pemantauan:</strong>
                            <div class="mt-2">
                                <?php echo e($item->recommendation ?? 'Belum ada rekomendasi untuk negara ini.'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="footer">
            © <?php echo e(date('Y')); ?> Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views\watchlists\index.blade.php ENDPATH**/ ?>