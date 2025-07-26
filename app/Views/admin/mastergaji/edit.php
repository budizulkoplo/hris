<?php if (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('gagal')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('gagal') ?></div>
<?php endif; ?>

<form action="<?= base_url('admin/mastergaji/update/' . $gaji['idgaji']) ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="idgaji" value="<?= $gaji['idgaji'] ?>">

    <div class="form-group">
        <label for="pegawai_pin">Nama Pegawai</label>
        <input type="text" class="form-control" value="<?= esc($pegawai['pegawai_nama']) ?>" disabled>
    </div>

    <div class="form-group">
        <label for="tglaktif">Tanggal Aktif</label>
        <input type="date" name="tglaktif" class="form-control" value="<?= esc($gaji['tglaktif']) ?>" required>
    </div>

    <div class="form-group">
        <label for="gajipokok">Gaji Pokok</label>
        <input type="number" name="gajipokok" class="form-control" value="<?= esc($gaji['gajipokok']) ?>" step="0.01" required>
    </div>

    <div class="form-group">
        <label for="tunjstruktural">Tunjangan Struktural</label>
        <input type="number" name="tunjstruktural" class="form-control" value="<?= esc($gaji['tunjstruktural']) ?>" step="0.01">
    </div>

    <div class="form-group">
        <label for="tunjfungsional">Tunjangan Fungsional</label>
        <input type="number" name="tunjfungsional" class="form-control" value="<?= esc($gaji['tunjfungsional']) ?>" step="0.01">
    </div>

    <div class="form-group">
        <label for="tunjkeluarga">Tunjangan Keluarga</label>
        <input type="number" name="tunjkeluarga" class="form-control" value="<?= esc($gaji['tunjkeluarga']) ?>" step="0.01">
    </div>

    <div class="form-group">
        <label for="tunjapotek">Tunjangan Apotek</label>
        <input type="number" name="tunjapotek" class="form-control" value="<?= esc($gaji['tunjapotek']) ?>" step="0.01">
    </div>

    <div class="form-group">
        <label for="tunjapotek">per Kehadiran</label>
        <input type="number" name="kehadiran" class="form-control" value="<?= esc($gaji['kehadiran']) ?>" step="0.01">
    </div>

    <div class="form-group">
        <label for="verifikasi">Verifikasi</label>
        <select name="verifikasi" class="form-control">
            <option value="0" <?= $gaji['verifikasi'] == '0' ? 'selected' : '' ?>>Belum Verifikasi</option>
            <option value="1" <?= $gaji['verifikasi'] == '1' ? 'selected' : '' ?>>Terverifikasi</option>
        </select>
    </div>
    <a href="<?= base_url('admin/mastergaji') ?>" class="btn btn-primary btn-sm">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar Pegawai
    </a>
    <button type="submit" class="btn btn-success btn-sm">Simpan Perubahan</button>
</form>
