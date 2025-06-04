<?= session()->getFlashdata('sukses') ? '<div class="alert alert-success">'.session()->getFlashdata('sukses').'</div>' : '' ?>

<a href="<?= base_url('admin/kelompokjam/tambah'); ?>" class="btn btn-primary mb-3"><i class="fa fa-plus"></i> Tambah</a>

<table id="tabel" class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Bagian</th>
            <th>Shift</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($kelompokjam as $kj) : ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= esc($kj['bagian']); ?></td>
            <td><?= esc($kj['shift']); ?></td>
            <td><?= esc($kj['jammasuk']); ?></td>
            <td><?= esc($kj['jampulang']); ?></td>
            <td>
                <a href="<?= base_url('admin/kelompokjam/edit/' . $kj['id']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                <a href="<?= base_url('admin/kelompokjam/hapus/' . $kj['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');">
                    <i class="fa fa-trash"></i> Hapus
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    $(document).ready(function() {
        $('#tabel').DataTable();
    });
</script>