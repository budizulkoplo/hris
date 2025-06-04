<?php if (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<a href="<?= base_url('admin/masterpegawai/input') ?>" class="btn btn-success mb-3">
    <i class="fa fa-plus"></i> Tambah Pegawai
</a>

<div class="table-responsive">
    <table id="tabel" class="table table-bordered table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>NIP</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>No HP</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($pegawai as $p): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= esc($p['pegawai_nip']) ?></td>
                    <td><?= esc($p['nik']) ?></td>
                    <td><?= esc($p['pegawai_nama']) ?></td>
                    <td><?= esc($p['jabatan']) ?></td>
                    <td><?= esc($p['nohp']) ?></td>
                    <td><?= esc($p['email']) ?></td>
                    <td class="text-center">
                        <a href="<?= base_url('admin/masterpegawai/edit/' . $p['pegawai_id']) ?>" class="btn btn-sm btn-primary">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#tabel').DataTable({ pageLength: 50 });
    });
</script>
