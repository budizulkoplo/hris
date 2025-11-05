<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            width: 100%;
            max-width: 7.5cm;
            margin: auto;
            padding: 5px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        h5, h4 { margin: 2px 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #666; font-size: 10px; }
        .fw-bold { font-weight: bold; }
        .bg-header {
            background-color: #f4f4f4;
            padding: 3px 5px;
            font-weight: bold;
            margin: 8px 0 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        td, th {
            padding: 2px 0;
            vertical-align: top;
        }
        .label { width: 60%; }
        .value { width: 40%; text-align: right; }
        .total-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 3px;
        }
        .total-potongan {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 3px;
        }
        .highlight {
            font-weight: bold;
            font-size: 13px;
            margin: 5px 0;
        }
        .note { font-size: 10px; margin-top: 3px; }
        img { max-height: 75px; margin-bottom: 2px; }
        p { margin: 5px 0; }
    </style>
</head>
<body>

    <div class="text-center">
        <img src="<?= base_url('assets/upload/image/logo.png') ?>"><br>
        Slip Gaji - <?= strftime('%B %Y', strtotime($periode.'-01')) ?>
    </div>

    <p>
        <strong>Nama:</strong> <?= esc($rekap['pegawai_nama']) ?><br>
        <strong>NIP:</strong> <?= esc($rekap['pegawai_nip']) ?><br>
        <strong>Jabatan:</strong> <?= esc($rekap['jabatan']) ?>
    </p>

    <?php
       $lemburVal = (!empty($rekap['lemburkhusus']) && $rekap['lemburkhusus'] > 0) 
             ? ($rekap['konversilembur'] ?? 0) * ($rekap['lemburkhusus'] ?? 0)
             : ($rekap['konversilembur'] ?? 0) * ($rekap['kehadiran'] ?? 0);

        $totalPenghasilan =
            $rekap['gajipokok'] +
            $rekap['tunjstruktural'] +
            $rekap['tunjkeluarga'] +
            $rekap['tunjfungsional'] +
            (($rekap['jmlrujukan'] ?? 0) * ($rekap['rujukan'] ?? 0)) +
            (($rekap['totalharikerja'] ?? 0) * ($rekap['uangmakan'] ?? 0)) +
            (($rekap['jmlabsensi'] ?? 0) * ($rekap['kehadiran'] ?? 0)) +
            (($rekap['cuti'] ?? 0) * ($rekap['kehadiran'] ?? 0)) +
            (($rekap['tugasluar'] ?? 0) * ($rekap['kehadiran'] ?? 0)) +
            $lemburVal;

        $bpjs      = ($totalPenghasilan > 4000000) ? 40000 : 28000;
        $zis       = round($totalPenghasilan * 0.025);
        
        // Potongan dari tabel potongan
        $potongan_tambahan = 
            ($rekap['leasing_kendaraan'] ?? 0) + 
            ($rekap['iuran_amal_soleh'] ?? 0) + 
            ($rekap['simpanan_pokok'] ?? 0) + 
            ($rekap['simpanan_wajib'] ?? 0) + 
            ($rekap['simpanan_hari_raya'] ?? 0) + 
            ($rekap['simpanan_gerakan_menabung'] ?? 0) +
            ($rekap['angsuran_koperasi'] ?? 0) + 
            ($rekap['belanja_koperasi_tdm'] ?? 0) +
            ($rekap['simpanan_dplk_bni'] ?? 0) +
            ($rekap['angsuran_bri'] ?? 0) +
            ($rekap['angsuran_bank_jateng'] ?? 0) +
            ($rekap['angsuran_darmawanita'] ?? 0) +
            ($rekap['arisan_darmawanita'] ?? 0) +
            ($rekap['tabungan_darmawanita'] ?? 0) +
            ($rekap['lain_lain'] ?? 0);

        $totalPotongan = $zis + 
                        $bpjs +
                        ($rekap['bpjstk'] ?? 0) + 
                        ($rekap['koperasi'] ?? 0) + 
                        $potongan_tambahan;
        
        $netto = $totalPenghasilan - $totalPotongan;
    ?>

    <div class="bg-header">Penghasilan</div>
    <table>
        <tr><td class="label">Gaji Pokok</td><td class="value">Rp <?= number_format($rekap['gajipokok'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><td class="label">Tunj. Struktural</td><td class="value">Rp <?= number_format($rekap['tunjstruktural'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><td class="label">Tunj. Keluarga</td><td class="value">Rp <?= number_format($rekap['tunjkeluarga'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><td class="label">Tunj. Fungsional</td><td class="value">Rp <?= number_format($rekap['tunjfungsional'] ?? 0, 0, ',', '.') ?></td></tr>

        <?php if(($rekap['jmlrujukan'] ?? 0) > 0): ?>
        <tr>
            <td class="label">Tunj. Rujukan <?= $rekap['jmlrujukan'] ?> x Rp <?= number_format($rekap['rujukan'] ?? 0, 0, ',', '.') ?></td>
            <td class="value">Rp <?= number_format(($rekap['jmlrujukan'] ?? 0) * ($rekap['rujukan'] ?? 0), 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>

        <tr>
            <td class="label">Uang Makan <?= $rekap['totalharikerja'] ?? 0 ?> x Rp <?= number_format($rekap['uangmakan'] ?? 0, 0, ',', '.') ?></td>
            <td class="value">Rp <?= number_format(($rekap['totalharikerja'] ?? 0) * ($rekap['uangmakan'] ?? 0), 0, ',', '.') ?></td>
        </tr>

        <tr>
            <td class="label">Kehadiran <?= $rekap['jmlabsensi'] ?? 0 ?> x Rp <?= number_format($rekap['kehadiran'] ?? 0, 0, ',', '.') ?></td>
            <td class="value">Rp <?= number_format(($rekap['jmlabsensi'] ?? 0) * ($rekap['kehadiran'] ?? 0), 0, ',', '.') ?></td>
        </tr>

        <?php if(($rekap['cuti'] ?? 0) > 0): ?>
        <tr>
            <td class="label">Cuti <?= $rekap['cuti'] ?> x Rp <?= number_format($rekap['kehadiran'] ?? 0, 0, ',', '.') ?></td>
            <td class="value">Rp <?= number_format(($rekap['cuti'] ?? 0) * ($rekap['kehadiran'] ?? 0), 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>

        <?php if(($rekap['tugasluar'] ?? 0) > 0): ?>
        <tr>
            <td class="label">Tugas Luar <?= $rekap['tugasluar'] ?> x Rp <?= number_format($rekap['kehadiran'] ?? 0, 0, ',', '.') ?></td>
            <td class="value">Rp <?= number_format(($rekap['tugasluar'] ?? 0) * ($rekap['kehadiran'] ?? 0), 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>

        <?php if(($rekap['konversilembur'] ?? 0) > 0): ?>
        <tr>
            <td class="label">Lembur <?= $rekap['konversilembur'] ?> x Rp <?= number_format(!empty($rekap['lemburkhusus']) && $rekap['lemburkhusus'] > 0 ? $rekap['lemburkhusus'] : $rekap['kehadiran'], 0, ',', '.') ?></td>
            <td class="value">Rp <?= number_format($lemburVal, 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>

        <tr class="total-row">
            <td>Total Penghasilan</td>
            <td class="value">Rp <?= number_format($totalPenghasilan, 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="bg-header">Potongan</div>
    <table>
        <tr><td class="label">ZIS (2.5%)</td><td class="value">Rp <?= number_format($zis, 0, ',', '.') ?></td></tr>
        <tr><td class="label">BPJS Kes</td><td class="value">Rp <?= number_format($bpjs, 0, ',', '.') ?></td></tr>
        <tr><td class="label">BPJS TK</td><td class="value">Rp <?= number_format($rekap['bpjstk'] ?? 0, 0, ',', '.') ?></td></tr>
        <tr><td class="label">Koperasi</td><td class="value">Rp <?= number_format($rekap['koperasi'] ?? 0, 0, ',', '.') ?></td></tr>
        
        <!-- Semua potongan dari tabel potongan -->
        <?php if(($rekap['leasing_kendaraan'] ?? 0) > 0): ?>
        <tr><td class="label">Leasing Kendaraan</td><td class="value">Rp <?= number_format($rekap['leasing_kendaraan'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['iuran_amal_soleh'] ?? 0) > 0): ?>
        <tr><td class="label">Iuran Amal Soleh</td><td class="value">Rp <?= number_format($rekap['iuran_amal_soleh'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['simpanan_pokok'] ?? 0) > 0): ?>
        <tr><td class="label">Simpanan Pokok</td><td class="value">Rp <?= number_format($rekap['simpanan_pokok'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['simpanan_wajib'] ?? 0) > 0): ?>
        <tr><td class="label">Simpanan Wajib</td><td class="value">Rp <?= number_format($rekap['simpanan_wajib'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['simpanan_hari_raya'] ?? 0) > 0): ?>
        <tr><td class="label">Simpanan Hari Raya</td><td class="value">Rp <?= number_format($rekap['simpanan_hari_raya'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['simpanan_gerakan_menabung'] ?? 0) > 0): ?>
        <tr><td class="label">Simpanan Gerakan Menabung</td><td class="value">Rp <?= number_format($rekap['simpanan_gerakan_menabung'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['angsuran_koperasi'] ?? 0) > 0): ?>
        <tr><td class="label">Angsuran Koperasi</td><td class="value">Rp <?= number_format($rekap['angsuran_koperasi'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['belanja_koperasi_tdm'] ?? 0) > 0): ?>
        <tr><td class="label">Belanja Koperasi TDM</td><td class="value">Rp <?= number_format($rekap['belanja_koperasi_tdm'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['simpanan_dplk_bni'] ?? 0) > 0): ?>
        <tr><td class="label">Simpanan DPLK BNI</td><td class="value">Rp <?= number_format($rekap['simpanan_dplk_bni'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['angsuran_bri'] ?? 0) > 0): ?>
        <tr><td class="label">Angsuran BRI</td><td class="value">Rp <?= number_format($rekap['angsuran_bri'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['angsuran_bank_jateng'] ?? 0) > 0): ?>
        <tr><td class="label">Angsuran Bank Jateng</td><td class="value">Rp <?= number_format($rekap['angsuran_bank_jateng'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['angsuran_darmawanita'] ?? 0) > 0): ?>
        <tr><td class="label">Angsuran Darmawanita</td><td class="value">Rp <?= number_format($rekap['angsuran_darmawanita'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['arisan_darmawanita'] ?? 0) > 0): ?>
        <tr><td class="label">Arisan Darmawanita</td><td class="value">Rp <?= number_format($rekap['arisan_darmawanita'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['tabungan_darmawanita'] ?? 0) > 0): ?>
        <tr><td class="label">Tabungan Darmawanita</td><td class="value">Rp <?= number_format($rekap['tabungan_darmawanita'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <?php if(($rekap['lain_lain'] ?? 0) > 0): ?>
        <tr><td class="label">Lain-lain</td><td class="value">Rp <?= number_format($rekap['lain_lain'] ?? 0, 0, ',', '.') ?></td></tr>
        <?php endif; ?>
        
        <tr class="total-potongan">
            <td>Total Potongan</td>
            <td class="value">Rp <?= number_format($totalPotongan, 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="text-center">
        <div class="highlight">Total Diterima</div>
        <div class="highlight">Rp <?= number_format($netto, 0, ',', '.') ?></div>
        <div class="note">Semoga Berkah!</div>
    </div>

    <div class="text-right text-muted">
        <small>Kendal, <?= strftime('%d %B %Y') ?></small>
    </div>

</body>
</html>