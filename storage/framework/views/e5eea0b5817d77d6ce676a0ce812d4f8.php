<form action="<?php echo e(route('admin.ports.update', $port->id)); ?>" method="POST" class="data-ajax-edit-form" data-refresh-section="ports-section">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama Pelabuhan</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $port->name)); ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Kota</label>
            <input type="text" name="city" class="form-control" value="<?php echo e(old('city', $port->city)); ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Negara</label>
            <select name="country_id" class="form-select" required>
                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php echo e(old('country_id', $port->country_id) == $c->id ? 'selected' : ''); ?>>
                        <?php echo e($c->name); ?> (<?php echo e($c->country_code); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Latitude</label>
            <input type="number" step="0.000001" name="latitude" class="form-control" value="<?php echo e(old('latitude', $port->latitude)); ?>" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Longitude</label>
            <input type="number" step="0.000001" name="longitude" class="form-control" value="<?php echo e(old('longitude', $port->longitude)); ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Status Pelabuhan</label>
            <select name="status" class="form-select" required>
                <?php $__currentLoopData = ['Aman', 'Normal', 'Waspada', 'Siaga', 'Darurat']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($st); ?>" <?php echo e(old('status', $port->status) === $st ? 'selected' : ''); ?>>
                        <?php echo e($st); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Skor Risiko Pelabuhan (0-100)</label>
            <input type="number" step="0.1" min="0" max="100" name="port_risk_score" class="form-control" value="<?php echo e(old('port_risk_score', $port->port_risk_score)); ?>" required>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>
<?php /**PATH D:\laragon\www\supply-chain-management\resources\views\admin\partials\edit-port.blade.php ENDPATH**/ ?>