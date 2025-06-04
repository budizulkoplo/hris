<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan extends BaseController
{
    public function absensi()
    {
        checklogin();
        $bulanTahun = $this->request->getGet('bulanTahun') ?? date('Y-m');

        $tanggalAwal = date('Y-m-26', strtotime("$bulanTahun -1 month"));
        $tanggalAkhir = date('Y-m-25', strtotime("$bulanTahun"));

        $summaryPegawai = $this->generateSummaryAbsensi($tanggalAwal, $tanggalAkhir);

        $data = [
            'title'           => 'Laporan Rekap Absensi ' . date('F Y', strtotime($bulanTahun . '-01')),
            'excelstatus'     => 'excel',
            'printstatus'     => 'print',
            'payrollstatus'   => 'payroll',
            'summaryPegawai'  => $summaryPegawai,
            'bulanTahun'      => $bulanTahun,
            'tanggalAwal'     => $tanggalAwal,
            'tanggalAkhir'    => $tanggalAkhir,
            'content'         => 'admin/laporan/absensi',
        ];

        return view('admin/layout/wrapper', $data);
    }

    public function rekapexport_excel()
    {
        $bulanTahun = $this->request->getGet('bulanTahun') ?? date('Y-m');
        $tanggalAwal = date('Y-m-26', strtotime("$bulanTahun -1 month"));
        $tanggalAkhir = date('Y-m-25', strtotime("$bulanTahun"));

        $summaryPegawai = $this->generateSummaryAbsensi($tanggalAwal, $tanggalAkhir);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Bagian')
            ->setCellValue('C1', 'Nama Pegawai')
            ->setCellValue('D1', 'Jml Absensi')
            ->setCellValue('E1', 'Total Terlambat')
            ->setCellValue('F1', 'Total Lembur')
            ->setCellValue('G1', 'Konversi Lembur')
            ->setCellValue('H1', 'Cuti')
            ->setCellValue('I1', 'Tugas Luar')
            ->setCellValue('J1', 'Total Hari Kerja');

        $row = 2;
        $no = 1;
        foreach ($summaryPegawai as $pegawai) {
            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $pegawai['bagian'])
                ->setCellValue('C' . $row, $pegawai['pegawai_nama'])
                ->setCellValue('D' . $row, $pegawai['total_hari_kerja'])
                ->setCellValue('E' . $row, $pegawai['total_terlambat_formatted'])
                ->setCellValue('F' . $row, $pegawai['total_lembur_formatted'])
                ->setCellValue('G' . $row, floor($pegawai['konversilembur']))
                ->setCellValue('H' . $row, $pegawai['total_cuti'])
                ->setCellValue('I' . $row, $pegawai['total_tugas_luar'])
                ->setCellValue('J' . $row, $pegawai['total_hari_kerja'] + $pegawai['total_tugas_luar'] + $pegawai['total_cuti'] + floor($pegawai['konversilembur']));
            $row++;
        }

        $filename = 'Laporan_Absensi_' . $bulanTahun . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function generateSummaryAbsensi($tanggalAwal, $tanggalAkhir)
    {
        $db = \Config\Database::connect();
        $query = $db->query("CALL spRptRekapAbsensi(" . $db->escape($tanggalAwal) . ", " . $db->escape($tanggalAkhir) . ")");
        $dataAbsensi = $query->getResultArray();

        $summaryPegawai = [];

        foreach ($dataAbsensi as $row) {
            $pin = $row['pegawai_pin'];

            if (!isset($summaryPegawai[$pin])) {
                $summaryPegawai[$pin] = [
                    'pegawai_nama' => $row['pegawai_nama'],
                    'bagian' => $row['bagian'],
                    'total_hari_kerja' => 0,
                    'total_terlambat' => 0,
                    'total_lembur' => 0,
                    'konversilembur' => 0,
                    'total_cuti' => 0,
                    'total_tugas_luar' => 0
                ];
            }

            $isTugasLuar = !empty($row['status_khusus']) && (
                stripos($row['status_khusus'], 'tugas luar') !== false ||
                stripos($row['status_khusus'], 'dinas luar') !== false
            );

            if ((!empty($row['jam_masuk']) || !empty($row['jam_pulang'])) && !$isTugasLuar) {
                $summaryPegawai[$pin]['total_hari_kerja']++;
            }

            if (empty($row['status_khusus'])) {
                if (!empty($row['jam_masuk']) && !empty($row['jam_masuk_shift'])) {
                    $masuk = strtotime($row['jam_masuk']);
                    $masukShift = strtotime($row['jam_masuk_shift']);
                    if ($masuk > $masukShift) {
                        $summaryPegawai[$pin]['total_terlambat'] += $masuk - $masukShift;
                    }
                }
            }

            if (!empty($row['alasan_lembur']) && !empty($row['lembur_masuk']) && !empty($row['lembur_pulang'])) {
                try {
                    $masuk = \DateTime::createFromFormat('H:i:s', $row['lembur_masuk']);
                    $pulang = \DateTime::createFromFormat('H:i:s', $row['lembur_pulang']);
                    if ($masuk && $pulang) {
                        if ($pulang < $masuk) {
                            $pulang->add(new \DateInterval('P1D'));
                        }
                        $interval = $masuk->diff($pulang);
                        $detik = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
                        $jam = $detik / 3600;
                        $konversi = $jam / 7;

                        if ($detik > 0 && $detik <= 57600) {
                            $summaryPegawai[$pin]['total_lembur'] += $detik;
                            $summaryPegawai[$pin]['konversilembur'] += $konversi;
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore parsing error
                }
            }

            if (!empty($row['status_khusus']) && strtolower($row['status_khusus']) === 'cuti') {
                $summaryPegawai[$pin]['total_cuti']++;
            }

            if ($isTugasLuar) {
                $summaryPegawai[$pin]['total_tugas_luar']++;
            }
        }

        // Format jam lembur & terlambat ke waktu
        foreach ($summaryPegawai as &$pegawai) {
            $pegawai['total_terlambat_formatted'] = $this->secondsToTime($pegawai['total_terlambat']);
            $pegawai['total_lembur_formatted'] = $this->secondsToTime($pegawai['total_lembur']);
        }

        return $summaryPegawai;
    }

    private function secondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    public function exportPayroll()
{
    $bulanTahun = $this->request->getPost('bulanTahun') ?? date('Y-m');
    $tanggalAwal = date('Y-m-26', strtotime("$bulanTahun -1 month"));
    $tanggalAkhir = date('Y-m-25', strtotime("$bulanTahun"));

    $summaryPegawai = $this->generateSummaryAbsensi($tanggalAwal, $tanggalAkhir);
    $db = \Config\Database::connect();

    // Hapus dulu data penggajian untuk periode ini
    $db->table('penggajian')->where('periode', $bulanTahun)->delete();

    // Insert ulang data penggajian
    foreach ($summaryPegawai as $pin => $pegawai) {
        $db->table('penggajian')->insert([
            'periode'         => $bulanTahun,
            'pegawai_pin'     => $pin,
            'jmlabsensi'      => $pegawai['total_hari_kerja'],
            'jmlterlambat'    => $pegawai['total_terlambat'], // pastikan format 'H:i:s'
            'konversilembur'  => floor($pegawai['konversilembur']),
            'cuti'            => $pegawai['total_cuti'],
            'tugasluar'       => $pegawai['total_tugas_luar'],
            'totalharikerja'  => $pegawai['total_hari_kerja'] + $pegawai['total_tugas_luar'] + $pegawai['total_cuti'] + floor($pegawai['konversilembur']),
        ]);
    }

    return redirect()->back()->with('success', 'Data penggajian berhasil di-refresh dan diekspor ke tabel.');
}


}
