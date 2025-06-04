<?php if (session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('sukses') ?></div>
<?php endif; ?>

<a href="<?= base_url('admin/kajian/tambah') ?>" class="btn btn-primary mb-3">Tambah Kajian</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kajian</th>
            <th>Tanggal</th>
            <th>Lokasi</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($kajian as $k): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= esc($k['namakajian']) ?></td>
                <td><?= date('d/m/Y', strtotime($k['tanggal'])) ?></td>
                <td><?= esc($k['lokasi']) ?></td>
                <td><?= esc($k['keterangan']) ?></td>
                <td>
                    <a href="#" onclick="openFullscreenQRCode('<?= base_url('admin/kajian/qrcode/' . $k['idkajian']) ?>'); return false;" 
                        class="btn btn-success btn-sm">
                        <i class="fa fa-qrcode"></i> Tampilkan QR
                    </a>
                    <a href="<?= base_url('admin/kajian/edit/' . $k['idkajian']) ?>" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a>
                    <a href="<?= base_url('admin/kajian/delete/' . $k['idkajian']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')"><i class="fa fa-trash"></i> Hapus</a>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<!-- Modal QR Code -->
<div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title" id="qrcodeModalLabel">QR Code Kajian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="qrcode-container" class="mb-3"></div>
                <p id="qrcode-link" class="text-primary"></p>
                <p id="namakajian-text" class="fw-bold"></p>
                <button class="btn btn-success mt-2" onclick="printQRCode()">Cetak QR Code</button>
            </div>
        </div>
    </div>
</div>

<!-- Area cetak (disembunyikan) -->
<div id="print-area" style="display: none; text-align: center;">
    <div id="print-qrcode"></div>
    <p id="print-link" style="font-size: 12px;"></p>
    <p id="print-namakajian" style="font-weight: bold;"></p>
</div>

<script>
function openFullscreenQRCode(url) {
    const w = window.screen.width;
    const h = window.screen.height;
    window.open(
        url,
        '_blank',
        `toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=${w},height=${h},top=0,left=0`
    );
}
</script>