<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0.5cm;
            font-size: 9pt;
            line-height: 1.1;
        }
        .container {
            display: flex;
            width: 100%;
        }
        .column {
            width: 48%;
            padding: 3px;
        }
        .right-column {
            margin-left: 4%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 10pt;
        }
        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            margin-right: 3px;
        }
        .checked {
            background-color: #000;
        }

        .ttd-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            font-size: 8pt;
            margin-top: 5px;
        }
        .ttd-sdi {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            margin-top: 5px;
        }
        .ttd-line, .sdi-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
            padding-top: 3px;
        }
        .sdi-line {
            width: 30%;
        }
        .signature-space {
            height: 40px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }

        .checkbox.checked {
            background-color: #000;
            position: relative;
        }

        .checkbox.checked::after {
            content: '✔';
            color: #fff;
            font-size: 10px;
            position: absolute;
            left: 1px;
            top: -2px;
        }

    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <!-- Kolom Kiri -->
        <div class="column">
            <div class="header">FORMULIR PENGAJUAN CUTI</div>
            <table>
                <tr>
                    <td width="30%">No Surat</td>
                    <td><?= $nosurat ?></td>
                </tr>
            </table>
            
            <table>
                <tr><th colspan="2">DATA PEGAWAI</th></tr>
                <tr>
                    <td width="30%">Nama</td>
                    <td><?= $pegawai['pegawai_nama'] ?></td>
                </tr>
                <tr>
                    <td>No. Induk Pegawai</td>
                    <td><?= $pegawai['nik'] ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td><?= $pegawai['jabatan'] ?></td>
                </tr>
                <tr>
                    <td>Divisi</td>
                    <td><?= $pegawai['bagian'] ?></td>
                </tr>
                <tr>
                    <td>No. Handphone</td>
                    <td><?= $pegawai['nohp'] ?></td>
                </tr>
            </table>
            
            <table>
                <tr><th colspan="2">JENIS CUTI</th></tr>
                <tr>
                    <td>
                        <span class="checkbox <?= $cuti['jeniscuti'] == 'Cuti Tahunan' ? 'checked' : '' ?>"></span> Cuti Tahunan
                    </td>
                    <td>
                        <span class="checkbox <?= $cuti['jeniscuti'] == 'Cuti Sakit' ? 'checked' : '' ?>"></span> Cuti Sakit
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="checkbox <?= $cuti['jeniscuti'] == 'Cuti Tidak Dibayar' ? 'checked' : '' ?>"></span> Cuti Tidak Dibayar
                    </td>
                    <td>
                        <span class="checkbox <?= !in_array($cuti['jeniscuti'], ['Cuti Tahunan','Cuti Sakit','Cuti Tidak Dibayar']) ? 'checked' : '' ?>"></span> <?= $cuti['jeniscuti'] ?>
                    </td>
                </tr>
            </table>

            <table>
                <tr><th colspan="2">PERIODE CUTI</th></tr>
                <tr>
                    <td width="40%">Diajukan Tgl.</td>
                    <td><?= $tgl_cetak ?></td>
                </tr>
                <tr>
                    <td>Tgl. Mulai Cuti</td>
                    <td><?= $tgl_mulai ?></td>
                </tr>
                <tr>
                    <td>Lama Cuti</td>
                    <td><?= $cuti['jml_hari'] ?> hari</td>
                </tr>
                <tr>
                    <td>Tgl. Masuk</td>
                    <td><?= $tgl_selesai ?></td>
                </tr>
            </table>
            
            <table>
                <tr><th>KETERANGAN / ALASAN</th></tr>
                <tr>
                    <td style="height: 40px;"><?= $cuti['alasancuti'] ?></td>
                </tr>
            </table>
            
            <table>
                <tr><th colspan="2">PELIMPAHAN TUGAS & WEWENANG KEPADA :</th></tr>
                <tr>
                    <td width="30%">Nama</td>
                    <td><?= $pengganti['pegawai_nama'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>No. Induk Pegawai</td>
                    <td><?= $pengganti['nik'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td><?= $pengganti['jabatan'] ?? '-' ?></td>
                </tr>
            </table>
            
            <!-- Tanda Tangan Kolom Kiri -->
            <div style="display: flex; justify-content: space-between; ">
                <div class="ttd-box">
                    <div>Diajukan oleh</div>
                    <div class="signature-space"></div>
                    <div class="ttd-line">(<?= $pegawai['pegawai_nama'] ?>)</div>
                    <div><?= $pegawai['jabatan'] ?></div>
                </div>
                
                <div class="ttd-box">
                    <div>Mengetahui</div>
                    <div class="signature-space"></div>
                    <div class="ttd-line">(<?= $pegawai['namakaru'] ?? 'Kepala Ruangan' ?>)</div>
                    <div>KARU</div>
                </div>
            </div>
            
            <div class="ttd-sdi">
                <div>Disetujui oleh</div>
                <div class="signature-space"></div>
                <div class="sdi-line">(<?= $sdi['pegawai_nama'] ?? 'SDI' ?>)</div>
                <div>SDI</div>
            </div>
        </div>
        
        <!-- Kolom Kanan -->
        <div class="column right-column">
            <div class="header">FORMULIR PENGAJUAN CUTI</div>
            <table>
                <tr>
                    <td width="30%">No Surat</td>
                    <td><?= $nosurat ?></td>
                </tr>
            </table>
            
            <table>
                <tr><th colspan="2">DATA PEGAWAI</th></tr>
                <tr>
                    <td width="30%">Nama</td>
                    <td><?= $pegawai['pegawai_nama'] ?></td>
                </tr>
                <tr>
                    <td>No. Induk Pegawai</td>
                    <td><?= $pegawai['nik'] ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td><?= $pegawai['jabatan'] ?></td>
                </tr>
                <tr>
                    <td>Divisi</td>
                    <td><?= $pegawai['bagian'] ?></td>
                </tr>
                <tr>
                    <td>No. Handphone</td>
                    <td><?= $pegawai['nohp'] ?></td>
                </tr>
            </table>
            
            <table>
                <tr><th colspan="2">JENIS CUTI</th></tr>
                <tr>
                    <td>
                        <span class="checkbox <?= $cuti['jeniscuti'] == 'Cuti Tahunan' ? 'checked' : '' ?>"></span> Cuti Tahunan
                    </td>
                    <td>
                        <span class="checkbox <?= $cuti['jeniscuti'] == 'Cuti Sakit' ? 'checked' : '' ?>"></span> Cuti Sakit
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="checkbox <?= $cuti['jeniscuti'] == 'Cuti Tidak Dibayar' ? 'checked' : '' ?>"></span> Cuti Tidak Dibayar
                    </td>
                    <td>
                        <span class="checkbox <?= !in_array($cuti['jeniscuti'], ['Cuti Tahunan','Cuti Sakit','Cuti Tidak Dibayar']) ? 'checked' : '' ?>"></span> <?= $cuti['jeniscuti'] ?>
                    </td>
                </tr>
            </table>

            <table>
                <tr><th colspan="2">PERIODE CUTI</th></tr>
                <tr>
                    <td width="40%">Diajukan Tgl.</td>
                    <td><?= $tgl_cetak ?></td>
                </tr>
                <tr>
                    <td>Tgl. Mulai Cuti</td>
                    <td><?= $tgl_mulai ?></td>
                </tr>
                <tr>
                    <td>Lama Cuti</td>
                    <td><?= $cuti['jml_hari'] ?> hari</td>
                </tr>
                <tr>
                    <td>Tgl. Masuk</td>
                    <td><?= $tgl_selesai ?></td>
                </tr>
            </table>
            
            <table>
                <tr><th>KETERANGAN / ALASAN</th></tr>
                <tr>
                    <td style="height: 40px;"><?= $cuti['alasancuti'] ?></td>
                </tr>
            </table>
            
            <table>
                <tr><th colspan="2">PELIMPAHAN TUGAS & WEWENANG KEPADA :</th></tr>
                <tr>
                    <td width="30%">Nama</td>
                    <td><?= $pengganti['pegawai_nama'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>No. Induk Pegawai</td>
                    <td><?= $pengganti['nik'] ?? '-' ?></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td><?= $pengganti['jabatan'] ?? '-' ?></td>
                </tr>
            </table>
            
            <!-- Tanda Tangan Kolom Kanan -->
            <div style="display: flex; justify-content: space-between; ">
                <div class="ttd-box">
                    <div>Diajukan oleh</div>
                    <div class="signature-space"></div>
                    <div class="ttd-line">(<?= $pegawai['pegawai_nama'] ?>)</div>
                    <div><?= $pegawai['jabatan'] ?></div>
                </div>
                
                <div class="ttd-box">
                    <div>Mengetahui</div>
                    <div class="signature-space"></div>
                    <div class="ttd-line">(<?= $pegawai['namakaru'] ?? 'Kepala Ruangan' ?>)</div>
                    <div>KARU</div>
                </div>
            </div>
            
            <div class="ttd-sdi">
                <div>Disetujui oleh</div>
                <div class="signature-space"></div>
                <div class="sdi-line">(<?= $sdi['pegawai_nama'] ?? 'SDI' ?>)</div>
                <div>SDI</div>
            </div>
        </div>
    </div>
</body>
</html>