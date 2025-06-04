<?= form_open(base_url('admin/pengajuan/simpancuti')); ?>
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
    <label class="col-3 col-form-label">Tanggal Cuti</label>
    <div class="col-9">
        <div class="input-group date">
            <input type="text" name="tanggal_cuti" id="tanggal_cuti" class="form-control" required autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Sisa Cuti</label>
    <div class="col-9">
        <input type="text" class="form-control" value="<?= esc($sisa_cuti); ?>" readonly>
        <input type="hidden" name="idkaru" value="<?= esc($idkaru ?? ''); ?>">
        <small class="text-muted">Sisa cuti berdasarkan jumlah cuti yang telah diambil dalam tahun berjalan.</small>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Alasan Cuti</label>
    <div class="col-9">
        <textarea name="alasan" class="form-control" required></textarea>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Ajukan Cuti
    </button>
    <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
</div>

<?= form_close(); ?>

<!-- Tabel Daftar Cuti -->
<h4 class="mt-4">Daftar Cuti Pegawai</h4>
<table id="tablecuti" class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Cuti</th>
            <th>Alasan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($daftar_cuti)) : ?>
            <?php $no = 1; ?>
            <?php foreach ($daftar_cuti as $cuti) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d-m-Y', strtotime($cuti['tglcuti'])); ?></td>
                    <td><?= esc($cuti['alasancuti']); ?></td>
                    <td>
                        <a href="<?= base_url('admin/pengajuan/batalcuti/' . $cuti['idcuti']); ?>" 
                            class="btn btn-danger btn-sm" 
                            onclick="return confirm('Yakin ingin membatalkan cuti ini?');">
                            <i class="fa fa-times"></i> Batal
                        </a>
                        <a href="<?= base_url('admin/pengajuan/cetak_cuti/' . $cuti['idcuti']); ?>" 
                            class="btn btn-sm btn-primary" target="_blank">
                            <i class="fa fa-print"></i> Cetak Surat
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" class="text-center">Belum ada riwayat cuti</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {

        $('#tanggal_cuti').datepicker({
            format: 'yyyy-mm-dd', 
            autoclose: true,
            todayHighlight: true
        });
    });

    $(document).ready(function () {
        var tanggal_terpakai = <?= json_encode(array_column($daftar_cuti, 'tglcuti')); ?>;

        $('#tanggal_cuti').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            beforeShowDay: function (date) {
                var tanggal = date.toISOString().split('T')[0]; 
                return tanggal_terpakai.includes(tanggal) ? false : true; 
            }
        });
    });
</script>

