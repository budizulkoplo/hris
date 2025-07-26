<?= form_open(base_url('admin/pengajuan/simpanrujukan')); ?>
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

<h4 class="mb-3">Form Pengajuan Rujukan</h4>

<!-- Pegawai -->
<div class="form-group row">
    <label class="col-3 col-form-label">Pegawai</label>
    <div class="col-9">
        <select class="form-control" disabled>
            <option value="">Pilih Pegawai</option>
            <?php foreach ($pegawai as $p) : ?>
                <option value="<?= esc($p['pegawai_pin']); ?>" 
                    <?= isset($selected_pegawai) && $selected_pegawai == $p['pegawai_pin'] ? 'selected' : ''; ?>>
                    <?= esc($p['pegawai_nama']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="pegawai_pin" value="<?= esc($selected_pegawai ?? ''); ?>">
    </div>
</div>

<!-- Nama Pasien -->
<div class="form-group row">
    <label class="col-3 col-form-label">Nama Pasien</label>
    <div class="col-9">
        <input type="text" name="namapasien" class="form-control" required maxlength="100" placeholder="Contoh: Nama Istri / Anak" autocomplete="off">
    </div>
</div>

<!-- Tanggal Rujukan -->
<div class="form-group row">
    <label class="col-3 col-form-label">Tanggal Rujukan</label>
    <div class="col-9">
        <div class="input-group date">
            <input type="text" name="tanggal" id="tanggal" class="form-control" required autocomplete="off" placeholder="MM/DD/YYYY">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
</div>

<!-- Keterangan -->
<div class="form-group row">
    <label class="col-3 col-form-label">Keterangan</label>
    <div class="col-9">
        <textarea name="keterangan" class="form-control" rows="3" required maxlength="255" placeholder="Masukkan keterangan rujukan..."></textarea>
    </div>
</div>

<!-- Tombol -->
<div class="mt-3">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Ajukan Rujukan
    </button>
    <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
</div>

<?= form_close(); ?>
<h4 class="mt-4">Riwayat Rujukan</h4>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pasien</th>
            <th>Tanggal Rujukan</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($daftar_rujukan)) : ?>
            <?php $no = 1; ?>
            <?php foreach ($daftar_rujukan as $r) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= esc($r['namapasien']); ?></td>
                    <td><?= date('d-m-Y', strtotime($r['tglrujukan'])); ?></td>
                    <td><?= esc($r['keterangan']); ?></td>
                    <td>
                        <a href="<?= base_url('admin/pengajuan/batalRujukan/' . $r['idrujukan']); ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin membatalkan rujukan ini?');">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="5" class="text-center">Belum ada riwayat rujukan</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<script>
$(document).ready(function() {
    $('#tanggal').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
        todayHighlight: true
    });
});
</script>
