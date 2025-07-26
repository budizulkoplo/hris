<?= form_open(base_url('admin/karu/tambahPegawai/' . $idkaru)); ?>
<?= csrf_field(); ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php elseif (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<!-- Informasi Kepala Regu dan Kelompok Kerja -->
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Nama KARU:</strong> <?= esc($karu['nama'] ?? 'Tidak Diketahui'); ?></p>
        <p><strong>Nama Kelompok Kerja:</strong> <?= esc($karu['namakelompok'] ?? 'Tidak Diketahui'); ?></p>
    </div>
</div>

<!-- Form Pilih Pegawai -->
<div class="form-group row">
    <label class="col-3">Pilih Pegawai</label>
    <div class="col-9">
        <select name="idpegawai" class="form-control-sm select2" required>
            <option value="">Pilih Pegawai</option>
            <?php
            // Ambil daftar pegawai yang belum masuk dalam kelompok kerja
            $pegawaiYangSudahAda = array_column($Karupegawai, 'pegawai_pin');

            foreach ($pegawai as $p) :
                $pegawaiId = $p['idpegawai'] ?? $p['pegawai_pin'];
                if (!in_array($pegawaiId, $pegawaiYangSudahAda)) :
            ?>
                <option value="<?= esc($pegawaiId); ?>">
                    <?= esc($p['pegawai_nama']); ?>
                </option>
            <?php
                endif;
            endforeach;
            ?>
        </select>
    </div>
</div>

<!-- Tombol Submit -->
<div class="mt-3">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Tambah
    </button>
    <a href="<?= base_url('admin/karu'); ?>" class="btn btn-secondary">Kembali</a>
</div>

<?= form_close(); ?>

<!-- Daftar Pegawai di Kelompok Kerja -->
<h4 class="mt-4">Daftar Pegawai di Kelompok Kerja</h4>
<div class="table-responsive">
    <table id="TabelKaruPegawai" class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($Karupegawai) && is_array($Karupegawai)): ?>
                <?php $no = 1; ?>
                <?php foreach ($Karupegawai as $kp) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= esc($kp['pegawai_nama'] ?? 'Tidak Ada Nama'); ?></td>
                        <td>
                            <div class="btn-group">
                            <a href="<?= base_url('admin/pengajuan/cuti/' . esc($kp['pegawai_pin']) . (isset($idkaru) ? '/' . esc($idkaru) : '')); ?>" 
                                class="btn btn-info btn-sm">
                                    <i class="fa fa-taxi"></i> Cuti
                                </a>
                                <a href="<?= base_url('admin/pengajuan/lembur/' . $kp['pegawai_pin']); ?>" 
                                   class="btn btn-success btn-sm">
                                    <i class="fa fa-clock"></i> Lembur
                                </a>
                                <a href="<?= base_url('admin/pengajuan/tugasluar/' . $kp['pegawai_pin']); ?>" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-briefcase"></i> Tugas Luar
                                </a>
                                <a href="<?= base_url('admin/pengajuan/izin/' . $kp['pegawai_pin']); ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fa fa-medkit"></i> Izin Sakit
                                </a>
                                <a href="<?= base_url('admin/pengajuan/rujukan/' . $kp['pegawai_pin']); ?>" 
                                   class="btn btn-secondary btn-sm">
                                    <i class="fa fa-bed"></i> Rujukan
                                </a>
                            </div>

                            <!-- Tombol Hapus -->
                            <a href="<?= base_url('admin/karu/hapusPegawai/' . $kp['idkarupegawai']); ?>" 
                               class="btn btn-danger btn-sm ml-2" 
                               onclick="return confirm('Yakin ingin menghapus pegawai ini dari kelompok kerja?');">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Belum ada pegawai di kelompok kerja ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#TabelKaruPegawai').DataTable({
            pageLength: 25, // Jumlah baris per halaman default
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
</script>
