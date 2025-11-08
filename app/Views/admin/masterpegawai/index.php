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
                <th>NIK</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Bagian</th> <!-- Tambahan kolom -->
                <th>Administrasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($pegawai as $p): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= esc($p['nik']) ?></td>
                    <td><?= esc($p['pegawai_nama']) ?></td>
                    <td><?= esc($p['jabatan']) ?></td>
                    <td><?= esc($p['bagian']) ?></td> <!-- Tampilkan bagian -->
                    <td>
                        <div class="btn-group">
                            <a href="<?= base_url('admin/pengajuan/cuti/' . esc($p['pegawai_pin']) . (isset($idkaru) ? '/' . esc($idkaru) : '')); ?>" 
                                class="btn btn-info btn-sm">
                                <i class="fa fa-taxi"></i> Cuti
                            </a>
                            <a href="<?= base_url('admin/pengajuan/lembur/' . $p['pegawai_pin']); ?>" 
                               class="btn btn-success btn-sm">
                                <i class="fa fa-clock"></i> Lembur
                            </a>
                            <a href="<?= base_url('admin/pengajuan/tugasluar/' . $p['pegawai_pin']); ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fa fa-briefcase"></i> Tugas Luar
                            </a>
                            <a href="<?= base_url('admin/pengajuan/izin/' . $p['pegawai_pin']); ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="fa fa-medkit"></i> Izin Sakit
                            </a>
                        </div>
                    </td>
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
