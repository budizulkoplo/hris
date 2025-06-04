<?= form_open(base_url('admin/pengajuan/simpantugasluar')); ?>
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
    <label class="col-3 col-form-label">Tanggal Tugas Luar</label>
    <div class="col-9">
        <div class="input-group date">
            <input type="text" name="tanggal_tugas" id="tanggal_tugas" class="form-control" required autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Alasan Tugas Luar</label>
    <div class="col-9">
        <textarea name="alasan" class="form-control" required></textarea>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Ajukan Tugas Luar
    </button>
    <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
</div>

<?= form_close(); ?>

<!-- Tabel Daftar Tugas Luar -->
<h4 class="mt-4">Daftar Tugas Luar Pegawai</h4>
<table id="table" class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Tugas Luar</th>
            <th>Alasan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($daftar_tugasluar)) : ?>
            <?php $no = 1; ?>
            <?php foreach ($daftar_tugasluar as $tugas) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d-m-Y', strtotime($tugas['tgltugasluar'])); ?></td>
                    <td><?= esc($tugas['alasan']); ?></td>
                    <td>
                        <a href="<?= base_url('admin/pengajuan/batalTugasLuar/' . $tugas['idtugasluar']); ?>" 
                            class="btn btn-danger btn-sm" 
                            onclick="return confirm('Yakin ingin membatalkan tugas luar ini?');">
                            <i class="fa fa-times"></i> Batal
                        </a>
                        <a href="<?= base_url('admin/pengajuan/cetak_tugasluar/' . $tugas['idtugasluar']); ?>" 
                            class="btn btn-sm btn-primary" target="_blank">
                            <i class="fa fa-print"></i> Cetak Surat
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" class="text-center">Belum ada riwayat tugas luar</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('#tanggal_tugas').datepicker({
            format: 'mm/dd/yyyy', 
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
