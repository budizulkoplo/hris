
<h2>Kehadiran Kajian</h2>

<p><strong>Judul Kajian:</strong> <?= esc($kajian['namakajian']) ?></p>
<p><strong>Tanggal:</strong> <?= date('d/m/Y') ?></p>
<p><strong>Lokasi:</strong> <?= esc($kajian['lokasi']) ?></p>
<hr>
<p><strong>Barcode Terdeteksi:</strong> <?= esc($barcode) ?></p>

<div style="margin-top:20px;">
    <?php if ($duplikat === true): ?>
        <p style="color: red; font-size: 18px;">
            <strong>
                <?= isset($pesan) ? esc($pesan) : 'Barcode sudah pernah digunakan.' ?>
            </strong>
        </p>
    <?php else: ?>
        <p style="color: green; font-size: 18px;">
            <strong>✅ Kehadiran berhasil direkam!</strong>
        </p>
    <?php endif; ?>
</div>

<div style="margin-top: 30px;">
    <a href="<?= base_url('admin/kajian/formScan') ?>" style="
    display: inline-block;
    padding: 8px 16px;
    background-color: #28a745;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
">
    ← Kembali ke Scan QR
</a>

</div>
