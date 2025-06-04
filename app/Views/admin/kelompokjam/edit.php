<form action="<?= base_url('admin/kelompokjam/edit/' . $kelompokjam['id']); ?>" method="post">
    <?= csrf_field(); ?>

    <div class="form-group">
        <label>Bagian</label>
        <input type="text" name="bagian" class="form-control" value="<?= esc($kelompokjam['bagian']); ?>" required>
    </div>

    <div class="form-group">
        <label>Shift</label>
        <input type="text" name="shift" class="form-control" value="<?= esc($kelompokjam['shift']); ?>" required>
    </div>

    <div class="form-group">
        <label>Jam Masuk</label>
        <input type="time" name="jammasuk" class="form-control" value="<?= esc($kelompokjam['jammasuk']); ?>" required>
    </div>

    <div class="form-group">
        <label>Jam Pulang</label>
        <input type="time" name="jampulang" class="form-control" value="<?= esc($kelompokjam['jampulang']); ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="<?= base_url('admin/kelompokjam'); ?>" class="btn btn-secondary">Kembali</a>
</form>
