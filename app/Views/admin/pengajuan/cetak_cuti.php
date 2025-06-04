<!DOCTYPE html>
<html>
<head>
    <title>Surat Cuti Pegawai</title>
</head>
<body>
    <h2 style="text-align:center;">Surat Izin Cuti</h2>
    <p>Dengan ini menyatakan bahwa:</p>
    <p>Nama: <?= esc($cuti['pegawai_nama']); ?></p>
    <p>Tanggal Cuti: <?= date('d-m-Y', strtotime($cuti['tglcuti'])); ?></p>
    <p>Alasan: <?= esc($cuti['alasancuti']); ?></p>
    <p>Telah mengajukan cuti pada tanggal tersebut.</p>
    <p>Hormat kami,</p>
    <p>( HRD )</p>
    <script>window.print();</script>
</body>
</html>
