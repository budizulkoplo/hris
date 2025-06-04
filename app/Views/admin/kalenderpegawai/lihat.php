<h4><?= esc($pegawai['pegawai_nama']); ?> (<?= esc($pegawai['pegawai_pin']); ?>)</h4>
<p>Periode: <?= date('d M Y', strtotime($tanggalAwal)); ?> s/d <?= date('d M Y', strtotime($tanggalAkhir)); ?></p>

<?php if (empty($jadwal)): ?>
    <p>Tidak ada data jadwal/absensi tersedia.</p>
<?php else: ?>
    <div class="table-responsive">
        <table id="kalenderTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Hari</th>
                    <th class="text-center">Jadwal</th>
                    <th class="text-center">Masuk</th>
                    <th class="text-center">Pulang</th>
                    <th class="text-center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $periode = new DatePeriod(
                    new DateTime($tanggalAwal),
                    new DateInterval('P1D'),
                    (new DateTime($tanggalAkhir))->modify('+1 day')
                );

                foreach ($periode as $tanggal):
                    $tgl = $tanggal->format('Y-m-d');
                    $hari = $tanggal->format('l');
                    $jadwalHari = $jadwal[$tgl] ?? ['sif' => '-', 'jammasuk' => '-', 'jampulang' => '-'];
                    $absenHari = $absen[$tgl] ?? ['jammasuk' => '-', 'jampulang' => '-'];
                    
                    $keterangan = '';

                    // Cek cuti
                    foreach ($cuti as $c) {
                        if ($c['tanggal_cuti'] === $tgl) {
                            $keterangan = 'Cuti';
                            break;
                        }
                    }

                    // Cek tugas luar
                    foreach ($tugasluar as $tl) {
                        if ($tl['tgltugasluar'] === $tgl) {
                            $keterangan = 'Tugas Luar';
                            break;
                        }
                    }

                    // Cek lembur
                    if (isset($lembur[$tgl])) {
                        $keterangan .= ($keterangan ? '<br>' : '') . 'Lembur: ' . $lembur[$tgl];
                    }

                    // Cek keterlambatan
                    $terlambat = false;
                    if (isset($jadwalHari['jammasuk'], $absenHari['jammasuk']) &&
                        $jadwalHari['jammasuk'] !== '-' && $absenHari['jammasuk'] !== '-') {
                        $terlambat = strtotime($absenHari['jammasuk']) > strtotime($jadwalHari['jammasuk']);
                    }
                ?>
                    <tr>
                        <td><?= $tgl; ?></td>
                        <td><?= $hari; ?></td>
                        <td><?= $jadwalHari['sif']; ?><br><?= $jadwalHari['jammasuk']; ?> - <?= $jadwalHari['jampulang']; ?></td>
                        <td <?= $terlambat ? 'style="color:red;font-weight:bold"' : ''; ?>>
                            <?= $absenHari['jammasuk']; ?>
                        </td>
                        <td><?= $absenHari['jampulang']; ?></td>
                        <td><?= $keterangan ?: '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
    $(document).ready(function () {
        $('#kalenderTable').DataTable({
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            scrollX: true
        });
    });
</script>

