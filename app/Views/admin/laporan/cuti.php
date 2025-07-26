<?= $this->include('admin/laporan/_template_header'); ?>
<h4 class="mb-0">Laporan Cuti Pegawai</h4>
...
<?php if (empty($cuti)): ?>
    <div class="alert alert-warning text-center">Data cuti tidak ditemukan.</div>
<?php else: ?>
    <div class="table-responsive">
        <table id="laporanTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>Tanggal Cuti</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($cuti as $c): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($c['pegawai_nama']) ?></td>
                        <td><?= esc($c['jabatan']) ?></td>
                        <td><?= date('d-m-Y', strtotime($c['tglcuti'])) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?= $this->include('admin/laporan/_template_footer'); ?>
