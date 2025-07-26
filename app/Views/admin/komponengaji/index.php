<form action="<?= base_url('admin/komponengaji') ?>" method="get" class="mb-4">
    <div class="row g-2">
        <div class="col-md-4 col-sm-6">
            <label for="periode" class="form-label fw-bold">Pilih Bulan:</label>
            <div class="input-group">
                <input type="month" class="form-control" name="periode" id="periode"
                    value="<?= esc($periode ?? date('Y-m')) ?>">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </div>
    </div>
</form>

<?php if (empty($rekap)): ?>
    <div class="alert alert-info">
        Data absensi bulan <strong><?= esc($periode) ?></strong> belum di-export dari HRIS.
    </div>
<?php else: ?>
    <div class="table-responsive" style="overflow-x: auto;">
        <table id="tabelRekap" class="table table-bordered table-striped nowrap" style="width: 100%;">
        <thead class="text-center">
    <tr>
        <th rowspan="2">Nama Pegawai</th>
        <th colspan="19" class="text-center">GAJI</th>
        <th colspan="9" class="text-center">POTONGAN</th>
        <th rowspan="2">Grand Total</th>
    </tr>
    <tr>
        <!-- <th>Rujukan</th> -->
        <th>Gaji Pokok</th>
        <th>Tunj. Struktural</th>
        <th>Tunj. Fungsional</th>
        <th>Tunj. Keluarga</th>
        <th>Tunj. Apotek</th>
        <th>Absensi</th>
        <th>Terlambat</th>
        <th>Jml Lembur</th>
        <th>Cuti</th>
        <th>Jml. Tugas Luar</th>
        <th>Double Shift</th>
        <th>Total Hari Kerja</th>
        <th>Jml Rujukan</th>
        <th>Tunj. Rujukan</th>
        <th>Uang Makan</th>
        <th>Kehadiran</th>
        <th>Tugas Luar</th>
        <th>Lembur</th>
        <th>Jumlah</th>
        <th>ZIS</th>
        <th>PPH21</th>
        <th>Qurban</th>
        <th>Potongan Transport</th>
        <th>Infaq PDM</th>
        <th>BPJS Kes</th>
        <th>BPJS Tk</th>
        <th>Koperasi</th>
        <th>Jml Potongan</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($rekap as $row): ?>
        <?php
            $jmlrujukan = $row['jmlrujukan'] ?? 0;
            $tunjRujukan = $jmlrujukan * $row['rujukan'];
            $totalHariKerja = $row['totalharikerja'] ?? 0;
            $konversiLembur = $row['konversilembur'] ?? 0;
            $kehadiranNominal = $row['kehadiran'] ?? 0;

            $uangMakan = $totalHariKerja * $row['uangmakan'];
            $kehadiranVal = $row['totalharikerja'] * $kehadiranNominal;
            $tugasluarval = $row['tugasluar'] * $kehadiranNominal;
            $lemburVal = $konversiLembur > 0 ? $konversiLembur * $kehadiranNominal : 0;

            $jumlah = $row['gajipokok'] + $row['tunjstruktural'] + $row['tunjkeluarga'] + $row['tunjfungsional'] + $row['tunjapotek'] + $tunjRujukan + $uangMakan + $kehadiranVal + $tugasluarval + $lemburVal;
            $bpjs = ($jumlah > 4000000) ? 40000 : 30000;

            // ZIS 2.5%, Infaq PDM 1% dari jumlah
            $zis = round($jumlah * 0.025);
            $infaqPdm = round($jumlah * 0.01);
            $potongan=$zis+$bpjs+$infaqPdm+$row['koperasi'];
            $grandtotal=$jumlah-$potongan;
        ?>
        <tr>
            <td><?= esc($row['pegawai_nama']) ?></td>

            <td class="text-end" data-order="<?= $row['gajipokok'] ?>">Rp. <?= number_format($row['gajipokok'], 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['tunjstruktural'] ?>">Rp. <?= number_format($row['tunjstruktural'], 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['tunjfungsional'] ?>">Rp. <?= number_format($row['tunjfungsional'], 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['tunjkeluarga'] ?>">Rp. <?= number_format($row['tunjkeluarga'], 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['tunjapotek'] ?>">Rp. <?= number_format($row['tunjapotek'], 0, ',', '.') ?></td>

            <td class="text-center"><?= esc($row['jmlabsensi']) ?></td>
            <td class="text-center"><?= esc($row['jmlterlambat']) ?></td>
            <td class="text-center"><?= esc($konversiLembur) ?></td>
            <td class="text-center"><?= esc($row['cuti']) ?></td>
            <td class="text-center"><?= esc($row['tugasluar']) ?></td>
            <td class="text-center"><?= esc($row['doubleshift']) ?></td>
            <td class="text-center"><?= esc($totalHariKerja) ?></td>
            <td class="text-center"><?= esc($row['jmlrujukan']) ?></td>

            <td class="text-end" data-order="<?= $tunjRujukan ?>">Rp. <?= number_format($tunjRujukan, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $uangMakan ?>">Rp. <?= number_format($uangMakan, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $kehadiranVal ?>">Rp. <?= number_format($kehadiranVal, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $tugasluarval ?>">Rp. <?= number_format($tugasluarval, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $lemburVal ?>">Rp. <?= number_format($lemburVal, 0, ',', '.') ?></td>

            <td class="text-end" data-order="<?= $jumlah ?>">Rp. <?= number_format($jumlah, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $zis ?>">Rp. <?= number_format($zis, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['pph21'] ?? 0 ?>">Rp. <?= number_format($row['pph21'] ?? 0, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['qurban'] ?? 0 ?>">Rp. <?= number_format($row['qurban'] ?? 0, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['potransport'] ?? 0 ?>">Rp. <?= number_format($row['potransport'] ?? 0, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $infaqPdm ?>">Rp. <?= number_format($infaqPdm, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $bpjs ?? 0 ?>">Rp. <?= number_format($bpjs ?? 0, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['bpjstk'] ?? 0 ?>">Rp. <?= number_format($row['bpjstk'] ?? 0, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $row['koperasi'] ?>">Rp. <?= number_format($row['koperasi'], 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $potongan ?>">Rp. <?= number_format($potongan, 0, ',', '.') ?></td>
            <td class="text-end" data-order="<?= $grandtotal ?>">Rp. <?= number_format($grandtotal, 0, ',', '.') ?></td>

        </tr>
    <?php endforeach; ?>
</tbody>


        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#tabelRekap').DataTable({
                scrollX: true,
                pageLength: 100
            });

            $('.rujukan-input').on('input', function() {
                const pin = $(this).data('pin');
                const periode = '<?= esc($periode) ?>';
                const jmlrujukan = $(this).val();

                if (jmlrujukan === '') return;

                $.ajax({
                    url: '<?= base_url('admin/komponengaji/updaterujukan') ?>',
                    method: 'POST',
                    data: {
                        pegawai_pin: pin,
                        periode: periode,
                        jmlrujukan: jmlrujukan
                    },
                    success: function(response) {
                        console.log('Update berhasil');
                    },
                    error: function() {
                        alert('Gagal mengupdate rujukan');
                    }
                });
            });
        });
    </script>

    <script>
        document.getElementById("exportExcel").addEventListener("click", function () {
            const periode = "<?= esc($periode) ?>"; // ambil dari PHP
            const url = "<?= base_url('admin/komponengaji/exportexcel') ?>?periode=" + encodeURIComponent(periode);
            window.open(url, "_blank"); // buka di tab baru
        });
    </script>

<?php endif; ?>
