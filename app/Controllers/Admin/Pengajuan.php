<?php

namespace App\Controllers\Admin;

use App\Models\Cuti_model;
use App\Models\Izin_model;
use App\Models\Lembur_model;
use App\Models\Tugasluar_model;
use App\Models\Pegawai_model;
use DateTime;
use DateInterval;
use DatePeriod;
use Mpdf\Mpdf;

class Pengajuan extends BaseController
{
    public function formatNomorSurat($id, $tanggal)
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $bulan = date('n', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        return str_pad($id, 3, '0', STR_PAD_LEFT) . '/CUTI/' . $bulanRomawi[$bulan] . '/' . $tahun;
    }

    public function cuti($pegawai_pin = null, $idkaru = null)
{
    checklogin();

    $m_cuti = new Cuti_model();
    $m_pegawai = new Pegawai_model();

    // Ambil daftar cuti pegawai jika pegawai_pin tersedia
    $daftar_cuti = [];
    $sisa_cuti = 12; // Default sisa cuti per tahun

    if (!empty($pegawai_pin)) {
        // Ambil daftar cuti dalam tahun berjalan
        $tahun_sekarang = date('Y');
        $daftar_cuti = $m_cuti->where('pegawai_pin', $pegawai_pin)
            ->where('YEAR(tgl_mulai)', $tahun_sekarang)
            ->orderBy('tgl_mulai', 'DESC')
            ->findAll();

        // Hitung sisa cuti
        $cuti_diambil = array_sum(array_column($daftar_cuti, 'jml_hari'));
        $sisa_cuti = max(0, 12 - $cuti_diambil);
    }

    $data = [
        'title'            => 'Ajukan Cuti Pegawai',
        'pegawai'          => $m_pegawai->findAll(),
        'selected_pegawai' => $pegawai_pin,
        'idkaru'           => $idkaru,
        'daftar_cuti'      => $daftar_cuti, 
        'sisa_cuti'        => $sisa_cuti, 
        'content'          => 'admin/pengajuan/cuti',
    ];

    echo view('admin/layout/wrapper', $data);
}

public function simpancuti()
{
    checklogin();

    $m_cuti = new Cuti_model();
    $m_pegawai = new Pegawai_model();

    if ($this->request->getMethod() === 'post' && $this->validate([
        'pegawai_pin'   => 'required',
        'tgl_mulai'     => 'required',
        'tgl_selesai'   => 'required',
        'alasan'        => 'required',
        'idpengganti'   => 'required',
        'jeniscuti'     => 'required'
    ])) {
        // Konversi format tanggal untuk tgl_mulai
        $tgl_mulai_obj = DateTime::createFromFormat('m/d/Y', $this->request->getPost('tgl_mulai'));
        if (!$tgl_mulai_obj) {
            session()->setFlashdata('error', 'Format tanggal mulai tidak valid (MM/DD/YYYY)');
            return redirect()->back()->withInput();
        }
        $tgl_mulai = $tgl_mulai_obj->format('Y-m-d');

        // Konversi format tanggal untuk tgl_selesai
        $tgl_selesai_obj = DateTime::createFromFormat('m/d/Y', $this->request->getPost('tgl_selesai'));
        if (!$tgl_selesai_obj) {
            session()->setFlashdata('error', 'Format tanggal selesai tidak valid (MM/DD/YYYY)');
            return redirect()->back()->withInput();
        }
        $tgl_selesai = $tgl_selesai_obj->format('Y-m-d');

        // Validasi tanggal selesai harus setelah tanggal mulai
        if ($tgl_selesai < $tgl_mulai) {
            session()->setFlashdata('error', 'Tanggal selesai harus setelah tanggal mulai');
            return redirect()->back()->withInput();
        }

        // Hitung jumlah hari cuti
        $start = new DateTime($tgl_mulai);
        $end = new DateTime($tgl_selesai);
        $end->modify('+1 day'); // Include end date
        $interval = $start->diff($end);
        $jml_hari = $interval->days;

        // Data untuk cutihdr
        $dataHeader = [
            'pegawai_pin'   => $this->request->getPost('pegawai_pin'),
            'tgl_mulai'     => $tgl_mulai,
            'tgl_selesai'   => $tgl_selesai,
            'jml_hari'      => $jml_hari,
            'alasancuti'    => $this->request->getPost('alasan'),
            'jeniscuti'     => $this->request->getPost('jeniscuti'),
            'idpengganti'   => $this->request->getPost('idpengganti'),
            'created_at'    => date('Y-m-d H:i:s')
        ];

        // Generate array tanggal cuti untuk detail
        $tanggalCuti = [];
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        foreach ($period as $date) {
            $tanggalCuti[] = $date->format('Y-m-d');
        }

        // Simpan ke database menggunakan model
        $idcuti = $m_cuti->simpanCuti($dataHeader, $tanggalCuti);

        if ($idcuti) {
            session()->setFlashdata('sukses', 'Cuti berhasil diajukan.');
            return redirect()->to(base_url("admin/pengajuan/cuti/" . $this->request->getPost('pegawai_pin')));
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan cuti');
            return redirect()->back()->withInput();
        }
    }

    session()->setFlashdata('error', 'Data tidak valid. Periksa kembali input Anda.');
    return redirect()->back()->withInput();
}

    // Cetak surat cuti
    public function cetak_cuti($idcuti)
{
    checklogin();

    $m_cuti = new Cuti_model();
    $m_pegawai = new Pegawai_model();

    // Ambil data header cuti
    $cuti = $m_cuti->find($idcuti);
    if (!$cuti) {
        return redirect()->back()->with('error', 'Data cuti tidak ditemukan.');
    }

    // Ambil data pegawai pemohon
    $pegawai = $m_pegawai
        ->db
        ->table('vwpegawai')
        ->where('pegawai_pin', $cuti['pegawai_pin'])
        ->get()
        ->getRowArray();

    if (!$pegawai) {
        return redirect()->back()->with('error', 'Data pegawai tidak ditemukan.');
    }


    // Ambil data pegawai pengganti
    $pengganti = $m_pegawai->where('pegawai_pin', $cuti['idpengganti'])->first();

    // Ambil data SDI
    $sdi = $m_pegawai->where('jabatan', 'SDI')->first();

    // Format nomor surat
    $bulanRomawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    $bulan = date('n', strtotime($cuti['tgl_mulai']));
    $tahun = date('Y', strtotime($cuti['tgl_mulai']));
    $nosurat = str_pad($cuti['idcuti'], 3, '0', STR_PAD_LEFT) . '/CUTI/' . $bulanRomawi[$bulan] . '/' . $tahun;

    $data = [
        'title'       => 'Formulir Pengajuan Cuti',
        'cuti'        => $cuti,
        'pegawai'     => $pegawai,
        'pengganti'   => $pengganti,
        'sdi'         => $sdi,
        'nosurat'     => $nosurat,
        'tgl_cetak'   => date('d-m-Y'),
        'tgl_mulai'   => date('d-m-Y', strtotime($cuti['tgl_mulai'])),
        'tgl_selesai' => date('d-m-Y', strtotime($cuti['tgl_selesai'])),
    ];

    return view('admin/pengajuan/cetak_cuti', $data);
}

    // Batalkan cuti
    public function batalcuti($idcuti)
{
    checklogin();

    $db = \Config\Database::connect();
    $m_cuti = new \App\Models\Cuti_model(); // ini untuk cutihdr

    $cutihdr = $m_cuti->find($idcuti);
    if (!$cutihdr) {
        return redirect()->back()->with('error', 'Cuti tidak ditemukan.');
    }

    // Mulai transaksi
    $db->transStart();

    // Hapus data detail dari tabel `cuti`
    $db->table('cuti')->where('idcuti', $idcuti)->delete();

    // Hapus data header dari tabel `cutihdr` melalui model
    $m_cuti->delete($idcuti);

    // Selesaikan transaksi
    $db->transComplete();

    if ($db->transStatus() === false) {
        return redirect()->back()->with('error', 'Gagal membatalkan cuti.');
    }

    return redirect()->back()->with('sukses', 'Cuti berhasil dibatalkan.');
}



    public function izin($pegawai_pin)
    {
        checklogin();
        $m_izin = new Izin_model();
        $m_pegawai = new Pegawai_model();

        if ($this->request->getMethod() === 'post' && $this->validate([
            'pegawai_pin' => 'required',
            'tanggal' => 'required',
            'alasan' => 'required',
        ])) {
            $data = [
                'pegawai_pin' => $this->request->getPost('pegawai_pin'),
                'tglizin' => $this->request->getPost('tanggal'),
                'alasan' => $this->request->getPost('alasan'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $m_izin->insert($data);
            session()->setFlashdata('sukses', 'Izin berhasil diajukan.');
            return redirect()->to(base_url('admin/pengajuan/izin'));
        }

        if (!empty($pegawai_pin)) {
            // Ambil daftar cuti dalam tahun berjalan
            $tahun_sekarang = date('Y');
            $daftar_izin = $m_izin->where('pegawai_pin', $pegawai_pin)
                ->where('YEAR(tglizin)', $tahun_sekarang)
                ->orderBy('tglizin', 'DESC')
                ->findAll();
        }

        $data = [
            'title' => 'Ajukan Izin Pegawai',
            'pegawai'          => $m_pegawai->findAll(),
            'selected_pegawai' => $pegawai_pin,
            'daftar_izin' => $daftar_izin,
            'content' => 'admin/pengajuan/izin',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function lembur($pegawai_pin = null)
    {
        checklogin();
        $m_lembur = new Lembur_model();
        $m_pegawai = new Pegawai_model();

        if ($this->request->getMethod() === 'post' && $this->validate([
            'pegawai_pin' => 'required',
            'tanggal' => 'required',
            'alasan' => 'required',
        ])) {
            $data = [
                'pegawai_pin' => $this->request->getPost('pegawai_pin'),
                'tgllembur' => $this->request->getPost('tanggal'),
                'alasan' => $this->request->getPost('alasan'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $m_lembur->insert($data);
            session()->setFlashdata('sukses', 'Lembur berhasil diajukan.');
            return redirect()->to(base_url('admin/pengajuan/lembur'));
        }

        if (!empty($pegawai_pin)) {
            $tahun_sekarang = date('Y');
            $daftar_lembur = $m_lembur->where('pegawai_pin', $pegawai_pin)
                ->where('YEAR(tgllembur)', $tahun_sekarang)
                ->orderBy('tgllembur', 'DESC')
                ->findAll();
        }

        $data = [
            'title' => 'Ajukan Lembur Pegawai',
            'selected_pegawai' => $pegawai_pin,
            'pegawai' => $m_pegawai->findAll(),
            'daftar_lembur' => $daftar_lembur,
            'content' => 'admin/pengajuan/lembur',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function tugasluar($pegawai_pin = null)
    {
        checklogin();
        $m_tugasluar = new Tugasluar_model();
        $m_pegawai = new Pegawai_model();

        if ($this->request->getMethod() === 'post' && $this->validate([
            'pegawai_pin' => 'required',
            'tanggal' => 'required',
            'alasan' => 'required',
        ])) {
            $data = [
                'pegawai_pin' => $this->request->getPost('pegawai_pin'),
                'tgltugasluar' => $this->request->getPost('tanggal'),
                'alasan' => $this->request->getPost('alasan'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $m_tugasluar->insert($data);
            session()->setFlashdata('sukses', 'Tugas luar berhasil diajukan.');
            return redirect()->to(base_url('admin/pengajuan/tugasluar'));
        }

        if (!empty($pegawai_pin)) {
            $tahun_sekarang = date('Y');
            $daftar_tugasluar = $m_tugasluar->where('pegawai_pin', $pegawai_pin)
                ->where('YEAR(tgltugasluar)', $tahun_sekarang)
                ->orderBy('tgltugasluar', 'DESC')
                ->findAll();
        }

        $data = [
            'title' => 'Ajukan Tugas Luar Pegawai',
            'selected_pegawai' => $pegawai_pin,
            'pegawai' => $m_pegawai->findAll(),
            'daftar_tugasluar' => $daftar_tugasluar,
            'content' => 'admin/pengajuan/tugasluar',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function simpanizin()
    {
        checklogin();
        $m_izin = new Izin_model();
    
        if ($this->request->getMethod() === 'post' && $this->validate([
            'pegawai_pin' => 'required',
            'tglizin' => 'required',
            'alasan' => 'required',
        ])) {
            $pegawai_pin = $this->request->getPost('pegawai_pin');
            $tglizin = $this->request->getPost('tglizin');
            $alasan = $this->request->getPost('alasan');
    
            // Validasi dan konversi format tanggal
            $tglizin_obj = DateTime::createFromFormat('m/d/Y', $tglizin);
            if ($tglizin_obj) {
                $tglizin = $tglizin_obj->format('Y-m-d');
            } else {
                session()->setFlashdata('error', 'Format tanggal tidak valid.');
                return redirect()->to(base_url('admin/izins'))->withInput();
            }
    
            $data = [
                'pegawai_pin' => $pegawai_pin,
                'tglizin' => $tglizin,
                'alasan' => $alasan,
                'created_at' => date('Y-m-d H:i:s'),
            ];
    
            $m_izin->insert($data);
            session()->setFlashdata('sukses', 'Izin berhasil diajukan.');
            return redirect()->to(base_url("admin/pengajuan/izin/{$pegawai_pin}"));
        }
    
        session()->setFlashdata('error', 'Data tidak valid. Periksa kembali input Anda.');
        return redirect()->to(base_url('admin/izins'))->withInput();
    }
    

    public function cetak_izin($idizin)
    {
        checklogin();
        $m_izin = new Izin_model();
        $izin = $m_izin->find($idizin);
        if (!$izin) {
            return redirect()->back()->with('error', 'Data izin tidak ditemukan.');
        }
        $data = ['title' => 'Surat Izin Pegawai', 'izin' => $izin];
        return view('admin/pengajuan/cetak_izin', $data);
    }

    public function batalizin($idizin)
    {
        checklogin();
        $m_izin = new Izin_model();
        if (!$m_izin->find($idizin)) {
            return redirect()->back()->with('error', 'Izin tidak ditemukan.');
        }
        $m_izin->delete($idizin);
        return redirect()->back()->with('sukses', 'Izin berhasil dibatalkan.');
    }

    public function simpanlembur()
{
    checklogin();
    $m_lembur = new Lembur_model();

    if ($this->request->getMethod() === 'post' && $this->validate([
        'pegawai_pin'    => 'required',
        'tanggal_lembur' => 'required',
        'alasan'         => 'required',
    ])) {
        $pegawai_pin    = $this->request->getPost('pegawai_pin');
        $tanggal_lembur = $this->request->getPost('tanggal_lembur');
        $alasan         = $this->request->getPost('alasan');

        // Ubah format dari MM/DD/YYYY ke Y-m-d
        $tanggal_obj = DateTime::createFromFormat('m/d/Y', $tanggal_lembur);
        if ($tanggal_obj) {
            $tanggal_lembur = $tanggal_obj->format('Y-m-d');
        } else {
            session()->setFlashdata('error', 'Format tanggal tidak valid.');
            return redirect()->to(base_url('admin/pengajuan/lembur'))->withInput();
        }

        $data = [
            'pegawai_pin' => $pegawai_pin,
            'tgllembur'   => $tanggal_lembur,
            'alasan'      => $alasan,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $m_lembur->insert($data);
        session()->setFlashdata('sukses', 'Lembur berhasil diajukan.');
        return redirect()->to(base_url("admin/pengajuan/lembur/{$pegawai_pin}"));
    }

    session()->setFlashdata('error', 'Data tidak valid.');
    return redirect()->to(base_url('admin/pengajuan/lembur'))->withInput();
}


    public function cetak_lembur($idlembur)
    {
        checklogin();
        $m_lembur = new Lembur_model();
        $lembur = $m_lembur->find($idlembur);
        if (!$lembur) {
            return redirect()->back()->with('error', 'Data lembur tidak ditemukan.');
        }
        $data = ['title' => 'Surat Lembur Pegawai', 'lembur' => $lembur];
        return view('admin/pengajuan/cetak_lembur', $data);
    }

    public function batallembur($idlembur)
    {
        checklogin();
        $m_lembur = new Lembur_model();
        if (!$m_lembur->find($idlembur)) {
            return redirect()->back()->with('error', 'Lembur tidak ditemukan.');
        }
        $m_lembur->delete($idlembur);
        return redirect()->back()->with('sukses', 'Lembur berhasil dibatalkan.');
    }

    // Proses untuk tugas luar
    public function simpantugasluar()
{
    checklogin();
    $m_tugasluar = new TugasLuar_model();

    if ($this->request->getMethod() === 'post' && $this->validate([
        'pegawai_pin'    => 'required',
        'tanggal_tugas'  => 'required',
        'alasan'         => 'required',
    ])) {
        $pegawai_pin   = $this->request->getPost('pegawai_pin');
        $tanggal_tugas = $this->request->getPost('tanggal_tugas');
        $alasan        = $this->request->getPost('alasan');

        // Konversi format tanggal dari MM/DD/YYYY ke Y-m-d
        $tanggal_obj = DateTime::createFromFormat('m/d/Y', $tanggal_tugas);
        if ($tanggal_obj) {
            $tanggal_tugas = $tanggal_obj->format('Y-m-d');
        } else {
            session()->setFlashdata('error', 'Format tanggal tidak valid.');
            return redirect()->to(base_url('admin/pengajuan/tugasluar'))->withInput();
        }

        $data = [
            'pegawai_pin'  => $pegawai_pin,
            'tgltugasluar' => $tanggal_tugas,
            'alasan'       => $alasan,
            'created_at'   => date('Y-m-d H:i:s'),
        ];

        $m_tugasluar->insert($data);
        session()->setFlashdata('sukses', 'Tugas luar berhasil diajukan.');
        return redirect()->to(base_url("admin/pengajuan/tugasluar/{$pegawai_pin}"));
    }

    session()->setFlashdata('error', 'Data tidak valid.');
    return redirect()->to(base_url('admin/pengajuan/tugasluar'))->withInput();
}


    public function cetak_tugasluar($idtugasluar)
    {
        checklogin();
        $m_tugasluar = new Tugasluar_model();
        $tugasluar = $m_tugasluar->find($idtugasluar);
        if (!$tugasluar) {
            return redirect()->back()->with('error', 'Data tugas luar tidak ditemukan.');
        }
        $data = ['title' => 'Surat Tugas Luar Pegawai', 'tugasluar' => $tugasluar];
        return view('admin/pengajuan/cetak_tugasluar', $data);
    }

    public function bataltugasluar($idtugasluar)
    {
        checklogin();
        $m_tugasluar = new Tugasluar_model();
        if (!$m_tugasluar->find($idtugasluar)) {
            return redirect()->back()->with('error', 'Tugas luar tidak ditemukan.');
        }
        $m_tugasluar->delete($idtugasluar);
        return redirect()->back()->with('sukses', 'Tugas luar berhasil dibatalkan.');
    }

    public function rujukan($pegawai_pin = null)
{
    checklogin(); // pastikan user sudah login
    $m_rujukan = new \App\Models\Rujukan_model(); // Model Rujukan
    $m_pegawai = new \App\Models\Pegawai_model(); // Model Pegawai (jika perlu)

    // Proses simpan data rujukan via POST
    if ($this->request->getMethod() === 'post' && $this->validate([
        'pegawai_pin' => 'required',
        'tanggal'     => 'required',
        'keterangan'  => 'required',
    ])) {
        $data = [
            'pegawai_pin' => $this->request->getPost('pegawai_pin'),
            'tglrujukan'  => $this->request->getPost('tanggal'),
            'keterangan'  => $this->request->getPost('keterangan'),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $m_rujukan->insert($data);
        session()->setFlashdata('sukses', 'Rujukan berhasil disimpan.');
        return redirect()->to(base_url('admin/pengajuan/rujukan'));
    }

    // Ambil daftar rujukan dalam tahun berjalan
    $daftar_rujukan = [];
    if (!empty($pegawai_pin)) {
        $tahun_sekarang = date('Y');
        $daftar_rujukan = $m_rujukan->where('pegawai_pin', $pegawai_pin)
            ->where('YEAR(tglrujukan)', $tahun_sekarang)
            ->orderBy('tglrujukan', 'DESC')
            ->findAll();
    }

    $data = [
        'title'           => 'Pengajuan Rujukan',
        'pegawai_pin'     => $pegawai_pin,
        'daftar_rujukan'  => $daftar_rujukan,
    ];

    return view('admin/pengajuan/rujukan', $data);
}


}
