<?php

namespace App\Controllers\Admin;

use App\Models\Lembur_model;

class Lembur extends BaseController
{
    public function index()
    {
        // Validasi login
        checklogin();

        // Ambil parameter bulanTahun dari query string atau gunakan default bulan ini
        $bulanTahun = $this->request->getGet('bulanTahun') ?? date('Y-m');

        // Ambil data Lembur dari stored procedure
        $m_Lembur = new Lembur_model();
        $dataLembur = $m_Lembur->getLemburByProcedure($bulanTahun);

        // Siapkan data untuk dikirim ke view
        $data = [
            'title'      => 'Data Lembur ' . $bulanTahun,
            'excelstatus' => 'excel',
            'dataLembur' => $dataLembur,
            'bulanTahun' => $bulanTahun,
            'content'    => 'admin/Lembur/index',
        ];

        // Tampilkan tampilan utama
        echo view('admin/layout/wrapper', $data);
    }

    public function exportCSV()
    {
        $bulanTahun = $this->request->getGet('bulanTahun') ?? date('Y-m');

        // Inisialisasi model
        $m_Lembur = new Lembur_model();
        $dataLembur = $m_Lembur->getLemburByProcedure($bulanTahun);

        if (empty($dataLembur)) {
            return redirect()->to(base_url('admin/lembur'))->with('error', 'Data lembur tidak tersedia.');
        }

        // Nama file CSV
        $filename = 'Lembur_' . $bulanTahun . '.csv';

        // Set header untuk download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Buka output stream untuk ditulis sebagai CSV
        $output = fopen('php://output', 'w');

        // Gunakan titik koma (;) agar kompatibel dengan Excel Indonesia
        $delimiter = ';';

        // Tambahkan judul di baris pertama
        fputcsv($output, ['Data Lembur Periode ' . $bulanTahun], $delimiter);
        fputcsv($output, [], $delimiter); // Baris kosong untuk pemisah

        // Header tabel CSV
        $header = ['Kelompok', 'Nama Pegawai'];
        foreach (array_keys($dataLembur[0]) as $key) {
            if (is_numeric($key)) {
                $header[] = $key;
            }
        }
        fputcsv($output, $header, $delimiter);

        // Isi data pegawai
        foreach ($dataLembur as $row) {
            $dataRow = [
                $row['bagian'],
                $row['pegawai_nama']
            ];
            foreach (array_keys($row) as $key) {
                if (is_numeric($key)) {
                    // Perbaiki masalah <br> agar jadi newline di Excel
                    $dataRow[] = str_replace('<br>', "\n", $row[$key]);
                }
            }
            // Tambahkan pembungkus " untuk mendukung multiline di Excel
            fputcsv($output, $dataRow, $delimiter, '"');
        }

        fclose($output);
        exit();
    }
}
