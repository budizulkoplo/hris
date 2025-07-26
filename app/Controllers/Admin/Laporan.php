<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\RekapLemburModel;
use DateTime;

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

    // Periode bulan lalu
    $tanggalAwalLalu = date('Y-m-d', strtotime('-1 month', strtotime($tanggalAwal)));
    $tanggalAkhirLalu = date('Y-m-d', strtotime('-1 day', strtotime($tanggalAwal)));

    // Ambil lembur bulan lalu
    $queryLalu = $db->query("CALL spRptRekapAbsensibulanlalu(" . $db->escape($tanggalAwalLalu) . ", " . $db->escape($tanggalAkhirLalu) . ")");
    $dataLalu = $queryLalu->getResultArray();
    $queryLalu->freeResult(); // Penting: Bebaskan result set jika pakai CALL

    $lemburBulanLalu = [];

    foreach ($dataLalu as $row) {
        $pin = $row['pegawai_pin'];
        if (!empty($row['alasan_lembur']) && !empty($row['lembur_masuk']) && !empty($row['lembur_pulang'])) {
            $masuk = \DateTime::createFromFormat('H:i:s', $row['lembur_masuk']);
            $pulang = \DateTime::createFromFormat('H:i:s', $row['lembur_pulang']);
            if ($masuk && $pulang) {
                if ($pulang < $masuk) {
                    $pulang->add(new \DateInterval('P1D'));
                }
                $interval = $masuk->diff($pulang);
                $detik = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
                if ($detik > 0 && $detik <= 57600) {
                    $lemburBulanLalu[$pin] = ($lemburBulanLalu[$pin] ?? 0) + $detik;
                }
            }
        }
    }

    // Ambil data absensi bulan ini
    $query = $db->query("CALL spRptRekapAbsensi(" . $db->escape($tanggalAwal) . ", " . $db->escape($tanggalAkhir) . ")");
    $dataAbsensi = $query->getResultArray();
    $query->freeResult(); // Penting juga untuk result dari CALL

    $summaryPegawai = [];

    foreach ($dataAbsensi as $row) {
        $pin = $row['pegawai_pin'];

        if (!isset($summaryPegawai[$pin])) {
            $summaryPegawai[$pin] = [
                'pegawai_pin' => $pin,
                'pegawai_nama' => $row['pegawai_nama'],
                'bagian' => $row['bagian'],
                'total_hari_kerja' => 0,
                'total_terlambat' => 0,
                'total_lembur' => 0,
                'total_lembur_lalu' => $lemburBulanLalu[$pin] ?? 0,
                'konversilembur' => 0,
                'total_cuti' => 0,
                'total_tugas_luar' => 0,
                'sisa_lembur_jam_bulan_ini' => 0
            ];
        }

        $isTugasLuar = !empty($row['status_khusus']) && (
            stripos($row['status_khusus'], 'tugas luar') !== false ||
            stripos($row['status_khusus'], 'dinas luar') !== false
        );

        // Hitung hari kerja
        if ((!empty($row['jam_masuk']) || !empty($row['jam_pulang'])) && !$isTugasLuar) {
            $summaryPegawai[$pin]['total_hari_kerja']++;
        }

        // Hitung keterlambatan
        if (empty($row['status_khusus'])) {
            if (!empty($row['jam_masuk']) && !empty($row['jam_masuk_shift'])) {
                $masuk = strtotime($row['jam_masuk']);
                $masukShift = strtotime($row['jam_masuk_shift']);
                if ($masuk > $masukShift) {
                    $summaryPegawai[$pin]['total_terlambat'] += $masuk - $masukShift;
                }
            }
        }

        // Hitung lembur bulan ini
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
                    if ($detik > 0 && $detik <= 57600) {
                        $summaryPegawai[$pin]['total_lembur'] += $detik;
                    }
                }
            } catch (\Exception $e) {
                // Abaikan kesalahan parsing
            }
        }

        // Hitung cuti
        if (!empty($row['status_khusus']) && strtolower($row['status_khusus']) === 'cuti') {
            $summaryPegawai[$pin]['total_cuti']++;
        }

        // Hitung tugas luar
        if ($isTugasLuar) {
            $summaryPegawai[$pin]['total_tugas_luar']++;
        }
    }

    foreach ($summaryPegawai as &$pegawai) {
        // Menghitung total lembur bulan lalu (dalam detik)
        $totalLemburBulanLaluDetik = $pegawai['total_lembur_lalu']; // Total detik lembur bulan lalu
    
        // Menghitung sisa lembur bulan lalu yang belum terkonversi ke hari kerja
        $detikPerHariKerja = 7 * 3600; // 7 jam dalam detik
        $sisaLemburBulanLaluDetik = $totalLemburBulanLaluDetik % $detikPerHariKerja; // Sisa lembur bulan lalu yang belum terkonversi ke hari kerja
    
        // Menghitung jam, menit, detik dari sisa lembur bulan lalu
        $sisaLemburBulanLaluJam = floor($sisaLemburBulanLaluDetik / 3600); // Jam sisa lembur bulan lalu
        $sisaLemburBulanLaluDetik = $sisaLemburBulanLaluDetik % 3600; // Sisa detik setelah dihitung jam
        $sisaLemburBulanLaluMenit = floor($sisaLemburBulanLaluDetik / 60); // Menit sisa lembur bulan lalu
        $sisaLemburBulanLaluDetik = $sisaLemburBulanLaluDetik % 60; // Sisa detik setelah dihitung menit
    
        // Menghitung jam lembur bulan ini (dalam detik)
        $totalDetikBulanIni = $pegawai['total_lembur']; // Detik lembur bulan ini
        $totallembur=$pegawai['total_lembur']+$sisaLemburBulanLaluDetik;
    
        // Menghitung jam, menit, detik dari lembur bulan ini
        $totalLemburBulanIniJam = floor($totalDetikBulanIni / 3600); // Jam lembur bulan ini
        $totalDetikBulanIni = $totalDetikBulanIni % 3600; // Sisa detik setelah dihitung jam
        $totalLemburBulanIniMenit = floor($totalDetikBulanIni / 60); // Menit lembur bulan ini
        $totalDetikBulanIni = $totalDetikBulanIni % 60; // Sisa detik setelah dihitung menit
    
        // Menghitung sisa lembur bulan ini yang belum terkonversi ke hari kerja
        $sisaLemburBulanIniDetik = $totalDetikBulanIni % $detikPerHariKerja; // Sisa lembur bulan ini yang belum terkonversi ke hari kerja
        $sisaLemburBulanIniJam = floor($sisaLemburBulanIniDetik / 3600); // Jam sisa lembur bulan ini
        $sisaLemburBulanIniDetik = $sisaLemburBulanIniDetik % 3600; // Sisa detik setelah dihitung jam
        $sisaLemburBulanIniMenit = floor($sisaLemburBulanIniDetik / 60); // Menit sisa lembur bulan ini
        $sisaLemburBulanIniDetik = $sisaLemburBulanIniDetik % 60; // Sisa detik setelah dihitung menit
    
        // Total lembur bulan lalu + bulan ini dalam jam
        $totalLemburJam = $totalLemburBulanIniJam + ($sisaLemburBulanLaluJam);

        // tambahan coba sisa lembur total
        $totalLemburLalu = $pegawai['total_lembur_lalu']; // Total detik lembur bulan lalu
        $totalLemburIni  = $pegawai['total_lembur'];      // Total detik lembur bulan ini

        // Hitung sisa lembur bulan lalu (yang tidak sempat dikonversi bulan lalu)
        $sisaLemburLaluDetik = $totalLemburLalu % $detikPerHariKerja;

        // Tambahkan dengan total lembur bulan ini → untuk sisa konversi bulan ini
        $akumulasiUntukSisa = $sisaLemburLaluDetik + $totalLemburIni;
        $sisaKonversiDetik = $akumulasiUntukSisa % $detikPerHariKerja;

        // Format sisa konversi bulan ini ke jam:menit:detik
        $sisaJam = floor($sisaKonversiDetik / 3600);
        $sisaDetikSisa = $sisaKonversiDetik % 3600;
        $sisaMenit = floor($sisaDetikSisa / 60);
        $sisaDetik = $sisaDetikSisa % 60;

        // Hitung total jam (dari semua lembur lalu dan bulan ini)
        $totalGabunganDetik = $totalLemburLalu + $totalLemburIni;
        $totalJamGabungan = floor($totalGabunganDetik / 3600);
        $pegawai['konversilembur'] = round($totalJamGabungan / 7, 2);
        // disini
    
        // Mengonversi total lembur (bulan lalu + bulan ini) ke hari kerja (1 hari kerja = 7 jam)
        $pegawai['konversilembur'] = round($totalLemburJam / 7, 2); // Total lembur dalam hari kerja
    
        // Menyimpan data detail ke array
        $pegawai['sisa_lembur_bulan_lalu'] = sprintf("%02d:%02d:%02d", $sisaLemburBulanLaluJam, $sisaLemburBulanLaluMenit, $sisaLemburBulanLaluDetik); // Lembur bulan lalu yang belum terkonversi dalam format jam:menit:detik
        $pegawai['real_lembur_bulan_ini'] = sprintf("%02d:%02d:%02d", $totalLemburBulanIniJam, $totalLemburBulanIniMenit, $totalDetikBulanIni); // Real lembur bulan ini dalam format jam:menit:detik
        $pegawai['sisa_lembur_bulan_ini'] = sprintf("%02d:%02d:%02d", $sisaJam, $sisaMenit, $sisaDetik);

        // Mengonversi detik menjadi format waktu jam:menit:detik untuk total terlambat
        $pegawai['total_terlambat_formatted'] = $this->secondsToTime($pegawai['total_terlambat']);
        $pegawai['total_lembur_formatted'] = $this->secondsToTime($totallembur);
        $pegawai['total_lembur_lalu_formatted'] = $this->secondsToTime($pegawai['total_lembur_lalu']);

    }    

    $this->simpanRekapLembur($summaryPegawai, $tanggalAwal, $tanggalAkhir);
    return $summaryPegawai;
}

private function simpanRekapLembur($summaryPegawai, $tanggalAwal, $tanggalAkhir)
{
    $db = \Config\Database::connect();
    $currentDateTime = date('Y-m-d H:i:s');

    // Tentukan bulan dan tahun untuk periode 26-25
    $periode = $this->getPeriode2625($tanggalAwal, $tanggalAkhir);
    
    foreach ($summaryPegawai as $pin => $pegawai) {
        // Gunakan $pin sebagai pegawai_pin jika array menggunakan format associative
        $pegawaiPin = is_array($pegawai) ? ($pegawai['pegawai_pin'] ?? $pin) : $pin;
        
        // Pastikan kita memiliki nilai pin yang valid
        if (empty($pegawaiPin)) {
            continue;
        }

        $pegawaiId = $this->getPegawaiIdByPin($pegawaiPin);
        if (!$pegawaiId) {
            continue;
        }

        // Validasi data yang diperlukan
        $requiredKeys = ['sisa_lembur_bulan_ini', 'total_lembur', 'konversilembur'];
        foreach ($requiredKeys as $key) {
            if (!isset($pegawai[$key])) {
                log_message('error', "Key {$key} tidak ditemukan untuk pegawai PIN: {$pegawaiPin}");
                continue 2; // Lewati pegawai ini
            }
        }

        // Konversi sisa lembur ke detik
        $sisaLemburDetik = $this->timeToSeconds($pegawai['sisa_lembur_bulan_ini']);

        $rekapData = [
            'pegawai_id' => $pegawaiId,
            'bulan' => $periode['bulan'],
            'tahun' => $periode['tahun'],
            
            'sisa_lembur_detik' => $sisaLemburDetik,
            'total_lembur' => $pegawai['total_lembur'],
            'konversi_hari' => $pegawai['konversilembur'],
            'updated_at' => $currentDateTime
        ];

        // Cek data existing berdasarkan periode
        $existingRecord = $db->table('rekap_lembur')
            ->where('pegawai_id', $pegawaiId)
           
            ->get()
            ->getRowArray();


            $rekapData['created_at'] = $currentDateTime;
            $db->table('rekap_lembur')->insert($rekapData);
        
    }
}

/**
 * Menentukan bulan dan tahun untuk periode 26-25
 */
private function getPeriode2625($tanggalAwal, $tanggalAkhir)
{
    $awal = new DateTime($tanggalAwal);
    $akhir = new DateTime($tanggalAkhir);

    // Jika tanggal akhir lebih kecil dari tanggal awal, berarti periode melampaui tahun
    if ($akhir < $awal) {
        return [
            'bulan' => $awal->format('n'), // Bulan dari tanggal awal
            'tahun' => $awal->format('Y')  // Tahun dari tanggal awal
        ];
    }
    
    return [
        'bulan' => $akhir->format('n'), // Bulan dari tanggal akhir
        'tahun' => $akhir->format('Y')  // Tahun dari tanggal akhir
    ];
}

/**
 * Helper function untuk mendapatkan ID pegawai berdasarkan PIN
 */
private function getPegawaiIdByPin($pin)
{
    $db = \Config\Database::connect();
    $row = $db->table('pegawai')
             ->select('id')
             ->where('pin', $pin)
             ->get()
             ->getRow();
    
    return $row ? $row->id : null;
}

/**
 * Konversi format waktu (HH:MM:SS) ke detik
 */
private function timeToSeconds($time)
{
    $parts = explode(':', $time);
    if (count($parts) !== 3) return 0;
    return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
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

public function kajian()
    {
        $m_kehadiran = new \App\Models\KehadiranKajianModel();
        $m_kajian = new \App\Models\Kajian_model();

        // Ambil input filter
        $idkajian = $this->request->getGet('idkajian');
        $tanggal = $this->request->getGet('tanggal'); // format: Y-m-d

        // Query dasar join dengan tabel kajian untuk menampilkan nama kajian
        $query = $m_kehadiran->select('kehadiran_kajian.*, kajian.namakajian, kajian.tanggal as tanggal_kajian')
            ->join('kajian', 'kajian.idkajian = kehadiran_kajian.idkajian', 'left');

        // Filter berdasarkan idkajian jika dipilih
        if (!empty($idkajian)) {
            $query->where('kehadiran_kajian.idkajian', $idkajian);
        }

        // Filter berdasarkan tanggal scan jika dipilih
        if (!empty($tanggal)) {
            $query->where('DATE(kehadiran_kajian.waktu_scan)', $tanggal);
        }

        // Eksekusi query
        $dataKehadiran = $query->orderBy('kehadiran_kajian.waktu_scan', 'ASC')->findAll();

        // Ambil daftar kajian untuk dropdown filter
        $dataKajian = $m_kajian->findAll();

        // Kirim data ke view
        $data = [
            'title'         => 'Laporan Kehadiran Kajian',
            'printstatus'   => 'print',
            'dataKehadiran' => $dataKehadiran,
            'dataKajian'    => $dataKajian,
            'idkajian'      => $idkajian,
            'tanggal'       => $tanggal,
            'content'       => 'admin/laporan/kajian',
        ];

        return view('admin/layout/wrapper', $data);
    }

public function absensidokter()
{
    $periode = $this->request->getGet('periode') ?? date('Y-m');
    $urlApi = "https://dr.rspkuboja.com/api/absendokter?periode={$periode}";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $urlApi,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'X-API-KEY: pkuboja2025',
        ],
    ]);
    $response = curl_exec($ch);

    if (!$response) {
        throw new \Exception('Tidak dapat mengambil data absensi dari API.');
    }

    file_put_contents(WRITEPATH . 'debug_api_response.txt', $response);

    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception('JSON decode error: ' . json_last_error_msg());
    }

    if (!isset($result['status']) || $result['status'] !== 'success') {
        throw new \Exception('Data absensi tidak valid.');
    }

    $dataAbsensi = $result['data'];
    $periodeDisplay = date('F Y', strtotime($result['periode'] . '-01'));

    $rekapDokter = [];
    foreach ($dataAbsensi as $absensi) {
        $nik = $absensi['nik'];
        if (!isset($rekapDokter[$nik])) {
            $rekapDokter[$nik] = [
                'nik' => $nik,
                'nama_lengkap' => $absensi['nama_lengkap'],
                'jabatan' => $absensi['jabatan'],
                'jumlah_kehadiran' => 0,
            ];
        }
        $rekapDokter[$nik]['jumlah_kehadiran']++;
    }

    usort($rekapDokter, fn($a, $b) => strcmp($a['nama_lengkap'], $b['nama_lengkap']));

    $data = [
        'title' => 'Rekap Presensi Dokter',
        'printstatus' => 'print',
        'rekapDokter' => $rekapDokter,
        'periode' => $periode,
        'periodeDisplay' => $periodeDisplay,
        'tanggalCetak' => date('d-m-Y'),
        'totalDokter' => count($rekapDokter),
        'totalKehadiran' => array_sum(array_column($rekapDokter, 'jumlah_kehadiran')),
        'content' => 'admin/laporan/absensidokter',
    ];

    return view('admin/layout/wrapper', $data);
}

}
