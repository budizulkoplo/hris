<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Komponengaji_model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Komponengaji extends BaseController
{
    public function index()
{
    checklogin(); // validasi login

    $periode = $this->request->getGet('periode') ?? date('Y-m');
    $model   = new Komponengaji_model();

    // Ambil data rekap gaji per periode
    $rekap = $model->getRekapGaji($periode);

    // Ambil data rujukan dari tabel rujukan berdasarkan bulan periode
    $db = \Config\Database::connect();

    // Misal periode = '2025-07' -> ambil dari tglrujukan
    $startDate = $periode . '-01';
    $endDate = date('Y-m-t', strtotime($startDate)); // ambil akhir bulan

    $rujukanData = $db->table('rujukan')
                    ->select('pegawai_pin, COUNT(*) as jmlrujukan')
                    ->where('tglrujukan >=', $startDate)
                    ->where('tglrujukan <=', $endDate)
                    ->groupBy('pegawai_pin')
                    ->get()->getResultArray();

    // Mapping rujukan berdasarkan pegawai_pin
    $mapRujukan = [];
    foreach ($rujukanData as $rj) {
        $mapRujukan[$rj['pegawai_pin']] = $rj['jmlrujukan'];
    }

    // Gabungkan data rujukan ke dalam array rekap
    foreach ($rekap as &$row) {
        $row['jmlrujukan'] = $mapRujukan[$row['pegawai_pin']] ?? 0;
    }


    // Data untuk view
    $data = [
        'title'   => 'Komponen Penggajian Pegawai',
        'excelstatus'     => 'excel',
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

        $detail = $model->getDetailGaji($pegawai_pin, $periode);

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

    public function updateRujukan()
{
    $pin = $this->request->getPost('pegawai_pin');
    $periode = $this->request->getPost('periode');
    $jml = (int) $this->request->getPost('jmlrujukan');

    $db = \Config\Database::connect();
    $builder = $db->table('rujukan');

    // Cek apakah data sudah ada
    $existing = $builder->where(['pegawai_pin' => $pin, 'periode' => $periode])->get()->getRow();

    if ($existing) {
        $builder->where(['pegawai_pin' => $pin, 'periode' => $periode])
                ->update(['jmlrujukan' => $jml]);
    } else {
        $builder->insert([
            'pegawai_pin' => $pin,
            'periode' => $periode,
            'jmlrujukan' => $jml
        ]);
    }

    return $this->response->setJSON(['status' => 'success']);
}

public function slip($pin)
{
    checklogin(); // validasi login

    $periode = $this->request->getGet('periode') ?? date('Y-m');
    $model   = new \App\Models\Komponengaji_model();

    // Ambil semua rekap gaji pada periode tersebut
    $rekapList = $model->getRekapGaji($periode);

    // Ambil data rujukan dari DB untuk periode ini
    $db = \Config\Database::connect();
    $rujukanData = $db->table('rujukan')
                      ->where('periode', $periode)
                      ->get()->getResultArray();

    // Mapping rujukan berdasarkan pegawai_pin
    $mapRujukan = [];
    foreach ($rujukanData as $rj) {
        $mapRujukan[$rj['pegawai_pin']] = $rj['jmlrujukan'];
    }

    // Cari data pegawai dengan pin yang cocok
    $rekap = null;
    foreach ($rekapList as $row) {
        if ($row['pegawai_pin'] == $pin) {
            // Tambahkan jmlrujukan dari mapping jika ada
            $row['jmlrujukan'] = $mapRujukan[$pin] ?? 0;
            $rekap = $row;
            break;
        }
    }

    if (!$rekap) {
        return redirect()->to(base_url('admin/komponengaji'))->with('warning', 'Data tidak ditemukan.');
    }

    // Data tambahan untuk tampilan slip
    $site = [
        'icon' => 'logopku.png',
        'namaweb' => 'RS PKU Muhammadiyah Boja',
    ];

    return view('admin/komponengaji/slip', [
        'rekap'   => $rekap,
        'periode' => $periode,
        'site'    => $site,
    ]);
}

public function exportexcel()
{
    checklogin(); // Validasi login

    $periode = $this->request->getGet('periode') ?? date('Y-m');
    $model   = new \App\Models\Komponengaji_model();

    $rekap = $model->getRekapGaji($periode);

    // Ambil rujukan dari DB
    $db = \Config\Database::connect();
    $startDate = $periode . '-01';
    $endDate = date('Y-m-t', strtotime($startDate)); // ambil akhir bulan

    $rujukanData = $db->table('rujukan')
                    ->select('pegawai_pin, COUNT(*) as jmlrujukan')
                    ->where('tglrujukan >=', $startDate)
                    ->where('tglrujukan <=', $endDate)
                    ->groupBy('pegawai_pin')
                    ->get()->getResultArray();
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
        'Jml Rujukan', 'Tunj. Rujukan', 'Uang Makan', 'Kehadiran', 'Tugas Luar Val', 'Lembur Val',
        'Jumlah', 'ZIS', 'PPH21', 'Qurban', 'Pot. Transport', 'Infaq PDM', 'BPJS', 'BPJS TK', 'Koperasi', 'Total Potongan', 'Grand Total'
    ];
    $sheet->fromArray($header, null, 'A1');

    $rowIndex = 2;
    foreach ($rekap as $row) {
        $jmlrujukan = $mapRujukan[$row['pegawai_pin']] ?? 0;
        $tunjRujukan = $jmlrujukan * $row['rujukan'];
        $totalHariKerja = $row['totalharikerja'] ?? 0;
        $kehadiranNominal = $row['kehadiran'] ?? 0;
        $konversiLembur = $row['konversilembur'] ?? 0;

        $uangMakan = $totalHariKerja * $row['uangmakan'];
        $kehadiranVal = $row['totalharikerja'] * $kehadiranNominal;
        $tugasluarval = $row['tugasluar'] * $kehadiranNominal;
        $lemburVal = $konversiLembur * $kehadiranNominal;

        $jumlah = $row['gajipokok'] + $row['tunjstruktural'] + $row['tunjkeluarga'] + $row['tunjfungsional'] + $row['tunjapotek'] + $tunjRujukan + $uangMakan + $kehadiranVal + $tugasluarval + $lemburVal;
        $bpjs = ($jumlah > 4000000) ? 40000 : 30000;
        $zis = round($jumlah * 0.025);
        $infaqPdm = round($jumlah * 0.01);
        $potongan = $zis + $bpjs + $infaqPdm + $row['koperasi'];
        $grandtotal = $jumlah - $potongan;

        $sheet->fromArray([
            $row['pegawai_nama'], $row['gajipokok'], $row['tunjstruktural'], $row['tunjfungsional'],
            $row['tunjkeluarga'], $row['tunjapotek'], $row['jmlabsensi'], $row['jmlterlambat'],
            $konversiLembur, $row['cuti'], $row['tugasluar'], $row['doubleshift'], $totalHariKerja,
            $jmlrujukan, $tunjRujukan, $uangMakan, $kehadiranVal, $tugasluarval, $lemburVal,
            $jumlah, $zis, $row['pph21'] ?? 0, $row['qurban'] ?? 0, $row['potransport'] ?? 0,
            $infaqPdm, $bpjs, $row['bpjstk'] ?? 0, $row['koperasi'], $potongan, $grandtotal
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
