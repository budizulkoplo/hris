<form action="<?= base_url('admin/mastergaji/save') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="pegawai_pin" value="<?= esc($pegawai['pegawai_pin']) ?>">

    <div class="mb-3">
        <label>Nama Pegawai</label>
        <input type="text" class="form-control" value="<?= esc($pegawai['pegawai_nama']) ?>" disabled>
    </div>

    <div class="mb-3">
        <label>Tanggal Aktif Gaji</label>
        <input type="date" name="tglaktif" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Gaji Pokok</label>
        <input type="number" name="gajipokok" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Tunjangan Struktural</label>
        <input type="number" name="tunjstruktural" class="form-control">
    </div>

    <div class="mb-3">
        <label>Tunjangan Fungsional</label>
        <input type="number" name="tunjfungsional" class="form-control">
    </div>

    <div class="mb-3">
        <label>Tunjangan Keluarga</label>
        <input type="number" name="tunjkeluarga" class="form-control">
    </div>

    <div class="mb-3">
        <label>Tunjangan Apotek</label>
        <input type="number" name="tunjapotek" class="form-control">
    </div>

    <div class="mb-3">
        <label>per Kehadiran</label>
        <input type="number" name="kehadiran" class="form-control">
    </div>

    <div class="mb-3">
        <label>Verifikasi</label>
        <select name="verifikasi" class="form-control">
            <option value="0">Belum Terverifikasi</option>
            <option value="1">Terverifikasi</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Simpan Gaji</button>
    <a href="<?= base_url('admin/mastergaji') ?>" class="btn btn-secondary">Kembali</a>
</form>
