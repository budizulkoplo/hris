
    <?= \Config\Services::validation()->listErrors() ?>

    <form action="<?= base_url('admin/kajian/edit/' . $kajian['idkajian']) ?>" method="post">
        <div class="form-group">
            <label>Nama Kajian</label>
            <input type="text" name="namakajian" class="form-control" value="<?= esc($kajian['namakajian']) ?>" required>
        </div>
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="<?= $kajian['tanggal'] ?>" required>
        </div>
        <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="lokasi" class="form-control" value="<?= esc($kajian['lokasi']) ?>" required>
        </div>
        <!-- <div class="form-group">
            <label>QR Code (opsional)</label>
            <input type="text" name="qrcode" class="form-control" value="<?= esc($kajian['qrcode']) ?>">
        </div> -->
        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"><?= esc($kajian['keterangan']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="<?= base_url('admin/kajian') ?>" class="btn btn-secondary">Kembali</a>
    </form>


