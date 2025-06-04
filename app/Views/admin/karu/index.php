<?php include 'tambah.php'; ?>

<div class="table-responsive">
    <table id="tabel" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th width="20">No</th>
                <th>PIN</th>
                <th>Nama Karu</th>
                <th>Kelompok Kerja</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($karu as $k) : ?>
                <tr>
                    <td><?= $no; ?></td>
                    <td><?= esc($k['pin']); ?></td>
                    <td><?= esc($k['nama']); ?></td>
                    <td><?= esc($k['namakelompok']); ?></td>
                    <td>
                        <a href="<?= base_url('admin/karu/edit/' . $k['idkaru']); ?>" class="btn btn-success btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        <a href="<?= base_url('admin/karu/tambahPegawai/' . $k['idkaru']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-user-plus"></i> Update Regu</a>
                        <a href="<?= base_url('admin/karu/delete/' . $k['idkaru']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><i class="fa fa-trash"></i> Hapus</a>
                    </td>
                </tr>
            <?php $no++; endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#tabel').DataTable();
    });
</script>
