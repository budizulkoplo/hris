<style>
@media print {
    @page {
        size: 40mm auto; /* Lebar 8cm, tinggi menyesuaikan isi */
        margin: 5mm;     /* Margin tipis */
    }

    body {
            font-family: sans-serif;
            font-size: 11px;
            width: 100%;
            max-width: 7.5cm;
            margin: auto;
            padding: 5px;
        }

    .header img {
        width: 40px;
        height: auto;
    }

    h4, h5 {
        margin: 2px 0;
        font-size: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td, th {
        padding: 2px 4px;
        border: none;
        font-size: 10px;
    }

    hr {
        margin: 4px 0;
        border-top: 1px solid #000;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }
}
</style>


<?php
    $totalPenghasilan =
        $rekap['gajipokok'] +
        $rekap['tunjstruktural'] +
        $rekap['tunjkeluarga'] +
        $rekap['tunjapotek'] +
        $rekap['tunjfungsional'] +
        (($rekap['jmlrujukan'] ?? 0) * $rekap['rujukan']) +
        (($rekap['totalharikerja'] ?? 0) * $rekap['uangmakan']) +
        ($rekap['jmlabsensi'] * $rekap['kehadiran']) +
        ($rekap['tugasluar'] * $rekap['kehadiran']) +
        ($rekap['konversilembur'] * $rekap['kehadiran']);
?>

<?php
    $bpjs = ($totalPenghasilan > 4000000) ? 40000 : 30000;
    $zis = round($totalPenghasilan * 0.025);
    $infaqPdm = round($rekap['gajipokok'] * 0.01);
    $totalPotongan = $zis + ($rekap['pph21'] ?? 0) + ($rekap['qurban'] ?? 0) +
                     ($rekap['potransport'] ?? 0) + $infaqPdm + $bpjs +
                     ($rekap['bpjstk'] ?? 0) + ($rekap['koperasi'] ?? 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { width: 80px; height: 80px; }
        .section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 5px; border: 1px solid #ccc; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
    </style>
</head>
    <body>
    <div class="header" style="display: flex; align-items: center; justify-content: center; gap: 20px;">
        <img src="<?= base_url('../../../assets/upload/image/'.$site['icon']) ?>" alt="<?= $site['namaweb'] ?>" style="width: 80px; height: 80px;">
        <div>
            <h2 style="margin: 0;"><?= $site['namaweb'] ?></h2>
            <p style="margin: 0;">Jalan Raya Boja Limbangan, Salamsari, Kec. Boja, Kabupaten Kendal, Jawa Tengah 51381</p>
            <h3 style="margin-top: 10px;">Slip Gaji Periode: <?= date('F Y', strtotime($periode.'-01')) ?></h3>
        </div>
    </div>
    <hr>
    <div class="section">
        <strong>NIP:</strong> <?= esc($rekap['pegawai_nip']) ?><br>
        <strong>Nama Pegawai:</strong> <?= esc($rekap['pegawai_nama']) ?><br>
        <strong>JABATAN:</strong> <?= esc($rekap['jabatan']) ?>
    </div>

    <div class="section">
        <h4>PENGHASILAN</h4>
        <table>
            <tr><th>RINCIAN</th><th>BESARAN</th><th>JUMLAH</th></tr>
            <tr><td>Gaji Pokok</td><td></td><td class="text-end">Rp <?= number_format($rekap['gajipokok'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Jabatan</td><td></td><td class="text-end">Rp <?= number_format($rekap['tunjstruktural'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Keluarga</td><td></td><td class="text-end">Rp <?= number_format($rekap['tunjkeluarga'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Profesi</td><td></td><td class="text-end">Rp <?= number_format($rekap['tunjapotek'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Fungsional</td><td></td><td class="text-end">Rp <?= number_format($rekap['tunjfungsional'], 0, ',', '.') ?></td></tr>
            <tr><td>Tunj. Rujukan</td><td></td><td class="text-end">Rp <?= number_format(($rekap['jmlrujukan'] ?? 0) * $rekap['rujukan'], 0, ',', '.') ?></td></tr>

            <?php if (($rekap['totalharikerja'] ?? 0) > 0): ?>
                <tr>
                    <td>Uang Makan</td>
                    <td align='center'><?= number_format($rekap['totalharikerja'] ?? 0) ?> x Rp <?= number_format($rekap['uangmakan'], 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format(($rekap['totalharikerja'] ?? 0) * $rekap['uangmakan'], 0, ',', '.') ?></td>
                </tr>
            <?php else: ?>
                <tr><td>Uang Makan</td><td></td><td class="text-end">Rp 0</td></tr>
            <?php endif; ?>

            <?php if (($rekap['jmlabsensi'] ?? 0) > 0): ?>
                <tr>
                    <td>Kehadiran</td>
                    <td align='center'><?= number_format($rekap['jmlabsensi'] ?? 0) ?> x Rp <?= number_format($rekap['kehadiran'], 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format(($rekap['jmlabsensi'] ?? 0) * $rekap['kehadiran'], 0, ',', '.') ?></td>
                </tr>
            <?php else: ?>
                <tr><td>Kehadiran</td><td></td><td class="text-end">Rp 0</td></tr>
            <?php endif; ?>

            <?php if (($rekap['tugasluar'] ?? 0) > 0): ?>
                <tr>
                    <td>Tugas Luar</td>
                    <td align='center'><?= number_format($rekap['tugasluar'] ?? 0) ?> x Rp <?= number_format($rekap['kehadiran'], 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format(($rekap['tugasluar'] ?? 0) * $rekap['kehadiran'], 0, ',', '.') ?></td>
                </tr>
            <?php else: ?>
                <tr><td>Tugas Luar</td><td></td><td class="text-end">Rp 0</td></tr>
            <?php endif; ?>

            <?php if (($rekap['konversilembur'] ?? 0) > 0): ?>
                <tr>
                    <td>Lembur</td>
                    <td align='center'><?= number_format($rekap['konversilembur'] ?? 0) ?> x Rp <?= number_format($rekap['kehadiran'], 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format(($rekap['konversilembur'] ?? 0) * $rekap['kehadiran'], 0, ',', '.') ?></td>
                </tr>
            <?php else: ?>
                <tr><td>Lembur</td><td></td><td class="text-end">Rp 0</td></tr>
            <?php endif; ?>

            <tr><th colspan='2' class="text-start">Total Penghasilan</th><th class="text-end">Rp <?= number_format($totalPenghasilan, 0, ',', '.') ?></th></tr>
        </table>



    </div>

    <div class="section">
        <h4>POTONGAN</h4>
        <table>
            <?php
                $jumlah = $rekap['gajipokok'] + $rekap['tunjstruktural'] + $rekap['tunjkeluarga'] + $rekap['tunjfungsional'] + $rekap['tunjapotek'] +
                          (($rekap['jmlrujukan'] ?? 0) * $rekap['rujukan']) + (($rekap['totalharikerja'] ?? 0) * $rekap['uangmakan']) +
                          ($rekap['jmlabsensi'] * $rekap['kehadiran']) + ($rekap['tugasluar'] * $rekap['kehadiran']) + ($rekap['konversilembur'] * $rekap['kehadiran']);
                $bpjs = ($jumlah > 4000000) ? 40000 : 30000;
                $zis = round($jumlah * 0.025);
                $infaqPdm = round($rekap['gajipokok'] * 0.01);
            ?>
            <tr><th>RINCIAN</th><th>BESARAN</th><th>JUMLAH</th></tr>
            <tr><td>ZIS</td><td align='center'>2.5%</td><td class="text-end">Rp <?= number_format($zis, 0, ',', '.') ?></td></tr>
            <tr><td>PPH21</td><td></td><td class="text-end">Rp <?= number_format($rekap['pph21'] ?? 0, 0, ',', '.') ?></td></tr>
            <tr><td>Qurban</td><td></td><td class="text-end">Rp <?= number_format($rekap['qurban'] ?? 0, 0, ',', '.') ?></td></tr>
            <tr><td>Potongan Transport</td><td></td><td class="text-end">Rp <?= number_format($rekap['potransport'] ?? 0, 0, ',', '.') ?></td></tr>
            <tr><td>Infaq PDM</td><td align='center'>1%</td><td class="text-end">Rp <?= number_format($infaqPdm, 0, ',', '.') ?></td></tr>
            <tr><td>BPJS Kes</td><td></td><td class="text-end">Rp <?= number_format($bpjs, 0, ',', '.') ?></td></tr>
            <tr><td>BPJS TK</td><td></td><td class="text-end">Rp <?= number_format($rekap['bpjstk'] ?? 0, 0, ',', '.') ?></td></tr>
            <tr><td>Koperasi</td><td></td><td class="text-end">Rp <?= number_format($rekap['koperasi'], 0, ',', '.') ?></td></tr>
            <tr><th colspan='2' class="text-start">Total Potongan</th><th class="text-end">Rp <?= number_format($totalPotongan, 0, ',', '.') ?></th></tr>

        </table>
    </div>

    <div class="section">
    <table> 
        <tr>
            <th class="text-start">Penerimaan Bersih Karyawan:</th><th class="text-end"> Rp <?= number_format($totalPenghasilan - $totalPotongan, 0, ',', '.') ?></th>
        </tr>
        <tr>
            <td colspan='2' class="text-start">Semoga berkah.....!</td>
        </tr>
    </table>
    </div>
    <br><br>
<div style="text-align:right;">
    Boja, <?= date('d F Y') ?>
</div>


</body>
</html>
