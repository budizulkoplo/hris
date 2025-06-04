
    <?= \Config\Services::validation()->listErrors() ?>

    <form action="<?= base_url('admin/kajian/tambah') ?>" method="post">
        <div class="form-group">
            <label>Nama Kajian</label>
            <input type="text" name="namakajian" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="lokasi" class="form-control" required>
        </div>
        <!-- <div class="form-group">
            <label>QR Code (opsional)</label>
            <input type="text" name="qrcode" class="form-control">
        </div> -->
        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="<?= base_url('admin/kajian') ?>" class="btn btn-secondary">Kembali</a>
    </form>


