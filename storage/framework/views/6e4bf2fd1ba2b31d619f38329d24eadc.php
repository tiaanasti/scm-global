

<?php $__env->startSection('title', 'Laporan Sistem - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="topbar">
        <div class="page-title">
            <h1>Laporan Sistem</h1>
            <p>Ringkasan akhir risiko rantai pasok global berdasarkan data negara, pelabuhan, kurs, dan berita.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('reports.export.csv')); ?>" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                Export CSV
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <div class="content">
        <!-- SUMMARY CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-blue">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <div>
                        <div class="metric-label">Total Negara</div>
                        <div class="metric-value"><?php echo e($summary['countries_count']); ?></div>
                        <div class="metric-sub">Negara dalam sistem</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-orange">
                        <i class="bi bi-pin-map-fill"></i>
                    </div>
                    <div>
                        <div class="metric-label">Total Pelabuhan</div>
                        <div class="metric-value"><?php echo e($summary['ports_count']); ?></div>
                        <div class="metric-sub">Dataset pelabuhan</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-red">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <div>
                        <div class="metric-label">Total Berita</div>
                        <div class="metric-value"><?php echo e($summary['news_count']); ?></div>
                        <div class="metric-sub">News cache</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-purple">
                        <i class="bi bi-star"></i>
                    </div>
                    <div>
                        <div class="metric-label">Watchlist</div>
                        <div class="metric-value"><?php echo e($summary['watchlists_count']); ?></div>
                        <div class="metric-sub">Negara dipantau</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RISK SUMMARY -->
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card-clean">
                    <div class="section-title">Komposisi Risiko Negara</div>
                    <canvas id="riskSummaryChart" height="230"></canvas>

                    <div class="recommendation-box mt-4">
                        <strong>Kesimpulan Sistem:</strong>
                        <div class="mt-2">
                            Negara dengan risiko tertinggi adalah
                            <strong><?php echo e($highestRisk->country_name ?? '-'); ?></strong>,
                            sedangkan negara dengan risiko terendah adalah
                            <strong><?php echo e($lowestRisk->country_name ?? '-'); ?></strong>.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-clean">
                    <div class="section-title">Ringkasan Risiko Utama</div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="indicator-box indicator-red">
                                <div class="metric-label">Risiko Tinggi</div>
                                <div class="metric-value"><?php echo e($summary['high_risk_count']); ?></div>
                                <div class="metric-sub">Skor ≥ 60</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="indicator-box indicator-orange">
                                <div class="metric-label">Risiko Sedang</div>
                                <div class="metric-value"><?php echo e($summary['medium_risk_count']); ?></div>
                                <div class="metric-sub">Skor 35–59</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="indicator-box indicator-green">
                                <div class="metric-label">Risiko Rendah</div>
                                <div class="metric-value"><?php echo e($summary['low_risk_count']); ?></div>
                                <div class="metric-sub">Skor &lt; 35</div>
                            </div>
                        </div>
                    </div>

                    <div class="recommendation-box mt-4">
                        <strong>Rekomendasi:</strong>
                        <div class="mt-2">
                            Prioritaskan monitoring negara dengan risiko tinggi, terutama pada indikator cuaca,
                            fluktuasi kurs, dan sentimen berita negatif karena ketiganya dapat memengaruhi biaya,
                            keterlambatan pengiriman, dan stabilitas rantai pasok.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RISK TABLE -->
        <div class="card-clean mb-4">
            <div class="section-title">Ranking Risiko Negara</div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Wilayah</th>
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
                            <td><?php echo e($row->region ?? '-'); ?></td>
                            <td><?php echo e($row->weather_score); ?>%</td>
                            <td><?php echo e($row->inflation_score); ?>%</td>
                            <td><?php echo e($row->currency_score); ?>%</td>
                            <td><?php echo e($row->news_score); ?>%</td>
                            <td><strong><?php echo e($row->total_score); ?>/100</strong></td>
                            <td>
                                <span class="risk-badge
                                    <?php echo e($row->total_score >= 60 ? 'risk-high' : ($row->total_score >= 35 ? 'risk-medium' : 'risk-low')); ?>">
                                    <?php echo e($row->risk_level); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CURRENCY + PORT -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card-clean">
                    <div class="section-title">Ringkasan Risiko Kurs</div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                            <tr>
                                <th>Negara</th>
                                <th>Kurs</th>
                                <th>Perubahan</th>
                                <th>Risiko</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $currencyRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($row->country_name); ?></strong></td>
                                    <td>
                                        <?php echo e($row->base_currency); ?>/<?php echo e($row->target_currency); ?>

                                        <div class="metric-sub">
                                            <?php echo e(number_format($row->exchange_rate, 2)); ?>

                                        </div>
                                    </td>
                                    <td><?php echo e($row->change_percentage); ?>%</td>
                                    <td>
                                        <span class="risk-badge
                                            <?php echo e($row->currency_risk_score >= 60 ? 'risk-high' : ($row->currency_risk_score >= 35 ? 'risk-medium' : 'risk-low')); ?>">
                                            <?php echo e($row->currency_risk_score); ?>/100
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-clean">
                    <div class="section-title">Ringkasan Risiko Pelabuhan</div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                            <tr>
                                <th>Pelabuhan</th>
                                <th>Negara</th>
                                <th>Status</th>
                                <th>Risiko</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $portRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $port): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-pin-map-fill text-primary"></i>
                                        <strong><?php echo e($port->name); ?></strong>
                                        <div class="metric-sub"><?php echo e($port->city ?? '-'); ?></div>
                                    </td>
                                    <td><?php echo e($port->country_name ?? '-'); ?></td>
                                    <td>
                                        <span class="risk-badge
                                            <?php echo e($port->status === 'Aman' ? 'risk-low' : ($port->status === 'Waspada' ? 'risk-high' : 'risk-medium')); ?>">
                                            <?php echo e($port->status); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($port->port_risk_score); ?>/100</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- NEWS SUMMARY -->
        <div class="card-clean">
            <div class="section-title">Ringkasan Sinyal Berita</div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Judul Berita</th>
                        <th>Kategori</th>
                        <th>Sentimen</th>
                        <th>Skor</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $newsRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $news): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong><?php echo e($news->country_name ?? '-'); ?></strong></td>
                            <td><?php echo e($news->title); ?></td>
                            <td><?php echo e($news->category ?? '-'); ?></td>
                            <td>
                                <span class="risk-badge
                                    <?php echo e($news->sentiment === 'Negative' ? 'risk-high' : ($news->sentiment === 'Positive' ? 'risk-low' : 'risk-medium')); ?>">
                                    <?php echo e($news->sentiment); ?>

                                </span>
                            </td>
                            <td>
                                +<?php echo e($news->positive_score); ?>

                                /
                                -<?php echo e($news->negative_score); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            © <?php echo e(date('Y')); ?> Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const highRiskCount = Number(<?php echo json_encode($summary['high_risk_count'], 15, 512) ?>);
    const mediumRiskCount = Number(<?php echo json_encode($summary['medium_risk_count'], 15, 512) ?>);
    const lowRiskCount = Number(<?php echo json_encode($summary['low_risk_count'], 15, 512) ?>);

    new Chart(document.getElementById('riskSummaryChart'), {
        type: 'doughnut',
        data: {
            labels: ['Risiko Tinggi', 'Risiko Sedang', 'Risiko Rendah'],
            datasets: [{
                data: [highRiskCount, mediumRiskCount, lowRiskCount],
                backgroundColor: ['#ef4444', '#f59e0b', '#22c55e'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @media print {
        .sidebar,
        .topbar button {
            display: none !important;
        }

        .main {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .content {
            padding: 20px !important;
        }

        .card-clean {
            box-shadow: none !important;
            break-inside: avoid;
        }
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views/reports/index.blade.php ENDPATH**/ ?>