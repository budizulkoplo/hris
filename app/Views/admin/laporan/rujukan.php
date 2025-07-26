<?= $this->include('admin/laporan/_template_header'); ?>
<h4 class="mb-0">Laporan Rujukan Pegawai</h4>
...
<?php if (empty($rujukan)): ?>
    <div class="alert alert-warning text-center">Data rujukan tidak ditemukan.</div>
<?php else: ?>
    <div class="table-responsive">
        <table id="laporanTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>Nama Pasien</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($rujukan as $r): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($r['pegawai_nama']) ?></td>
                        <td><?= esc($r['jabatan']) ?></td>
                        <td><?= esc($r['namapasien']) ?></td>
                        <td><?= date('d-m-Y', strtotime($r['tglrujukan'])) ?></td>
                        <td><?= esc($r['keterangan']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?= $this->include('admin/laporan/_template_footer'); ?>
