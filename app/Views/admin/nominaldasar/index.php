<?php if (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<h4>Edit Nominal Dasar</h4>

<form action="<?= base_url('admin/nominaldasar/update') ?>" method="post">
    <?= csrf_field() ?>
    <div class="form-group mb-2">
        <label>Rujukan</label>
        <input type="number" name="rujukan" value="<?= esc($nominal['rujukan']) ?>" class="form-control" required>
    </div>
    <div class="form-group mb-2">
        <label>Uang Makan</label>
        <input type="number" name="uangmakan" value="<?= esc($nominal['uangmakan']) ?>" class="form-control" required>
    </div>
    <div class="form-group mb-2">
        <label>BPJS</label>
        <input type="number" name="bpjs" value="<?= esc($nominal['bpjs']) ?>" class="form-control">
    </div>
    <div class="form-group mb-3">
        <label>Koperasi</label>
        <input type="number" name="koperasi" value="<?= esc($nominal['koperasi']) ?>" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">
        <i class="fa fa-save"></i> Simpan
    </button>
</form>
