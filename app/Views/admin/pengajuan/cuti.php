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

<!-- Jenis Cuti -->
<div class="form-group row">
    <label class="col-3 col-form-label">Jenis Cuti</label>
    <div class="col-9">
        <select name="jeniscuti" class="form-control" id="jeniscuti" required>
            <option value="">Pilih Jenis Cuti</option>
            <option value="Cuti Tahunan">Cuti Tahunan</option>
            <option value="Cuti Sakit">Cuti Sakit</option>
            <option value="Cuti Tidak Dibayar">Cuti Tidak Dibayar</option>
            <option value="Cuti Melahirkan">Cuti Melahirkan</option>
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Tanggal Mulai Cuti</label>
    <div class="col-9">
        <div class="input-group date">
            <input type="text" name="tgl_mulai" id="tgl_mulai" class="form-control" required autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Tanggal Selesai Cuti</label>
    <div class="col-9">
        <div class="input-group date">
            <input type="text" name="tgl_selesai" id="tgl_selesai" class="form-control" required autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-3 col-form-label">Jumlah Hari</label>
    <div class="col-9">
        <input type="text" name="jml_hari" id="jml_hari" class="form-control" readonly>
    </div>
</div>

<!-- Sisa Cuti -->
<div class="form-group row" id="form_sisa_cuti">
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

<!-- Pegawai Pengganti -->
<div class="form-group row">
    <label class="col-3 col-form-label">Pegawai Pengganti</label>
    <div class="col-9">
        <select name="idpengganti" class="form-control-sm select2" required>
            <option value="">Pilih Pegawai</option>
            <?php foreach ($pegawai as $p) : ?>
                <option value="<?= esc($p['pegawai_pin']); ?>">
                    <?= esc($p['pegawai_pin']) ?> - <?= esc($p['pegawai_nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>
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
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Jumlah Hari</th>
            <th>Cuti</th>
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
                    <td><?= date('d-m-Y', strtotime($cuti['tgl_mulai'])); ?></td>
                    <td><?= date('d-m-Y', strtotime($cuti['tgl_selesai'])); ?></td>
                    <td><?= $cuti['jml_hari']; ?></td>
                    <td><?= $cuti['jeniscuti']; ?></td>
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
                <td colspan="6" class="text-center">Belum ada riwayat cuti</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- SCRIPT -->
<script>
$(document).ready(function() {
    $('#tgl_mulai, #tgl_selesai').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
        todayHighlight: true
    });

    function calculateDays() {
        var start = $('#tgl_mulai').datepicker('getDate');
        var end = $('#tgl_selesai').datepicker('getDate');
        if (start && end) {
            var timeDiff = end.getTime() - start.getTime();
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
            $('#jml_hari').val(diffDays);
        }
    }

    $('#tgl_mulai, #tgl_selesai').change(calculateDays);

    // Sembunyikan sisa cuti jika jenis cuti = cuti melahirkan
    $('#jeniscuti').change(function() {
        var jenis = $(this).val();
        if (jenis === 'Cuti Melahirkan') {
            $('#form_sisa_cuti').hide();
        } else {
            $('#form_sisa_cuti').show();
        }
    }).trigger('change'); // Jalankan saat awal load juga
});
</script>
