<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Karu_model;
use App\Models\Karupegawai_model;
use App\Models\Kelompokjam_model;
use App\Models\Jadwal_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Jadwal extends BaseController
{
    public function index()
    {
        checklogin();

        $m_Karu = new Karu_model();
        $data = [
            'title' => 'Input Jadwal Pegawai',
            'excelstatus' => 'excel',
            'karu_list' => $m_Karu->getAll(),
            'content' => 'admin/jadwal/index'
        ];
        echo view('admin/layout/wrapper', $data);
    }

    public function getPegawaiByKaru()
{
    checklogin();
    $m_Karupegawai = new Karupegawai_model();
    $m_Kelompokjam = new Kelompokjam_model();
    $m_Jadwal = new Jadwal_model();

    $idkaru = $this->request->getPost('idkaru');
    $periode = $this->request->getPost('periode'); // Format: YYYY-MM

    if (!$idkaru || !$periode) {
        return $this->response->setJSON([
            'pegawaiList' => [],
            'shiftList' => [],
            'jadwalList' => []
        ]);
    }

    // Ambil daftar pegawai berdasarkan idkaru
    $pegawaiList = $m_Karupegawai
        ->select('karupegawai.*, pegawai.pegawai_nama, pegawai.pegawai_pin, pegawai.bagian')
        ->join('pegawai', 'pegawai.pegawai_pin = karupegawai.pegawai_pin')
        ->where('karupegawai.idkaru', $idkaru)
        ->findAll();

    // Ambil daftar shift berdasarkan bagian kerja pegawai
    $shiftList = [];
    if (!empty($pegawaiList)) {
        $bagianList = array_unique(array_column($pegawaiList, 'bagian'));
        if (!empty($bagianList)) {
            $shiftList = $m_Kelompokjam->whereIn('bagian', $bagianList)->findAll();
        }
    }

    // Ambil data jadwal yang sudah tersimpan untuk pegawai di periode ini
    $jadwalList = [];
    if (!empty($pegawaiList)) {
        $pegawaiPins = array_column($pegawaiList, 'pegawai_pin');

        // Periode dari tanggal 26 bulan sebelumnya hingga tanggal 25 bulan ini
        $tanggalAwal = date('Y-m-d', strtotime("$periode-26 -1 month"));
        $tanggalAkhir = date('Y-m-d', strtotime("$periode-25"));

        // Cek hasil tanggal
        error_log("Periode: $tanggalAwal - $tanggalAkhir");

        // Ambil data jadwal
        $jadwalData = $m_Jadwal
            ->whereIn('pegawai_pin', $pegawaiPins)
            ->where('tgl >=', $tanggalAwal)
            ->where('tgl <=', $tanggalAkhir)
            ->findAll();

        // Debug query (bisa dicek di log)
        error_log($m_Jadwal->getLastQuery());

        // Format ulang untuk frontend
        foreach ($jadwalData as $jadwal) {
            $jadwalList[$jadwal['pegawai_pin']][$jadwal['tgl']] = $jadwal['shift'];
        }
    }

    return $this->response->setJSON([
        'pegawaiList' => $pegawaiList,
        'shiftList' => $shiftList,
        'jadwalList' => $jadwalList ?: []
    ]);
}

public function store()
{
    $m_jadwal = new Jadwal_model();
    $periode = $this->request->getPost('periode');
    $idkaru = $this->request->getPost('idkaru');
    $jadwal = $this->request->getPost('jadwal');

    if (!empty($periode) && !empty($idkaru) && !empty($jadwal)) {
        foreach ($jadwal as $idpegawai => $dates) {
            foreach ($dates as $tanggal => $shift) {
                if (!empty($shift)) { // Pastikan shift tidak kosong
                    $m_jadwal->insert([
                        'periode' => $periode,
                        'idkaru' => $idkaru,
                        'idpegawai' => $idpegawai,
                        'tanggal' => $tanggal, // Simpan tanggalnya
                        'shift' => $shift
                    ]);
                }
            }
        }
        session()->setFlashdata('sukses', 'Jadwal berhasil disimpan.');
    } else {
        session()->setFlashdata('error', 'Harap lengkapi semua data sebelum menyimpan.');
    }

    return redirect()->to(base_url('admin/jadwal'));
}

public function autosave()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No direct script access allowed'
        ]);
    }

    $pegawai_pin = $this->request->getPost('pegawai_pin');
    $tgl = $this->request->getPost('tgl');
    $shift = $this->request->getPost('shift'); // Sesuaikan dengan field 'shift'

    if (!$pegawai_pin || !$tgl) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Pegawai dan tanggal harus diisi'
        ]);
    }

    $model = new \App\Models\Jadwal_model();
    $cek = $model->where(['pegawai_pin' => $pegawai_pin, 'tgl' => $tgl])->first();

    if ($cek) {
        if ($shift === null || $shift === '') {
            // Jika shift kosong, hapus jadwal
            $model->delete($cek['idjadwal']);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Jadwal dihapus'
            ]);
        } else {
            // Update jadwal jika sudah ada
            $model->update($cek['idjadwal'], ['shift' => $shift]);
        }
    } else {
        // Jika shift kosong, tidak perlu insert
        if ($shift !== null && $shift !== '') {
            $model->insert([
                'pegawai_pin' => $pegawai_pin,
                'tgl' => $tgl,
                'shift' => $shift
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Jadwal tidak diubah'
            ]);
        }
    }

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Jadwal tersimpan'
    ]);
}

private function getTanggalKerja($periode)
{
    // Hitung tanggal awal (26 bulan sebelumnya) dan tanggal akhir (25 bulan berjalan)
    $start = date('Y-m-d', strtotime("$periode-26 -1 month"));
    $end = date('Y-m-d', strtotime("$periode-25"));

    $tanggalKerja = [];
    while (strtotime($start) <= strtotime($end)) {
        $tanggalKerja[] = [
            'tgl' => date('d', strtotime($start)),
            'bulan' => date('m', strtotime($start)),
            'tahun' => date('Y', strtotime($start)),
            'full_date' => $start
        ];
        $start = date('Y-m-d', strtotime($start . ' +1 day'));
    }

    return $tanggalKerja;
}

public function exportCSV()
    {
        $idkaru = $this->request->getGet('idkaru');
        $periode = $this->request->getGet('periode');

        if (!$idkaru || !$periode) {
            return redirect()->back()->with('error', 'Karu dan Periode harus dipilih!');
        }

        $m_Karupegawai = new \App\Models\Karupegawai_model();
        $m_Karu = new \App\Models\Karu_model();
        $m_Jadwal = new \App\Models\Jadwal_model();

        $karu = $m_Karu->detail($idkaru);
        $karuNama = $karu ? $karu['nama'] : 'Karu Tidak Dikenal';
        $namakelompok = $karu ? $karu['kelompok_nama'] : 'namakelompok Tidak Dikenal';

        $pegawaiList = $m_Karupegawai
            ->select('karupegawai.*, karu.nama as nama_karu, pegawai.pegawai_nama, pegawai.pegawai_pin, pegawai.bagian')
            ->join('pegawai', 'pegawai.pegawai_pin = karupegawai.pegawai_pin')
            ->join('karu', 'karu.idkaru = karupegawai.idkaru')
            ->where('karupegawai.idkaru', $idkaru)
            ->findAll();

        if (!$pegawaiList) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan.');
        }

        $pegawaiPins = array_column($pegawaiList, 'pegawai_pin');

        $tanggalAwal = date('Y-m-d', strtotime("$periode-26 -1 month"));
        $tanggalAkhir = date('Y-m-d', strtotime("$periode-25"));

        $jadwalData = $m_Jadwal
            ->whereIn('pegawai_pin', $pegawaiPins)
            ->where('tgl >=', $tanggalAwal)
            ->where('tgl <=', $tanggalAkhir)
            ->findAll();

        $jadwalList = [];
        foreach ($jadwalData as $jadwal) {
            $jadwalList[$jadwal['pegawai_pin']][$jadwal['tgl']] = $jadwal['shift'];
        }

        $tanggalKerja = [];
        $start = strtotime($tanggalAwal);
        $end = strtotime($tanggalAkhir);
        while ($start <= $end) {
            $tanggalKerja[] = date('Y-m-d', $start);
            $start = strtotime('+1 day', $start);
        }

        // Map warna shift
        $shiftMap = [
            'pagi'    => ['label' => 'P',  'color' => '00FFBF'],
            'siang'   => ['label' => 'S',  'color' => 'FFBF00'],
            'malam'   => ['label' => 'M',  'color' => '00BFFF'],
            'midle'   => ['label' => 'MD', 'color' => 'FF6699'],
            'office'  => ['label' => 'O',  'color' => 'CCCCCC'],
            'office 1' => ['label' => 'O1',  'color' => 'CCCCCC'],
            'office 2' => ['label' => 'O2',  'color' => 'CCCCCC'],
            'pagi bangsal' => ['label' => 'PB',  'color' => 'CCCCCC'],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $lastColIndex = count($tanggalKerja) + 1;
        $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'Jadwal Kerja Periode ' . $periode);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Nama Karu
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Nama Karu: ' . $karuNama.' - Kelompok Kerja: '.$namakelompok);
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Header tabel tanggal
        $sheet->setCellValue('A3', 'Nama Pegawai');
        $colIndex = 2;
        foreach ($tanggalKerja as $tgl) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '3', date('d', strtotime($tgl)));
            $colIndex++;
        }

        // Data isi
        $row = 4;
        foreach ($pegawaiList as $pegawai) {
            $sheet->setCellValue('A' . $row, $pegawai['pegawai_nama']);
            $col = 2;
            foreach ($tanggalKerja as $tgl) {
                $pin = $pegawai['pegawai_pin'];
                $shiftRaw = strtolower($jadwalList[$pin][$tgl] ?? '');
                $shift = $shiftMap[$shiftRaw]['label'] ?? '';
                $color = $shiftMap[$shiftRaw]['color'] ?? '';

                $colLetter = Coordinate::stringFromColumnIndex($col);
                $cell = $colLetter . $row;
                $sheet->setCellValue($cell, $shift);
                if ($color) {
                    $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($color);
                }

                $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }

            $sheet->getStyle('A' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        // Border header
        $lastColIndex = count($tanggalKerja) + 1;
        $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);
        $lastHeaderCell = $lastColLetter . '3';    $sheet->getStyle("A3:$lastHeaderCell")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto size kolom
        // Set auto size hanya untuk kolom A (nama pegawai)
        $sheet->getColumnDimension('A')->setAutoSize(true);

        // Set lebar tetap untuk kolom tanggal (shift)
        $lastColIndex = count($tanggalKerja) + 1;
        for ($i = 2; $i <= $lastColIndex; $i++) { // Mulai dari kolom 2 (B)
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setWidth(4);
        }

        // Keterangan shift
        $ketRow = $row + 2;
        $sheet->setCellValue('A' . $ketRow, 'Keterangan Shift:');
        $sheet->getStyle('A' . $ketRow)->getFont()->setBold(true);
        $i = 1;
        $usedLabel = [];

        foreach ($shiftMap as $key => $info) {
            if (!in_array($info['label'], $usedLabel)) {
                $rowIndex = $ketRow + $i;
                $sheet->setCellValue('A' . $rowIndex, "{$info['label']} = " . ucfirst($key));
                $sheet->getStyle('A' . $rowIndex)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($info['color']);
                $usedLabel[] = $info['label'];
                $i++;
            }
        }        

        // Output Excel
        $filename = 'jadwal_kerja_' . $periode . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }


}
