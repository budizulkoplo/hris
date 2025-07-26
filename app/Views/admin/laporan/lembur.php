<?= $this->include('admin/laporan/_template_header'); ?>
<h4 class="mb-0">Laporan Lembur Pegawai</h4>
...
<?php if (empty($lembur)): ?>
    <div class="alert alert-warning text-center">Data lembur tidak ditemukan.</div>
<?php else: ?>
    <div class="table-responsive">
        <table id="laporanTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>Tanggal</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($lembur as $l): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($l['pegawai_nama']) ?></td>
                        <td><?= esc($l['jabatan']) ?></td>
                        <td><?= date('d-m-Y', strtotime($l['tgllembur'])) ?></td>
                        <td><?= esc($l['alasan']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?= $this->include('admin/laporan/_template_footer'); ?>
