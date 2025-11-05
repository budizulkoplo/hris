<?php

namespace App\Controllers\Admin;

use App\Models\Absensi_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Absensi extends BaseController
{
    public function index()
    {
        // Validasi login
        checklogin();

        // Ambil parameter bulanTahun dari query string atau gunakan default bulan ini
        $bulanTahun = $this->request->getGet('bulanTahun') ?? date('Y-m');

        // Ambil data absensi dari stored procedure
        $m_absensi = new Absensi_model();
        $dataAbsensi = $m_absensi->getAbsensiByProcedure($bulanTahun);

        // Siapkan data untuk dikirim ke view
        $data = [
            'title'      => 'Data Absensi ' . $bulanTahun,
            'excelstatus' => 'excel',
            'dataAbsensi' => $dataAbsensi,
            'bulanTahun' => $bulanTahun,
            'content'    => 'admin/absensi/index',
        ];

        // Tampilkan tampilan utama
        echo view('admin/layout/wrapper', $data);
    }


    public function exportExcel()
{
    $bulanTahun = $this->request->getGet('bulanTahun') ?? date('Y-m');
    $m_absensi = new Absensi_model();
    $dataAbsensi = $m_absensi->getAbsensiByProcedure($bulanTahun);

    if (empty($dataAbsensi)) {
        return redirect()->to(base_url('admin/absensi'))->with('error', 'Data absensi tidak tersedia.');
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Data Absensi Periode ' . $bulanTahun);
    $colCount = 2 + count(array_filter(array_keys($dataAbsensi[0]), 'is_numeric'));
    $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
    $sheet->mergeCells("A1:{$lastCol}1");
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

    // Header di baris 3
    $header = ['Kelompok', 'Nama Pegawai'];
    foreach (array_keys($dataAbsensi[0]) as $key) {
        if (is_numeric($key)) {
            $header[] = $key;
        }
    }
    $sheet->fromArray($header, NULL, 'A3');
    $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
    $sheet->getStyle("A3:{$lastCol}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Data isi mulai dari baris 4
    $rowNum = 4;
    foreach ($dataAbsensi as $row) {
        $colIndex = 1;

        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++) . $rowNum, $row['bagian']);
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex++) . $rowNum, $row['pegawai_nama']);

        foreach ($row as $key => $value) {
            if (!is_numeric($key)) continue;

            $text = strip_tags(str_replace('<br>', "\n", $value));
            $inOutText = '';

            // Menampilkan hanya jam masuk (IN) dan jam keluar (OUT)
            if (stripos($text, 'IN:') !== false) {
                preg_match('/IN:\s*(\d{2}:\d{2}:\d{2})/', $text, $match);
                $jamIn = $match[1] ?? '';
                $inOutText .= "IN: " . $jamIn . "\n";
            }

            if (stripos($text, 'OUT:') !== false) {
                preg_match('/OUT:\s*(\d{2}:\d{2}:\d{2})/', $text, $match);
                $jamOut = $match[1] ?? '';
                $inOutText .= "OUT: " . $jamOut . "\n";
            }

            $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $rowNum;
            $sheet->setCellValue($cellCoordinate, $inOutText);

            // Format cell
            $sheet->getStyle($cellCoordinate)->getAlignment()->setWrapText(true);
            $sheet->getStyle($cellCoordinate)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $colIndex++;
        }

        $rowNum++;
    }

    // Set auto width
    for ($col = 1; $col <= $colCount; $col++) {
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
    }

    // Output
    $filename = 'Absensi_' . $bulanTahun . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

public function importExcel()
    {
        checklogin();

        // Validasi file upload
        $file = $this->request->getFile('file_excel');
        if (!$file->isValid()) {
            return redirect()->to(base_url('admin/absensi'))
                ->with('error', 'File tidak valid.');
        }

        // Pastikan hanya format excel yang diterima
        $ext = $file->getClientExtension();
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return redirect()->to(base_url('admin/absensi'))
                ->with('error', 'File harus berformat .xls atau .xlsx');
        }

        // Load file excel
        $spreadsheet = IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $db = \Config\Database::connect();
        $builder = $db->table('att_log');

        $dataInsert = [];
        $firstRow = true;
        foreach ($rows as $row) {
            // Lewati header (baris pertama)
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            // Pastikan kolom sesuai dengan urutan di excel
            $sn         = $row['A'] ?? null;
            $scan_date  = $row['B'] ?? null;
            $pin        = $row['C'] ?? null;
            $verifymode = $row['D'] ?? null;
            $inoutmode  = $row['E'] ?? null;
            $reserved   = $row['F'] ?? null;
            $work_code  = $row['G'] ?? null;
            $att_id     = $row['H'] ?? null;

            if (empty($sn) || empty($scan_date)) {
                continue; // skip baris kosong
            }

            $dataInsert[] = [
                'sn'         => $sn,
                'scan_date'  => $scan_date,
                'pin'        => $pin,
                'verifymode' => $verifymode,
                'inoutmode'  => $inoutmode,
                'reserved'   => $reserved,
                'work_code'  => $work_code,
                'att_id'     => $att_id
            ];
        }

        if (!empty($dataInsert)) {
            $builder->insertBatch($dataInsert);
            return redirect()->to(base_url('admin/absensi'))
                ->with('success', count($dataInsert) . ' data absensi berhasil diimport.');
        } else {
            return redirect()->to(base_url('admin/absensi'))
                ->with('error', 'Tidak ada data yang diimport.');
        }

        $query = $db->query("CALL spRptRekapAbsensi(" . $db->escape($tanggalAwal) . ", " . $db->escape($tanggalAkhir) . ")");
    }


}
