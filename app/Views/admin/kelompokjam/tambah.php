<form action="<?= base_url('admin/kelompokjam/tambah'); ?>" method="post">
    <?= csrf_field(); ?>

    <div class="form-group">
        <label>Bagian</label>
        <input type="text" name="bagian" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Shift</label>
        <input type="text" name="shift" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Jam Masuk</label>
        <input type="time" name="jammasuk" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Jam Pulang</label>
        <input type="time" name="jampulang" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/kelompokjam'); ?>" class="btn btn-secondary">Kembali</a>
</form>
