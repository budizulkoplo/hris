<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Komponengaji_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class Komponengaji extends BaseController
{
    public function index()
    {
        checklogin();

        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $model   = new Komponengaji_model();

        // Ambil data rekap gaji per periode
        $rekap = $model->getRekapGaji($periode);

        // Ambil data rujukan
        $rujukanData = $model->getRujukanData($periode);
        $mapRujukan = [];
        foreach ($rujukanData as $rj) {
            $mapRujukan[$rj['pegawai_pin']] = $rj['jmlrujukan'];
        }

        // Gabungkan data rujukan ke dalam array rekap
        foreach ($rekap as &$row) {
            $row['jmlrujukan'] = $mapRujukan[$row['pegawai_pin']] ?? 0;
        }

        $data = [
            'title'   => 'Komponen Penggajian Pegawai',
            'excelstatus' => 'excel',
            'periode' => $periode,
            'rekap'   => $rekap,
            'content' => 'admin/komponengaji/index',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function detail($pegawai_pin)
    {
        checklogin();

        $model = new Komponengaji_model();
        $periode = $this->request->getGet('periode') ?? date('Y-m');

        $detail = $model->where('pegawai_pin', $pegawai_pin)
                       ->where('periode', $periode)
                       ->first();

        if (!$detail) {
            return redirect()->to('/admin/komponengaji')->with('gagal', 'Data penggajian tidak ditemukan.');
        }

        $data = [
            'title'   => 'Detail Penggajian Pegawai',
            'data'    => $detail,
            'periode' => $periode,
            'content' => 'admin/komponengaji/detail',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function slip($pin)
    {
        checklogin();

        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $model   = new Komponengaji_model();

        // Ambil data gaji pegawai
        $rekapList = $model->getRekapGaji($periode);
        
        // Ambil data rujukan
        $rujukanData = $model->getRujukanData($periode);
        $mapRujukan = [];
        foreach ($rujukanData as $rj) {
            $mapRujukan[$rj['pegawai_pin']] = $rj['jmlrujukan'];
        }

        $rekap = null;
        foreach ($rekapList as $row) {
            if ($row['pegawai_pin'] == $pin) {
                $row['jmlrujukan'] = $mapRujukan[$pin] ?? 0;
                $rekap = $row;
                break;
            }
        }

        if (!$rekap) {
            return redirect()->to(base_url('admin/komponengaji'))
                             ->with('warning', 'Data tidak ditemukan.');
        }

        $site = [
            'icon'    => 'logopku.png',
            'namaweb' => 'RS PKU Muhammadiyah Boja',
        ];

        $html  = '<style>
        @page { margin: 5; }
        html, body {
            margin: 5;
            padding: 0;
        }
        * {
            box-sizing: border-box;
        }
        </style>';
        $html .= view('admin/komponengaji/slip_compact', [
            'rekap'   => $rekap,
            'periode' => $periode,
            'site'    => $site,
        ]);

        // Konfigurasi Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $customPaper = [0, 0, 230, 800]; // point
        $dompdf->setPaper($customPaper, 'portrait');

        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = 'Slip_Gaji_' . $rekap['pegawai_nama'] . '_' . $periode . '.pdf';

        return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'attachment;filename="' . $filename . '"')
                    ->setBody($dompdf->output());
    }

    public function exportexcel()
    {
        checklogin();

        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $model   = new Komponengaji_model();

        $rekap = $model->getRekapGaji($periode);
        
        // Ambil data rujukan
        $rujukanData = $model->getRujukanData($periode);
        $mapRujukan = [];
        foreach ($rujukanData as $rj) {
            $mapRujukan[$rj['pegawai_pin']] = $rj['jmlrujukan'];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $header = [
            'Nama Pegawai', 'Gaji Pokok', 'Tunj. Struktural', 'Tunj. Fungsional', 'Tunj. Keluarga', 'Tunj. Apotek',
            'Absensi', 'Terlambat', 'Lembur', 'Cuti', 'Tugas Luar', 'Double Shift', 'Total Hari Kerja',
            'Jml Rujukan', 'Tunj. Rujukan', 'Uang Makan', 'Kehadiran', 'Tugas Luar Val', 'Lembur Val','Cuti Val','Double Shift Val',
            'Jumlah', 
            // Potongan dari tabel potongan
            'Leasing Kendaraan', 'Iuran Amal Soleh', 'Simpanan Pokok', 'Simpanan Wajib', 'Simpanan Hari Raya',
            'Simpanan Gerakan Menabung', 'Angsuran Koperasi', 'Belanja Koperasi TDM', 'Simpanan DPLK BNI',
            'Angsuran BRI', 'Angsuran Bank Jateng', 'Angsuran Darmawanita', 'Arisan Darmawanita', 
            'Tabungan Darmawanita', 'Lain-lain',
            // Potongan lainnya
            'ZIS', 'PPH21', 'Qurban', 'Pot. Transport', 'Infaq PDM', 'BPJS', 'BPJS TK', 'Koperasi', 
            'Total Potongan', 'Grand Total'
        ];
        $sheet->fromArray($header, null, 'A1');

        $rowIndex = 2;
        foreach ($rekap as $row) {
            // PERHITUNGAN PENGHASILAN
            $jmlrujukan = $mapRujukan[$row['pegawai_pin']] ?? 0;
            $tunjRujukan = $jmlrujukan * ($row['rujukan'] ?? 0);
            $totalHariKerja = $row['totalharikerja'] ?? 0;
            $konversiLembur = $row['konversilembur'] ?? 0;
            $kehadiranNominal = $row['kehadiran'] ?? 0;

            $uangMakan = $totalHariKerja * ($row['uangmakan'] ?? 0);
            $kehadiranVal = ($row['jmlabsensi'] ?? 0) * $kehadiranNominal;
            $tugasluarval = ($row['tugasluar'] ?? 0) * $kehadiranNominal;
            $cutiVal = ($row['cuti'] ?? 0) * $kehadiranNominal;
            $doubleshiftVal = ($row['doubleshift'] ?? 0) * $kehadiranNominal;
            
            // Perhitungan lembur dengan opsi lembur khusus
            $lemburNominal = (!empty($row['lemburkhusus']) && $row['lemburkhusus'] > 0) 
                ? $row['lemburkhusus'] 
                : $kehadiranNominal;
            $lemburVal = $konversiLembur > 0 ? $konversiLembur * $lemburNominal : 0;

            $jumlah = ($row['gajipokok'] ?? 0) + 
                     ($row['tunjstruktural'] ?? 0) + 
                     ($row['tunjkeluarga'] ?? 0) + 
                     ($row['tunjfungsional'] ?? 0) + 
                     ($row['tunjapotek'] ?? 0) + 
                     $tunjRujukan + $uangMakan + $kehadiranVal + 
                     $tugasluarval + $lemburVal + $cutiVal + $doubleshiftVal;

            // PERHITUNGAN POTONGAN
            $bpjs = ($jumlah > 4000000) ? 40000 : 28000;
            $zis = round($jumlah * 0.025);
            $infaqPdm = round(($row['gajipokok'] ?? 0) * 0.01);
            
            // Potongan dari tabel potongan
            $potongan_tambahan = ($row['leasing_kendaraan'] ?? 0) + 
                                ($row['iuran_amal_soleh'] ?? 0) + 
                                ($row['simpanan_pokok'] ?? 0) + 
                                ($row['simpanan_wajib'] ?? 0) + 
                                ($row['simpanan_hari_raya'] ?? 0) + 
                                ($row['simpanan_gerakan_menabung'] ?? 0) +
                                ($row['angsuran_koperasi'] ?? 0) + 
                                ($row['belanja_koperasi_tdm'] ?? 0) +
                                ($row['simpanan_dplk_bni'] ?? 0) +
                                ($row['angsuran_bri'] ?? 0) +
                                ($row['angsuran_bank_jateng'] ?? 0) +
                                ($row['angsuran_darmawanita'] ?? 0) +
                                ($row['arisan_darmawanita'] ?? 0) +
                                ($row['tabungan_darmawanita'] ?? 0) +
                                ($row['lain_lain'] ?? 0);

            $total_potongan = $zis + 
                             ($row['pph21'] ?? 0) + 
                             ($row['qurban'] ?? 0) + 
                             ($row['potransport'] ?? 0) + 
                             $infaqPdm + 
                             $bpjs + 
                             ($row['bpjstk'] ?? 0) + 
                             ($row['koperasi'] ?? 0) + 
                             $potongan_tambahan;

            $grandtotal = $jumlah - $total_potongan;

            $sheet->fromArray([
                // Data dasar
                $row['pegawai_nama'], 
                $row['gajipokok'] ?? 0, 
                $row['tunjstruktural'] ?? 0, 
                $row['tunjfungsional'] ?? 0,
                $row['tunjkeluarga'] ?? 0, 
                $row['tunjapotek'] ?? 0, 
                $row['jmlabsensi'] ?? 0, 
                $row['jmlterlambat'] ?? 0,
                $konversiLembur, 
                $row['cuti'] ?? 0, 
                $row['tugasluar'] ?? 0, 
                $row['doubleshift'] ?? 0, 
                $totalHariKerja,
                $jmlrujukan, 
                $tunjRujukan, 
                $uangMakan, 
                $kehadiranVal, 
                $tugasluarval, 
                $lemburVal, 
                $cutiVal, 
                $doubleshiftVal,
                $jumlah,
                
                // Potongan dari tabel potongan
                $row['leasing_kendaraan'] ?? 0,
                $row['iuran_amal_soleh'] ?? 0,
                $row['simpanan_pokok'] ?? 0,
                $row['simpanan_wajib'] ?? 0,
                $row['simpanan_hari_raya'] ?? 0,
                $row['simpanan_gerakan_menabung'] ?? 0,
                $row['angsuran_koperasi'] ?? 0,
                $row['belanja_koperasi_tdm'] ?? 0,
                $row['simpanan_dplk_bni'] ?? 0,
                $row['angsuran_bri'] ?? 0,
                $row['angsuran_bank_jateng'] ?? 0,
                $row['angsuran_darmawanita'] ?? 0,
                $row['arisan_darmawanita'] ?? 0,
                $row['tabungan_darmawanita'] ?? 0,
                $row['lain_lain'] ?? 0,
                
                // Potongan lainnya
                $zis, 
                $row['pph21'] ?? 0, 
                $row['qurban'] ?? 0, 
                $row['potransport'] ?? 0,
                $infaqPdm, 
                $bpjs, 
                $row['bpjstk'] ?? 0, 
                $row['koperasi'] ?? 0, 
                $total_potongan, 
                $grandtotal
            ], null, 'A' . $rowIndex);

            $rowIndex++;
        }

        // Output as Excel file
        $filename = 'Rekap_Gaji_' . $periode . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}