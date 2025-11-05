<?php if (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<h4>Riwayat Gaji: <?= esc($pegawai['pegawai_nama']) ?> (<?= esc($pegawai['pegawai_pin']) ?>)</h4>

<div class="mb-3">
    <a href="<?= base_url('admin/mastergaji') ?>" class="btn btn-primary btn-sm mr-2">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
    <a href="<?= base_url('admin/mastergaji/input/' . $pegawai['pegawai_pin']) ?>" class="btn btn-success btn-sm">
        <i class="fa fa-plus"></i> Input Gaji Baru
    </a>
</div>

<div class="table-responsive">
    <table id="tabel" class="table table-bordered table-striped table-sm">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Tgl Aktif</th>
                <th>Gaji Pokok</th>
                <th>Tunj. Struktural</th>
                <th>Tunj. Fungsional</th>
                <th>Tunj. Keluarga</th>
                <th>Tunj. Apotek</th>
                <th>Kehadiran</th>
                <th>Lembur Khusus</th>
                <th>Verifikasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($listgaji)) : ?>
                <?php $no = 1; foreach ($listgaji as $item) : ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center"><?= date('d-m-Y', strtotime($item['tglaktif'])) ?></td>
                        <td class="text-end"><?= number_format($item['gajipokok']) ?></td>
                        <td class="text-end"><?= number_format($item['tunjstruktural']) ?></td>
                        <td class="text-end"><?= number_format($item['tunjfungsional']) ?></td>
                        <td class="text-end"><?= number_format($item['tunjkeluarga']) ?></td>
                        <td class="text-end"><?= number_format($item['tunjapotek']) ?></td>
                        <td class="text-end"><?= number_format($item['kehadiran']) ?></td>
                        <td class="text-end"><?= number_format($item['lemburkhusus']) ?></td>
                        <td>
                            <?= $item['verifikasi'] == 1 ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('admin/mastergaji/edit/' . $item['idgaji']) ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="<?= base_url('admin/mastergaji/hapus/' . $item['idgaji']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data gaji ini?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>

                    </tr>
                <?php endforeach ?>
            <?php else : ?>
                <tr>
                    <td colspan="10" class="text-center">Belum ada data gaji untuk pegawai ini.</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#tabel').DataTable({
            pageLength: 25
        });
    });
</script>
