

<?php $__env->startSection('title', 'Edit Pelabuhan - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="topbar">
    <div class="page-title">
        <h1>Edit Pelabuhan</h1>
        <p>
            Perbarui informasi pelabuhan, lokasi, status operasional,
            dan skor risikonya.
        </p>
    </div>
</div>

<div class="content">

    
    <?php if(session('error')): ?>
        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
        >
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

    <div class="card-clean">
        <div
            class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"
        >
            <div>
                <div class="section-title mb-1">
                    Form Edit Pelabuhan
                </div>

                <div class="metric-sub">
                    ID Pelabuhan: <?php echo e($port->id); ?>

                </div>
            </div>

            <a
                href="<?php echo e(route('admin.index')); ?>"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Panel Admin
            </a>
        </div>

        <form
            action="<?php echo e(route('admin.ports.update', $port->id)); ?>"
            method="POST"
        >
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row g-3">

                
                <div class="col-lg-6 col-md-12">
                    <label for="name" class="form-label">
                        Nama Pelabuhan
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('name', $port->name)); ?>"
                        placeholder="Contoh: Pelabuhan Tanjung Priok"
                        maxlength="255"
                        required
                        autofocus
                    >

                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback">
                            <?php echo e($message); ?>

                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="col-lg-6 col-md-12">
                    <label for="city" class="form-label">
                        Kota
                    </label>

                    <input
                        type="text"
                        id="city"
                        name="city"
                        class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('city', $port->city)); ?>"
                        placeholder="Contoh: Jakarta"
                        maxlength="255"
                    >

                    <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback">
                            <?php echo e($message); ?>

                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="col-lg-6 col-md-12">
                    <label for="country_id" class="form-label">
                        Negara
                    </label>

                    <select
                        id="country_id"
                        name="country_id"
                        class="form-select <?php $__errorArgs = ['country_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        required
                    >
                        <option value="">
                            Pilih negara
                        </option>

                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($country->id); ?>"
                                <?php echo e((string) old(
                                        'country_id',
                                        $port->country_id
                                    ) === (string) $country->id
                                        ? 'selected'
                                        : ''); ?>

                            >
                                <?php echo e($country->name); ?>


                                <?php if(!empty($country->country_code)): ?>
                                    (<?php echo e($country->country_code); ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <?php $__errorArgs = ['country_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback">
                            <?php echo e($message); ?>

                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <div class="metric-sub mt-1">
                        Nama negara pada data pelabuhan akan mengikuti negara
                        yang dipilih.
                    </div>
                </div>

                
                <div class="col-lg-6 col-md-12">
                    <label for="status" class="form-label">
                        Status Pelabuhan
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        required
                    >
                        <option
                            value="Aman"
                            <?php echo e(old('status', $port->status) === 'Aman'
                                    ? 'selected'
                                    : ''); ?>

                        >
                            Aman
                        </option>

                        <option
                            value="Normal"
                            <?php echo e(old('status', $port->status) === 'Normal'
                                    ? 'selected'
                                    : ''); ?>

                        >
                            Normal
                        </option>

                        <option
                            value="Waspada"
                            <?php echo e(old('status', $port->status) === 'Waspada'
                                    ? 'selected'
                                    : ''); ?>

                        >
                            Waspada
                        </option>

                        <option
                            value="Siaga"
                            <?php echo e(old('status', $port->status) === 'Siaga'
                                    ? 'selected'
                                    : ''); ?>

                        >
                            Siaga
                        </option>

                        <option
                            value="Darurat"
                            <?php echo e(old('status', $port->status) === 'Darurat'
                                    ? 'selected'
                                    : ''); ?>

                        >
                            Darurat
                        </option>
                    </select>

                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback">
                            <?php echo e($message); ?>

                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="col-lg-4 col-md-6">
                    <label for="latitude" class="form-label">
                        Latitude
                    </label>

                    <input
                        type="number"
                        id="latitude"
                        name="latitude"
                        class="form-control <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('latitude', $port->latitude)); ?>"
                        placeholder="-6.1040"
                        min="-90"
                        max="90"
                        step="0.0000001"
                        required
                    >

                    <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback">
                            <?php echo e($message); ?>

                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <div class="metric-sub mt-1">
                        Nilai latitude harus antara -90 sampai 90.
                    </div>
                </div>

                
                <div class="col-lg-4 col-md-6">
                    <label for="longitude" class="form-label">
                        Longitude
                    </label>

                    <input
                        type="number"
                        id="longitude"
                        name="longitude"
                        class="form-control <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('longitude', $port->longitude)); ?>"
                        placeholder="106.8800"
                        min="-180"
                        max="180"
                        step="0.0000001"
                        required
                    >

                    <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback">
                            <?php echo e($message); ?>

                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <div class="metric-sub mt-1">
                        Nilai longitude harus antara -180 sampai 180.
                    </div>
                </div>

                
                <div class="col-lg-4 col-md-12">
                    <label for="port_risk_score" class="form-label">
                        Skor Risiko
                    </label>

                    <div class="input-group">
                        <input
                            type="number"
                            id="port_risk_score"
                            name="port_risk_score"
                            class="form-control <?php $__errorArgs = ['port_risk_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old(
                                'port_risk_score',
                                $port->port_risk_score ?? 0
                            )); ?>"
                            min="0"
                            max="100"
                            step="0.01"
                            required
                        >

                        <span class="input-group-text">
                            /100
                        </span>

                        <?php $__errorArgs = ['port_risk_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback">
                                <?php echo e($message); ?>

                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="metric-sub mt-1">
                        Skor risiko harus berada antara 0 sampai 100.
                    </div>
                </div>

                
                <div class="col-12">
                    <div class="recommendation-box">
                        <strong>Keterangan status:</strong>

                        <div class="mt-2">
                            <span class="risk-badge risk-low me-1">
                                Aman/Normal
                            </span>

                            Pelabuhan beroperasi dengan risiko rendah.
                        </div>

                        <div class="mt-2">
                            <span class="risk-badge risk-medium me-1">
                                Waspada
                            </span>

                            Pelabuhan memerlukan pemantauan lebih lanjut.
                        </div>

                        <div class="mt-2">
                            <span class="risk-badge risk-high me-1">
                                Siaga/Darurat
                            </span>

                            Pelabuhan memiliki tingkat risiko yang tinggi.
                        </div>
                    </div>
                </div>

                
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-save me-1"></i>
                            Simpan Perubahan
                        </button>

                        <a
                            href="<?php echo e(route('admin.index')); ?>"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Batal
                        </a>

                        <a
                            href="<?php echo e(route('ports.index', [
                                'country_id' => $port->country_id
                            ])); ?>"
                            class="btn btn-outline-primary"
                            target="_blank"
                        >
                            <i class="bi bi-map me-1"></i>
                            Lihat di Halaman Pelabuhan
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    
    <div class="card-clean mt-4">
        <div class="section-title">
            Informasi Data Saat Ini
        </div>

        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="route-summary-card h-100">
                    <small>Nama Pelabuhan</small>
                    <strong><?php echo e($port->name ?? '-'); ?></strong>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="route-summary-card h-100">
                    <small>Kota</small>
                    <strong><?php echo e($port->city ?? '-'); ?></strong>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="route-summary-card h-100">
                    <small>Status</small>

                    <?php
                        $currentStatus = $port->status ?? 'Aman';

                        $currentStatusClass = in_array(
                            $currentStatus,
                            ['Aman', 'Normal'],
                            true
                        )
                            ? 'risk-low'
                            : (
                                $currentStatus === 'Waspada'
                                    ? 'risk-medium'
                                    : 'risk-high'
                            );
                    ?>

                    <span class="risk-badge <?php echo e($currentStatusClass); ?>">
                        <?php echo e($currentStatus); ?>

                    </span>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="route-summary-card h-100">
                    <small>Skor Risiko</small>

                    <strong>
                        <?php echo e(number_format(
                            (float) ($port->port_risk_score ?? 0),
                            0
                        )); ?>/100
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        © <?php echo e(date('Y')); ?> Supply Chain Management.
        Semua hak dilindungi.
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views\ports\edit.blade.php ENDPATH**/ ?>