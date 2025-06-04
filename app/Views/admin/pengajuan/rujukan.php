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

<div class="form-group row">
    <label class="col-3 col-form-label">Tanggal Rujukan</label>
    <div class="col-9">
        <div class="input-group date">
            <input type="text" name="tanggal_rujukan" id="tanggal_rujukan" class="form-control" required autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Keterangan</label>
    <div class="col-9">
        <textarea name="keterangan" class="form-control" required></textarea>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Ajukan Rujukan
    </button>
    <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
</div>

<?= form_close(); ?>

<!-- Tabel Daftar Rujukan -->
<h4 class="mt-4">Daftar Rujukan Pegawai</h4>
<table id="table" class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
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
                    <td><?= date('d-m-Y', strtotime($r['tglrujukan'])); ?></td>
                    <td><?= esc($r['keterangan']); ?></td>
                    <td>
                        <a href="<?= base_url('admin/pengajuan/batalRujukan/' . $r['idrujukan']); ?>" 
                            class="btn btn-danger btn-sm" 
                            onclick="return confirm('Yakin ingin membatalkan rujukan ini?');">
                            <i class="fa fa-times"></i> Batal
                        </a>
                        <a href="<?= base_url('admin/pengajuan/cetak_rujukan/' . $r['idrujukan']); ?>" 
                            class="btn btn-sm btn-primary" target="_blank">
                            <i class="fa fa-print"></i> Cetak Surat
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" class="text-center">Belum ada riwayat rujukan</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('#table').DataTable({
            "pageLength": 50
        });

        $('#tanggal_rujukan').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
