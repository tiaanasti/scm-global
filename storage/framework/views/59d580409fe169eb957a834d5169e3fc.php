

<?php $__env->startSection('title', 'Perbandingan Negara - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="topbar">
        <div class="page-title">
            <h1>Perbandingan Negara</h1>
            <p>Bandingkan risiko rantai pasok antar negara untuk mendukung keputusan impor.</p>
        </div>

        <form action="<?php echo e(route('comparisons.index')); ?>" method="GET" class="d-flex gap-2">
            <select name="first_country_id" class="form-select country-select" onchange="this.form.submit()">
                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($country->id); ?>" <?php echo e($firstCountryId == $country->id ? 'selected' : ''); ?>>
                        <?php echo e($country->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <select name="second_country_id" class="form-select country-select" onchange="this.form.submit()">
                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($country->id); ?>" <?php echo e($secondCountryId == $country->id ? 'selected' : ''); ?>>
                        <?php echo e($country->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
    </div>

    <div class="content">
        <!-- COMPARISON HEADER -->
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card-clean text-center">
                    <div class="metric-label">Negara Pertama</div>
                    <div class="metric-value"><?php echo e($firstCountry->name ?? '-'); ?></div>
                    <div class="metric-sub"><?php echo e($firstCountry->region ?? '-'); ?></div>

                    <div class="mt-3">
                        <span class="risk-badge
                            <?php echo e(($firstRisk->total_score ?? 0) >= 60 ? 'risk-high' : (($firstRisk->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low')); ?>">
                            <?php echo e($firstRisk->risk_level ?? '-'); ?>

                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="card-clean text-center d-flex align-items-center justify-content-center">
                    <div>
                        <div style="font-size: 28px; font-weight: 800;">VS</div>
                        <div class="metric-sub">Comparison</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-clean text-center">
                    <div class="metric-label">Negara Kedua</div>
                    <div class="metric-value"><?php echo e($secondCountry->name ?? '-'); ?></div>
                    <div class="metric-sub"><?php echo e($secondCountry->region ?? '-'); ?></div>

                    <div class="mt-3">
                        <span class="risk-badge
                            <?php echo e(($secondRisk->total_score ?? 0) >= 60 ? 'risk-high' : (($secondRisk->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low')); ?>">
                            <?php echo e($secondRisk->risk_level ?? '-'); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN METRICS -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card-clean">
                    <div class="metric-label">Skor Risiko</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-sub"><?php echo e($firstCountry->name ?? '-'); ?></div>
                            <div class="metric-value"><?php echo e($firstRisk->total_score ?? 0); ?>/100</div>
                        </div>

                        <div class="text-end">
                            <div class="metric-sub"><?php echo e($secondCountry->name ?? '-'); ?></div>
                            <div class="metric-value"><?php echo e($secondRisk->total_score ?? 0); ?>/100</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean">
                    <div class="metric-label">Inflasi</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-sub"><?php echo e($firstCountry->name ?? '-'); ?></div>
                            <div class="metric-value"><?php echo e($firstEconomic->inflation_rate ?? 0); ?>%</div>
                        </div>

                        <div class="text-end">
                            <div class="metric-sub"><?php echo e($secondCountry->name ?? '-'); ?></div>
                            <div class="metric-value"><?php echo e($secondEconomic->inflation_rate ?? 0); ?>%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean">
                    <div class="metric-label">Cuaca</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-sub"><?php echo e($firstCountry->name ?? '-'); ?></div>
                            <div class="metric-value"><?php echo e($firstWeather->temperature ?? 0); ?>°C</div>
                        </div>

                        <div class="text-end">
                            <div class="metric-sub"><?php echo e($secondCountry->name ?? '-'); ?></div>
                            <div class="metric-value"><?php echo e($secondWeather->temperature ?? 0); ?>°C</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean">
                    <div class="metric-label">Kurs</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-sub">USD/<?php echo e($firstCurrency->target_currency ?? '-'); ?></div>
                            <div class="metric-value">
                                <?php echo e(number_format($firstCurrency->exchange_rate ?? 0, 2)); ?>

                            </div>
                        </div>

                        <div class="text-end">
                            <div class="metric-sub">USD/<?php echo e($secondCurrency->target_currency ?? '-'); ?></div>
                            <div class="metric-value">
                                <?php echo e(number_format($secondCurrency->exchange_rate ?? 0, 2)); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART + RECOMMENDATION -->
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card-clean">
                    <div class="section-title">Grafik Perbandingan Risiko</div>
                    <canvas id="comparisonChart" height="150"></canvas>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-clean">
                    <div class="section-title">Rekomendasi Impor</div>

                    <div class="recommendation-box">
                        <strong>Pilihan Lebih Aman:</strong>
                        <div class="metric-value mt-2"><?php echo e($saferCountry); ?></div>
                        <div class="mt-2">
                            <?php echo e($recommendation); ?>

                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="info-row">
                            <div class="info-label">Skor <?php echo e($firstCountry->name ?? '-'); ?></div>
                            <div class="info-value"><?php echo e($firstRisk->total_score ?? 0); ?>/100</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Skor <?php echo e($secondCountry->name ?? '-'); ?></div>
                            <div class="info-value"><?php echo e($secondRisk->total_score ?? 0); ?>/100</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Indikator Utama</div>
                            <div class="info-value">Risk Score</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAIL TABLE -->
        <div class="card-clean mb-4">
            <div class="section-title">Detail Perbandingan Indikator</div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Indikator</th>
                        <th><?php echo e($firstCountry->name ?? '-'); ?></th>
                        <th><?php echo e($secondCountry->name ?? '-'); ?></th>
                        <th>Lebih Baik</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td>Skor Risiko</td>
                        <td><?php echo e($firstRisk->total_score ?? 0); ?>/100</td>
                        <td><?php echo e($secondRisk->total_score ?? 0); ?>/100</td>
                        <td>
                            <?php echo e(($firstRisk->total_score ?? 0) <= ($secondRisk->total_score ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-')); ?>

                        </td>
                    </tr>

                    <tr>
                        <td>Inflasi</td>
                        <td><?php echo e($firstEconomic->inflation_rate ?? 0); ?>%</td>
                        <td><?php echo e($secondEconomic->inflation_rate ?? 0); ?>%</td>
                        <td>
                            <?php echo e(($firstEconomic->inflation_rate ?? 0) <= ($secondEconomic->inflation_rate ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-')); ?>

                        </td>
                    </tr>

                    <tr>
                        <td>Risiko Cuaca</td>
                        <td><?php echo e($firstRisk->weather_score ?? 0); ?>%</td>
                        <td><?php echo e($secondRisk->weather_score ?? 0); ?>%</td>
                        <td>
                            <?php echo e(($firstRisk->weather_score ?? 0) <= ($secondRisk->weather_score ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-')); ?>

                        </td>
                    </tr>

                    <tr>
                        <td>Risiko Kurs</td>
                        <td><?php echo e($firstRisk->currency_score ?? 0); ?>%</td>
                        <td><?php echo e($secondRisk->currency_score ?? 0); ?>%</td>
                        <td>
                            <?php echo e(($firstRisk->currency_score ?? 0) <= ($secondRisk->currency_score ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-')); ?>

                        </td>
                    </tr>

                    <tr>
                        <td>Sentimen Berita</td>
                        <td><?php echo e($firstRisk->news_score ?? 0); ?>%</td>
                        <td><?php echo e($secondRisk->news_score ?? 0); ?>%</td>
                        <td>
                            <?php echo e(($firstRisk->news_score ?? 0) <= ($secondRisk->news_score ?? 0) ? ($firstCountry->name ?? '-') : ($secondCountry->name ?? '-')); ?>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- NEWS -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-clean">
                    <div class="section-title">Berita <?php echo e($firstCountry->name ?? '-'); ?></div>

                    <?php $__empty_1 = true; $__currentLoopData = $firstNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="news-item">
                            <span class="risk-badge
                                <?php echo e($item->sentiment === 'Negative' ? 'risk-high' : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium')); ?>">
                                <?php echo e($item->sentiment); ?>

                            </span>

                            <div class="news-title mt-2"><?php echo e($item->title); ?></div>
                            <div class="news-desc"><?php echo e($item->description); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted">Belum ada berita.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-clean">
                    <div class="section-title">Berita <?php echo e($secondCountry->name ?? '-'); ?></div>

                    <?php $__empty_1 = true; $__currentLoopData = $secondNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="news-item">
                            <span class="risk-badge
                                <?php echo e($item->sentiment === 'Negative' ? 'risk-high' : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium')); ?>">
                                <?php echo e($item->sentiment); ?>

                            </span>

                            <div class="news-title mt-2"><?php echo e($item->title); ?></div>
                            <div class="news-desc"><?php echo e($item->description); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted">Belum ada berita.</p>
                    <?php endif; ?>
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
    const firstCountryName = <?php echo json_encode($firstCountry->name ?? '-', 15, 512) ?>;
    const secondCountryName = <?php echo json_encode($secondCountry->name ?? '-', 15, 512) ?>;

    const firstData = [
        Number(<?php echo json_encode($firstRisk->total_score ?? 0, 15, 512) ?>),
        Number(<?php echo json_encode($firstRisk->weather_score ?? 0, 15, 512) ?>),
        Number(<?php echo json_encode($firstRisk->inflation_score ?? 0, 15, 512) ?>),
        Number(<?php echo json_encode($firstRisk->currency_score ?? 0, 15, 512) ?>),
        Number(<?php echo json_encode($firstRisk->news_score ?? 0, 15, 512) ?>)
    ];

    const secondData = [
        Number(<?php echo json_encode($secondRisk->total_score ?? 0, 15, 512) ?>),
        Number(<?php echo json_encode($secondRisk->weather_score ?? 0, 15, 512) ?>),
        Number(<?php echo json_encode($secondRisk->inflation_score ?? 0, 15, 512) ?>),
        Number(<?php echo json_encode($secondRisk->currency_score ?? 0, 15, 512) ?>),
        Number(<?php echo json_encode($secondRisk->news_score ?? 0, 15, 512) ?>)
    ];

    new Chart(document.getElementById('comparisonChart'), {
        type: 'bar',
        data: {
            labels: ['Total', 'Cuaca', 'Inflasi', 'Kurs', 'Berita'],
            datasets: [
                {
                    label: firstCountryName,
                    data: firstData,
                    backgroundColor: '#2563eb'
                },
                {
                    label: secondCountryName,
                    data: secondData,
                    backgroundColor: '#f59e0b'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views\comparisons\index.blade.php ENDPATH**/ ?>