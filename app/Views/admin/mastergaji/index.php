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
                        <a href="<?= base_url('admin/mastergaji/listgaji/' . $p['pegawai_pin']) ?>" class="btn btn-info btn-sm">
                            <i class="fa fa-calculator"></i> Lihat Riwayat Gaji
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#tabel').DataTable({
            pageLength: 150
        });
    });
</script>
