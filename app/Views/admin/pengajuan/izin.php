<?= form_open(base_url('admin/pengajuan/simpanizin')); ?>
<?= csrf_field(); ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php elseif (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session()->getFlashdata('sukses')) ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="form-group row">
    <label class="col-3 col-form-label">Pegawai</label>
    <div class="col-9">
        <select class="form-control" disabled>
            <option value="">Pilih Pegawai</option>
            <?php foreach ($pegawai as $p) : ?>
                <option value="<?= esc($p['pegawai_pin']); ?>" 
                    <?= isset($selected_pegawai) && $selected_pegawai == $p['pegawai_pin'] ? 'selected' : ''; ?> >
                    <?= esc($p['pegawai_nama']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="pegawai_pin" value="<?= esc($selected_pegawai ?? ''); ?>">
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Tanggal Izin</label>
    <div class="col-9">
        <div class="input-group date">
            <input type="text" name="tglizin" id="tglizin" class="form-control" required>
            <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Alasan Izin</label>
    <div class="col-9">
        <textarea name="alasan" class="form-control" required></textarea>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Ajukan Izin
    </button>
    <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
</div>

<?= form_close(); ?>

<!-- Tabel Daftar Izin -->
<h4 class="mt-4">Daftar Izin Pegawai</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Izin</th>
            <th>Alasan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($daftar_izin)) : ?>
            <?php $no = 1; ?>
            <?php foreach ($daftar_izin as $izin) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d-m-Y', strtotime($izin['tglizin'])); ?></td>
                    <td><?= esc($izin['alasan']); ?></td>
                    <td>
                        <a href="<?= base_url('admin/pengajuan/batalizin/' . $izin['idizin']); ?>" 
                            class="btn btn-danger btn-sm" 
                            onclick="return confirm('Yakin ingin membatalkan izin ini?');">
                            <i class="fa fa-times"></i> Batal
                        </a>
                        <a href="<?= base_url('admin/pengajuan/cetak_izin/' . $izin['idizin']); ?>" 
                            class="btn btn-sm btn-primary" target="_blank">
                            <i class="fa fa-print"></i> Cetak Surat
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" class="text-center">Belum ada riwayat izin</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('#tglizin').datepicker({
            format: 'yyyy-mm-dd', 
            autoclose: true,
            todayHighlight: true
        });
    });
</script>