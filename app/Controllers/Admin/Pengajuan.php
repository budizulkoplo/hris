<?php

namespace App\Controllers\Admin;

use App\Models\Cuti_model;
use App\Models\Izin_model;
use App\Models\Lembur_model;
use App\Models\Tugasluar_model;
use App\Models\Pegawai_model;
use DateTime;

class Pengajuan extends BaseController
{
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
                ->where('YEAR(tglcuti)', $tahun_sekarang)
                ->orderBy('tglcuti', 'DESC')
                ->findAll();

            // Hitung sisa cuti
            $cuti_diambil = count($daftar_cuti);
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

        if ($this->request->getMethod() === 'post' && $this->validate([
            'pegawai_pin'   => 'required',
            'tanggal_cuti'  => 'required',
            'alasan'        => 'required',
        ])) {

            $pegawai_pin   = $this->request->getPost('pegawai_pin');
            $tanggal_cuti  = $this->request->getPost('tanggal_cuti');
            $alasan        = $this->request->getPost('alasan');
            $idkaru        = $this->request->getPost('idkaru'); 

            $tanggal_cuti_obj = DateTime::createFromFormat('m/d/Y', $tanggal_cuti);
            if ($tanggal_cuti_obj) {
                $tanggal_cuti = $tanggal_cuti_obj->format('Y-m-d');
            } else {
                session()->setFlashdata('error', 'Format tanggal tidak valid.');
                return redirect()->to(base_url('admin/cuti'))->withInput();
            }

            $data = [
                'pegawai_pin'   => $pegawai_pin,
                'tglcuti'       => $tanggal_cuti,
                'alasancuti'    => $alasan,
                'created_at'    => date('Y-m-d H:i:s')
            ];

            $m_cuti->insert($data);
            session()->setFlashdata('sukses', 'Cuti berhasil diajukan.');

            return redirect()->to(base_url("admin/pengajuan/cuti/{$pegawai_pin}"));
        }

        session()->setFlashdata('error', 'Data tidak valid. Periksa kembali input Anda.');
        return redirect()->to(base_url('admin/cuti'))->withInput();
    }

    public function cetak_cuti($idcuti)
    {
        checklogin();

        $m_cuti = new Cuti_model();
        $cuti = $m_cuti->find($idcuti);

        if (!$cuti) {
            return redirect()->back()->with('error', 'Data cuti tidak ditemukan.');
        }

        $data = [
            'title' => 'Surat Cuti Pegawai',
            'cuti'  => $cuti,
        ];

        return view('admin/pengajuan/cetak_cuti', $data);
    }
    public function batalcuti($idcuti)
    {
        checklogin();
        $m_cuti = new Cuti_model();

        // Periksa apakah cuti ada
        $cuti = $m_cuti->find($idcuti);
        if (!$cuti) {
            return redirect()->back()->with('error', 'Cuti tidak ditemukan.');
        }

        // Hapus cuti dari database
        $m_cuti->delete($idcuti);

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
