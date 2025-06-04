<?php if (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table id="tabel" class="table table-bordered table-striped">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama Pegawai</th>
                <!-- <th>Bagian</th> -->
                <th>Status Gaji</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($mastergaji as $p): ?>
                <?php
                    // Cek status verifikasi
                    if ($p['idgaji']) {
                        $verif = ($p['verifikasi'] == '1') ? '✅ Terverifikasi' : '❌ Belum Verifikasi';
                    } else {
                        $verif = '⏳ Belum Diinput';
                    }
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= esc($p['pegawai_nama']) ?></td>
                    <!-- <td><?= esc($p['bagian']) ?></td> -->
                    <td class="text-center"><?= $verif ?></td>
                    <td class="text-center">
                        <?php if ($p['idgaji']): ?>
                            <a href="<?= base_url('admin/mastergaji/edit/' . $p['idgaji']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-calculator"></i> Edit Gaji</a>
                        <?php else: ?>
                            <a href="<?= base_url('admin/mastergaji/input/' . $p['pegawai_pin']) ?>" class="btn btn-sm btn-success"><i class="fa fa-calculator"></i> Input Gaji</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#tabel').DataTable({
            pageLength: 50
        });
    });
</script>
