<?php if (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<h4>Edit Nominal Dasar</h4>

<form action="<?= base_url('admin/nominaldasar/update') ?>" method="post">
    <?= csrf_field() ?>
    <div class="form-group mb-2">
        <label>Uang Makan</label>
        <input type="number" name="uangmakan" value="<?= esc($nominal['uangmakan']) ?>" class="form-control" required>
    </div>
    <div class="form-group mb-2">
        <label>BPJS TK</label>
        <input type="number" name="bpjs" value="<?= esc($nominal['bpjs']) ?>" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">
        <i class="fa fa-save"></i> Simpan
    </button>
</form>
