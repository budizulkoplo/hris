<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Masterpegawai_model;

class LosAbsen extends BaseController
{
    public function index()
    {
        checklogin();

        $m_pegawai = new Masterpegawai_model();
        $pegawai_list = $m_pegawai->orderBy('pegawai_nama', 'ASC')->findAll();

        // Mapping pin => nama pegawai
        $map_nama = [];
        foreach ($pegawai_list as $p) {
            $map_nama[$p['pegawai_pin']] = $p['pegawai_nama'];
        }

        // Ambil parameter bulanTahun dari GET (format: YYYY-MM), default ke bulan ini
        $bulanTahun = $this->request->getGet('bulanTahun') ?? date('Y-m');

        // Ubah ke rentang tanggal
        $tanggal_awal  = $bulanTahun . '-01';
        $tanggal_akhir = date('Y-m-t', strtotime($tanggal_awal)); // Akhir bulan sesuai bulanTahun

        // Koneksi DB absensi
        $db = \Config\Database::connect('pdam');

        $absen_manual = $db->table('att_log')
            ->where('sn', '') // hanya manual
            ->where('scan_date >=', $tanggal_awal . ' 00:00:00')
            ->where('scan_date <=', $tanggal_akhir . ' 23:59:59')
            ->orderBy('scan_date', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title'        => 'Input Absen Manual (Lupa Absen)',
            'pegawai'      => $pegawai_list,
            'absen_manual' => $absen_manual,
            'map_nama'     => $map_nama,
            'bulanTahun'   => $bulanTahun, // dikirim ke view agar input month tetap terisi
            'content'      => 'admin/losabsen/index',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function hapus($att_id)
    {
        checklogin();

        $db = \Config\Database::connect('absensi');
        $builder = $db->table('att_log');

        $deleted = $builder->where('att_id', $att_id)->where('sn', '')->delete();

        if ($deleted) {
            return redirect()->to('/admin/losabsen')->with('sukses', 'Data absen manual berhasil dihapus.');
        } else {
            return redirect()->to('/admin/losabsen')->with('gagal', 'Gagal menghapus data.');
        }
    }

    public function deleteLog()
{
    checklogin();

    $pin       = $this->request->getPost('pin');
    $scan_date = $this->request->getPost('scan_date');

    $db      = \Config\Database::connect('absensi');
    $builder = $db->table('att_log');

    // Hapus hanya data manual (sn kosong) sesuai pin & scan_date
    $deleted = $builder
        ->where('pin', $pin)
        ->where('scan_date', $scan_date)
        ->delete();

    if ($deleted) {
        return $this->response->setJSON([
            'success' => true,
            'message' => "Log dengan PIN {$pin} dan Tanggal {$scan_date} berhasil dihapus"
        ]);
    } else {
        return $this->response->setJSON([
            'success' => false,
            'message' => "Gagal menghapus log PIN {$pin} pada Tanggal {$scan_date}"
        ]);
    }
}



    public function simpan()
    {
        checklogin();

        if ($this->request->getMethod() === 'post') {
            $db = \Config\Database::connect('absensi'); // koneksi ke DB absensi
            $builder = $db->table('att_log');

            $data = [
                'sn'         => '', // Bisa diisi default SN mesin jika dibutuhkan
                'scan_date'  => $this->request->getPost('scan_date'),
                'pin'        => $this->request->getPost('pin'), // pegawai_pin
                'verifymode'=> 1,
                'inoutmode'  => $this->request->getPost('inoutmode'),
                'reserved'   => 0,
                'work_code'  => 0,
                'att_id'     => uniqid(),
            ];

            $builder->insert($data);

            return redirect()->to('/admin/losabsen')->with('sukses', 'Data absen manual berhasil disimpan.');
        }

        return redirect()->to('/admin/losabsen')->with('gagal', 'Terjadi kesalahan saat menyimpan data.');
    }

    // Tambah di LosAbsen Controller
public function getLogAbsen()
{
    $pin = $this->request->getGet('pin');
    $tanggal = $this->request->getGet('tanggal');

    if (!$pin || !$tanggal) {
        return $this->response->setJSON(['error' => 'Parameter pin dan tanggal wajib diisi']);
    }

    // Gunakan koneksi default (database utama)
    $db = \Config\Database::connect(); // tanpa parameter = koneksi default

    try {
        $query = $db->query("CALL spGetLogAbsenPegawai(?, ?)", [$pin, $tanggal]);
        $result = $query->getResultArray();

        // Debug: tampilkan hasil mentah
        return $this->response->setJSON([
            'success' => true,
            'pin' => $pin,
            'tanggal' => $tanggal,
            'data' => $result
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menjalankan query',
            'error' => $e->getMessage()
        ]);
    }
}


}
