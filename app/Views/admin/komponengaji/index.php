<form action="<?= base_url('admin/komponengaji') ?>" method="get" class="form-inline mb-3">
    <div class="form-group">
        <label for="periode" class="control-label fw-bold mr-2">Pilih Bulan:</label>
        <input type="month" name="periode" id="periode"
            class="form-control-sm input-sm mr-2"
            value="<?= esc($periode ?? date('Y-m')) ?>">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="fa fa-search"></i> Tampilkan
        </button>
    </div>
</form>

<?php if (empty($rekap)): ?>
    <div class="alert alert-info">
        Data absensi bulan <strong><?= esc($periode) ?></strong> belum di-export dari HRIS.
    </div>
<?php else: ?>
    <div class="table-responsive" style="overflow-x: auto;">
        <table id="tabelRekap" class="table table-bordered table-striped nowrap table-sm" style="width: 100%; font-size: 0.75rem;">
            <thead class="text-center">
                <tr>
                    <th rowspan="2">Nama Pegawai</th>
                    <th rowspan="2">Slip</th>
                    <th colspan="18" class="text-center">GAJI</th>
                    <th colspan="12" class="text-center">POTONGAN</th>
                    <th rowspan="2">Grand Total</th>
                </tr>
                <tr>
                    <!-- GAJI -->
                    <th>Gaji Pokok</th>
                    <th>Tunj. Struktural</th>
                    <th>Tunj. Fungsional</th>
                    <th>Tunj. Keluarga</th>
                    <th>Absensi</th>
                    <th>Terlambat</th>
                    <th>Jml Lembur</th>
                    <th>Cuti</th>
                    <th>Jml. Tugas Luar</th>
                    <th>Total Hari Kerja</th>
                    <th>Jml Rujukan</th>
                    <th>Tunj. Rujukan</th>
                    <th>Uang Makan</th>
                    <th>Kehadiran</th>
                    <th>Tugas Luar</th>
                    <th>Lembur</th>
                    <th>Cuti</th>
                    <th>Jumlah</th>
                    
                    <!-- POTONGAN -->
                    <th>ZIS</th>
                    <th>BPJS Kes</th>
                    <th>BPJS Tk</th>
                    <th>Koperasi</th>
                    <th>Leasing Kendaraan</th>
                    <th>Iuran Amal Soleh</th>
                    <th>Simpanan Pokok</th>
                    <th>Simpanan Wajib</th>
                    <th>Simpanan Hari Raya</th>
                    <th>Angsuran Koperasi</th>
                    <th>Lain-lain</th>
                    <th>Total Potongan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rekap as $row): ?>
                    <?php
                    // PERHITUNGAN PENGHASILAN
                    $jmlrujukan = $row['jmlrujukan'] ?? 0;
                    $tunjRujukan = $jmlrujukan * ($row['rujukan'] ?? 0);
                    $totalHariKerja = $row['totalharikerja'] ?? 0;
                    $konversiLembur = $row['konversilembur'] ?? 0;
                    $kehadiranNominal = $row['kehadiran'] ?? 0;

                    $uangMakan = $totalHariKerja * ($row['uangmakan'] ?? 0);
                    $kehadiranVal = ($row['jmlabsensi'] ?? 0) * $kehadiranNominal;
                    $tugasluarval = ($row['tugasluar'] ?? 0) * $kehadiranNominal;
                    $cutiVal = ($row['cuti'] ?? 0) * $kehadiranNominal;
                    
                    // Perhitungan lembur dengan opsi lembur khusus
                    $lemburNominal = (!empty($row['lemburkhusus']) && $row['lemburkhusus'] > 0) 
                        ? $row['lemburkhusus'] 
                        : $kehadiranNominal;
                    $lemburVal = $konversiLembur > 0 ? $konversiLembur * $lemburNominal : 0;

                    $jumlah = ($row['gajipokok'] ?? 0) + 
                             ($row['tunjstruktural'] ?? 0) + 
                             ($row['tunjkeluarga'] ?? 0) + 
                             ($row['tunjfungsional'] ?? 0) + 
                             $tunjRujukan + $uangMakan + $kehadiranVal + 
                             $tugasluarval + $lemburVal + $cutiVal;

                    // PERHITUNGAN POTONGAN DARI TABEL POTONGAN
                    $bpjs = ($jumlah > 4000000) ? 40000 : 28000;
                    $zis = round($jumlah * 0.025);
                    
                    // Potongan dari tabel potongan
                    $potongan_tambahan = ($row['leasing_kendaraan'] ?? 0) + 
                                        ($row['iuran_amal_soleh'] ?? 0) + 
                                        ($row['simpanan_pokok'] ?? 0) + 
                                        ($row['simpanan_wajib'] ?? 0) + 
                                        ($row['simpanan_hari_raya'] ?? 0) + 
                                        ($row['angsuran_koperasi'] ?? 0) + 
                                        ($row['lain_lain'] ?? 0);

                    $total_potongan = $zis + 
                                     $bpjs + 
                                     ($row['bpjstk'] ?? 0) + 
                                     ($row['koperasi'] ?? 0) + 
                                     $potongan_tambahan;

                    $grandtotal = $jumlah - $total_potongan;
                    ?>
                    <tr>
                        <!-- DATA PEGAWAI -->
                        <td><?= esc($row['pegawai_nama']) ?></td>
                        <td class="text-center">
                            <a href="<?= base_url('admin/komponengaji/slip/' . $row['pegawai_pin'] . '?periode=' . urlencode($periode)) ?>" 
                            class="btn btn-sm btn-success" target="_blank">
                                <i class="fa fa-download"></i> Slip
                            </a>
                        </td>
                        
                        <!-- PENGHASILAN -->
                        <td class="text-end">Rp. <?= number_format($row['gajipokok'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['tunjstruktural'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['tunjfungsional'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['tunjkeluarga'] ?? 0, 0, ',', '.') ?></td>

                        <td class="text-center"><?= esc($row['jmlabsensi'] ?? 0) ?></td>
                        <td class="text-center"><?= esc($row['jmlterlambat'] ?? 0) ?></td>
                        <td class="text-center"><?= esc($konversiLembur) ?></td>
                        <td class="text-center"><?= esc($row['cuti'] ?? 0) ?></td>
                        <td class="text-center"><?= esc($row['tugasluar'] ?? 0) ?></td>
                        <td class="text-center"><?= esc($totalHariKerja) ?></td>
                        <td class="text-center"><?= esc($jmlrujukan) ?></td>

                        <td class="text-end">Rp. <?= number_format($tunjRujukan, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($uangMakan, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($kehadiranVal, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($tugasluarval, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($lemburVal, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($cutiVal, 0, ',', '.') ?></td>
                        <td class="text-end fw-bold">Rp. <?= number_format($jumlah, 0, ',', '.') ?></td>
                        
                        <!-- POTONGAN -->
                        <td class="text-end">Rp. <?= number_format($zis, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($bpjs, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['bpjstk'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['koperasi'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['leasing_kendaraan'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['iuran_amal_soleh'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['simpanan_pokok'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['simpanan_wajib'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['simpanan_hari_raya'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['angsuran_koperasi'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp. <?= number_format($row['lain_lain'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end fw-bold">Rp. <?= number_format($total_potongan, 0, ',', '.') ?></td>
                        
                        <!-- GRAND TOTAL -->
                        <td class="text-end fw-bold text-primary">Rp. <?= number_format($grandtotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#tabelRekap').DataTable({
                scrollX: true,
                pageLength: 100,
                fixedHeader: true
            });
        });
    </script>
<?php endif; ?>