
<?php if (session()->getFlashdata('sukses')): ?>
<div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<div class="table-responsive"> <!-- Tambahkan ini -->
<table id="tabel" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Dokter</th>
            <th>Hari</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($jadwal as $j): ?>
        <tr>
            <form method="post" action="<?= base_url('admin/jadwaldokter/update') ?>">
                <td><?= $no++ ?></td>
                <td><?= esc($j['nama_dokter']) ?></td>
                <td><?= esc($j['hari']) ?></td>
                <td>
                    <input type="hidden" name="id_jadwal" value="<?= $j['id_jadwal'] ?>">
                    <input type="time" name="jam_mulai" value="<?= $j['jam_mulai'] ?>" class="form-control" required>
                </td>
                <td>
                    <input type="time" name="jam_selesai" value="<?= $j['jam_selesai'] ?>" class="form-control" required>
                </td>
                <td>
                    <select name="status" class="form-control">
                        <option value="1" <?= $j['status'] == 1 ? 'selected' : '' ?>>Ada</option>
                        <option value="0" <?= $j['status'] == 0 ? 'selected' : '' ?>>Cuti</option>
                    </select>
                </td>
                <td>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div> <!-- Tutup table-responsive -->
<script>
    $(document).ready(function() {
        $('#tabel').DataTable({
            pageLength: 100,
            responsive: false, // biar tidak auto-collapse
            scrollX: true      // aktifkan scroll horizontal di DataTables
        });
    });
</script>