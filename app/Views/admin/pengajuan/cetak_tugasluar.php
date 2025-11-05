<?php
function tgl_indo($tanggal) {
    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $tgl = date('Y-m-d', strtotime($tanggal));
    $hari_indo = $hari[date('l', strtotime($tgl))];
    $tanggal = date('d', strtotime($tgl));
    $bulan_indo = $bulan[(int)date('m', strtotime($tgl))];
    $tahun = date('Y', strtotime($tgl));

    return "$hari_indo, $tanggal $bulan_indo $tahun";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Surat Tugas</title>
    <style>
        body { font-family: Arial, sans-serif; }
        @page {
            size: A4;
            margin: 15mm;
        }
        .header, .footer { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { border: 1px solid #000; padding: 5px; text-align: left; }
        .ttd { margin-top: 50px; width: 100%; }

        .label-cell {
            width: 30%;
            white-space: nowrap;
            vertical-align: top;
            padding: 4px;
        }

        .dot-cell {
            border-bottom: 1px dotted #000;
            width: 100%;
            height: 1.2em;
            padding: 4px;
        }

        .noborder-table {
            width: 100%;
            border-collapse: collapse;
        }

        .noborder-table td {
            border: none;
        }
    </style>
</head>
<body onload="window.print()">

<div class="header" style="display: flex; align-items: center; justify-content: space-between;">
    <!-- Logo kiri -->
    <div class="logo-kiri">
        <img src="<?= base_url('assets/upload/image/logo.png') ?>" alt="Logo RS" style="width: 90px;">
    </div>

    <!-- Teks tengah -->
    <div class="text-tengah" style="text-align: center; flex: 1;">
        <h3 style="margin: 0;">PERUMDA AIR MINUM <br>TIRTO PANGURIPAN</h3>
        <p style="margin: 2px 0; font-size: 12px;">
            Kendal
        </p>

    </div>

</div>
<hr>

<h4 style="text-align:center; margin: 5px 0; padding: 0;"><u>SURAT TUGAS</u></h4>
<p style="text-align:center; margin: 0px 0; padding: 0;"><strong>Nomor:</strong> <?= $no_surat ?? '......'; ?></p>

<p>Yang bertanda tangan di bawah ini:</p>
<table>
    <tr><td><strong>Nama</strong></td><td>: <?= $direktur['pegawai_nama'] ?? '........'; ?></td></tr>
    <tr><td><strong>Jabatan</strong></td><td>: Direktur</td></tr>
</table>

<p>Menugaskan kepada pegawai berikut:</p>
<table>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Jabatan</th>
    </tr>
    <tr>
        <td>1</td>
        <td><?= $pegawai['pegawai_nama'] ?? '......'; ?></td>
        <td><?= $pegawai['jabatan'] ?? '......'; ?></td>
    </tr>
</table>

<p>Untuk menghadiri kegiatan:</p>
<table>
    <tr><td><strong>Kegiatan</strong></td><td>: <?= $tugasluar['alasan'] ?? '......'; ?></td></tr>
    <tr>
    <td><strong>Hari/Tanggal</strong></td>
        <td>: <?= tgl_indo($tugasluar['tgltugasluar'] ?? date('Y-m-d')); ?></td>
    </tr>  
    <tr><td><strong>Jam</strong></td><td>: <?= date('H:i', strtotime($tugasluar['waktu'])) ?? '......'; ?> WIB s.d Selesai</td></tr>
    <tr><td><strong>Tempat</strong></td><td>: <?= $tugasluar['lokasi'] ?? '......'; ?></td></tr>
</table>

<p>Demikian surat tugas ini dibuat untuk dilaksanakan sebagaimana mestinya dan melaporkan kegiatan kepada pimpinan.</p>

<div class="ttd">
    <div style="width:50%; float:right; text-align:center;">
        Kendal, <?= date('d F Y'); ?><br>
        PDAM TIRTO PANGSURIPAN<br>
        Direktur,<br><br><br><br>
        <strong><?= $direktur['pegawai_nama'] ?? '........'; ?></strong><br>
        NIP: <?= $direktur['nip'] ?? '........'; ?>
    </div>
</div>

<div style="clear:both;"></div>

<br>
<table class="noborder-table">
    <tr>
        <td class="label-cell"><strong>Telah tiba di</strong></td>
        <td><div class="dot-cell">:</div></td>
    </tr>
    <tr>
        <td class="label-cell"><strong>Pada tanggal/waktu</strong></td>
        <td><div class="dot-cell">:</div></td>
    </tr>
    <tr>
        <td class="label-cell"><strong>Diterima oleh</strong></td>
        <td><div class="dot-cell">:</div></td>
    </tr>
</table>

</body>
</html>
