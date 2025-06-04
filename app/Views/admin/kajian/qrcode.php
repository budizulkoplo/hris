<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  <!-- jQuery untuk AJAX -->
    <style>
        body { text-align: center; font-family: sans-serif; padding: 30px; }
        #qrcode { margin: 20px auto; }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>

    <h2><?= esc($kajian['namakajian']) ?></h2>
    <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($kajian['tanggal'])) ?></p>
    <p><strong>Lokasi:</strong> <?= esc($kajian['lokasi']) ?></p>

    <div id="qrcode" align="center"></div>
    <p><strong>Scan untuk absen:</strong></p>
    <p style="font-size: 14px; color: blue;">HRIS RS PKU Muhammadiyah Boja</p>

<script>
    // Generate QR Code
    new QRCode(document.getElementById("qrcode"), {
        text: "<?= esc($qr_link) ?>",
        width: 500,
        height: 500
    });

    const idkajian = <?= json_encode($kajian['idkajian']) ?>;
    const barcodeuniq = <?= json_encode($barcode) ?>;

    function cekSudahScan() {
        $.ajax({
            url: `<?= base_url() ?>/admin/kajian/cekScan/${idkajian}/${barcodeuniq}`,
            method: 'GET',
            success: function(response) {
                if (response.sudah_scan) {
                    location.reload();
                }
            }
        });
    }

    setInterval(cekSudahScan, 2000);
</script>


</body>
</html>
