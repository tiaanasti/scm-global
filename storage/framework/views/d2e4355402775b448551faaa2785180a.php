

<?php $__env->startSection('title', 'Mesin Risiko - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="topbar">
        <div class="page-title">
            <h1>Mesin Skor Risiko</h1>
            <p>Analisis risiko rantai pasok berdasarkan cuaca, inflasi, kurs, dan sentimen berita.</p>
        </div>

        <form action="<?php echo e(route('risks.index')); ?>" method="GET">
            <select name="country_id" class="form-select country-select" onchange="this.form.submit()">
                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($item->id); ?>" <?php echo e($selectedCountryId == $item->id ? 'selected' : ''); ?>>
                        <?php echo e($item->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
    </div>

    <div class="content">
        <!-- TOP CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-red">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                    <div>
                        <div class="metric-label">Total Risiko</div>
                        <div class="metric-value"><?php echo e($risk->total_score ?? 0); ?>/100</div>
                        <div class="metric-sub">
                            <span class="risk-badge <?php echo e(($risk->total_score ?? 0) >= 60 ? 'risk-high' : (($risk->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low')); ?>">
                                <?php echo e($risk->risk_level ?? '-'); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-blue">
                        <i class="bi bi-cloud-rain"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Cuaca</div>
                        <div class="metric-value"><?php echo e($risk->weather_score ?? 0); ?>%</div>
                        <div class="metric-sub"><?php echo e($weather->weather_status ?? '-'); ?></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-orange">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Inflasi</div>
                        <div class="metric-value"><?php echo e($risk->inflation_score ?? 0); ?>%</div>
                        <div class="metric-sub">Inflasi <?php echo e($economic->inflation_rate ?? 0); ?>%</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-purple">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Kurs</div>
                        <div class="metric-value"><?php echo e($risk->currency_score ?? 0); ?>%</div>
                        <div class="metric-sub">USD/<?php echo e($currency->target_currency ?? '-'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAIL CUACA API -->
        <div class="card-clean mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div class="section-title mb-0">Data Cuaca dari Open-Meteo API</div>
                <span class="risk-badge risk-low">Tersinkron API</span>
            </div>

            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="indicator-box indicator-blue">
                        <div class="metric-label">Suhu</div>
                        <div class="metric-value"><?php echo e(number_format($weather->temperature ?? 0, 2)); ?>°C</div>
                        <div class="metric-sub"><?php echo e($weather->weather_status ?? '-'); ?></div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="indicator-box indicator-green">
                        <div class="metric-label">Curah Hujan</div>
                        <div class="metric-value"><?php echo e(number_format($weather->rainfall ?? 0, 2)); ?> mm</div>
                        <div class="metric-sub">Open-Meteo precipitation</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="indicator-box indicator-orange">
                        <div class="metric-label">Angin</div>
                        <div class="metric-value"><?php echo e(number_format($weather->wind_speed ?? 0, 2)); ?></div>
                        <div class="metric-sub">km/jam</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="indicator-box indicator-purple">
                        <div class="metric-label">Sinkron Terakhir</div>
                        <div class="info-value" style="font-size: 15px;">
                            <?php echo e($weather->reported_at ? \Carbon\Carbon::parse($weather->reported_at)->timezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB' : '-'); ?>

                        </div>
                        <div class="metric-sub">Sumber: Open-Meteo API</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART + FORMULA -->
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card-clean">
                    <div class="section-title">Komposisi Risiko <?php echo e($country->name ?? '-'); ?></div>

                    <canvas id="riskCompositionChart" height="230"></canvas>

                    <div class="risk-score-box mt-3">
                        <div class="risk-number"><?php echo e($risk->total_score ?? 0); ?></div>
                        <div class="metric-sub">Skor Risiko Total</div>
                        <div class="risk-level"><?php echo e($risk->risk_level ?? '-'); ?></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-clean">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0">Tren Skor Risiko</div>
                        <span class="risk-badge risk-low"><?php echo e($riskTrend->count()); ?> data</span>
                    </div>

                    <?php if($riskTrend->isNotEmpty()): ?>
                        <canvas id="riskScoreTrendChart" height="125"></canvas>
                    <?php else: ?>
                        <p class="text-muted mb-0">Data historis belum tersedia</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card-clean">
                    <div class="section-title">Model Perhitungan Risiko</div>

                    <div class="recommendation-box mb-4">
                        <strong>Formula:</strong>
                        <div class="mt-2">
                            Total Risiko = Cuaca + Inflasi + Kurs + Berita
                        </div>
                    </div>

                    <div class="risk-row">
                        <div class="risk-row-top">
                            <span><i class="bi bi-circle-fill text-primary"></i> Cuaca</span>
                            <strong><?php echo e($risk->weather_score ?? 0); ?>%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: <?php echo e($risk->weather_score ?? 0); ?>%"></div>
                        </div>
                    </div>

                    <div class="risk-row">
                        <div class="risk-row-top">
                            <span><i class="bi bi-circle-fill text-success"></i> Inflasi</span>
                            <strong><?php echo e($risk->inflation_score ?? 0); ?>%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?php echo e($risk->inflation_score ?? 0); ?>%"></div>
                        </div>
                    </div>

                    <div class="risk-row">
                        <div class="risk-row-top">
                            <span><i class="bi bi-circle-fill text-warning"></i> Kurs</span>
                            <strong><?php echo e($risk->currency_score ?? 0); ?>%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: <?php echo e($risk->currency_score ?? 0); ?>%"></div>
                        </div>
                    </div>

                    <div class="risk-row">
                        <div class="risk-row-top">
                            <span><i class="bi bi-circle-fill" style="color:#8b5cf6;"></i> Berita</span>
                            <strong><?php echo e($risk->news_score ?? 0); ?>%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo e($risk->news_score ?? 0); ?>%; background:#8b5cf6;"></div>
                        </div>
                    </div>

                    <div class="recommendation-box mt-4">
                        <strong>Interpretasi Sistem:</strong>
                        <div class="mt-2">
                            <?php echo e($risk->recommendation ?? 'Belum ada rekomendasi untuk negara ini.'); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEWS + TABLE -->
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card-clean">
                    <div class="section-title">Berita yang Mempengaruhi Risiko</div>

                    <?php $__empty_1 = true; $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="news-item">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="risk-badge <?php echo e($item->sentiment === 'Negative' ? 'risk-high' : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium')); ?>">
                                    <?php echo e($item->sentiment); ?>

                                </span>
                                <small class="text-muted"><?php echo e($item->category ?? 'Umum'); ?></small>
                            </div>
                            <div class="news-title"><?php echo e($item->title); ?></div>
                            <div class="news-desc"><?php echo e($item->description); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted">Belum ada berita untuk negara ini.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-clean">
                    <div class="section-title">Peringkat Risiko Negara</div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                            <tr>
                                <th>Negara</th>
                                <th>Cuaca</th>
                                <th>Inflasi</th>
                                <th>Kurs</th>
                                <th>Berita</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $riskRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($row->country_name); ?></strong></td>
                                    <td><?php echo e($row->weather_score); ?>%</td>
                                    <td><?php echo e($row->inflation_score); ?>%</td>
                                    <td><?php echo e($row->currency_score); ?>%</td>
                                    <td><?php echo e($row->news_score); ?>%</td>
                                    <td><strong><?php echo e($row->total_score); ?>/100</strong></td>
                                    <td>
                                        <span class="risk-badge <?php echo e($row->total_score >= 60 ? 'risk-high' : ($row->total_score >= 35 ? 'risk-medium' : 'risk-low')); ?>">
                                            <?php echo e($row->risk_level); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            © <?php echo e(date('Y')); ?> Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const weatherScore = Number(<?php echo json_encode($risk->weather_score ?? 0, 15, 512) ?>);
    const inflationScore = Number(<?php echo json_encode($risk->inflation_score ?? 0, 15, 512) ?>);
    const currencyScore = Number(<?php echo json_encode($risk->currency_score ?? 0, 15, 512) ?>);
    const newsScore = Number(<?php echo json_encode($risk->news_score ?? 0, 15, 512) ?>);

    <?php if($riskTrend->isNotEmpty()): ?>
    const riskTrendLabels = <?php echo json_encode($riskTrend->pluck('score_date'), 15, 512) ?>;
    const riskTrendValues = <?php echo json_encode($riskTrend->pluck('total_score'), 15, 512) ?>;

    new Chart(document.getElementById('riskScoreTrendChart'), {
        type: 'line',
        data: {
            labels: riskTrendLabels,
            datasets: [{
                label: 'Skor Risiko',
                data: riskTrendValues,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.10)',
                pointBackgroundColor: '#ef4444',
                pointRadius: 4,
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
    <?php endif; ?>

    new Chart(document.getElementById('riskCompositionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Cuaca', 'Inflasi', 'Kurs', 'Berita'],
            datasets: [{
                data: [weatherScore, inflationScore, currencyScore, newsScore],
                backgroundColor: ['#2563eb', '#22c55e', '#f59e0b', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views/risks/index.blade.php ENDPATH**/ ?>