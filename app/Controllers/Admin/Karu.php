<?php

namespace App\Controllers\Admin;

use App\Models\Karu_model;
use App\Models\Kelompokkerja_model;
use App\Models\Pegawai_model;
use App\Models\Karupegawai_model;

class Karu extends BaseController
{
    public function index()
{
    checklogin();

    $session = session();
    $akses_level = $session->get('akses_level');
    $namaKaru = $session->get('nama'); // Ambil nama karu dari session

    $m_karu     = new Karu_model();
    $m_kelompok = new Kelompokkerja_model();
    $m_pegawai  = new Pegawai_model();

    $used_kelompok = array_column($m_karu->findAll(), 'idkelompokkerja');

    // Jika akses level = karu, maka hanya tampilkan data milik Karu yang login
    if ($akses_level === 'karu') {
        $m_karu->where('pegawai.pegawai_nama', $namaKaru);
    }

    $data = [
        'title'     => 'Data Kepala Regu (KARU)',
        'karu'      => $m_karu->select('karu.*, pegawai.pegawai_pin, pegawai.pegawai_nama, kelompokkerja.namakelompok')
                            ->join('pegawai', 'pegawai.pegawai_pin = karu.pin')
                            ->join('kelompokkerja', 'kelompokkerja.idkelompokkerja = karu.idkelompokkerja')
                            ->findAll(),
        'kelompok'  => $m_kelompok->findAll(),
        'pegawai'   => $m_pegawai->findAll(),
        'used_kelompok'=> $used_kelompok,
        'content'   => 'admin/karu/index',
    ];

    echo view('admin/layout/wrapper', $data);
}


    public function tambah()
    {
        checklogin();

        $m_karu = new Karu_model();

        if ($this->request->getMethod() === 'post' && $this->validate([
            'idkelompokkerja' => 'required',
            'pin'             => 'required',
        ])) {
            $data = [
                'idkelompokkerja' => $this->request->getPost('idkelompokkerja'),
                'pin'             => $this->request->getPost('pin'),
                'nama'            => $this->request->getPost('nama_karu'),
            ];

            $m_karu->insert($data);
            $this->session->setFlashdata('sukses', 'Data KARU berhasil ditambahkan.');
            return redirect()->to(base_url('admin/karu'));
        }

        return redirect()->to(base_url('admin/karu'))->with('error', 'Gagal menambahkan KARU.');
    }

    public function edit($idkaru)
{
    checklogin();

    $m_karu     = new Karu_model();
    $m_kelompok = new Kelompokkerja_model();
    $m_pegawai  = new Pegawai_model();

    $karu = $m_karu->find($idkaru);
    if (!$karu) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Kepala Regu tidak ditemukan.');
    }

    if ($this->request->getMethod() === 'post' && $this->validate([
        'idkelompokkerja' => 'required',
        'pin'             => 'required',
        'nama'            => 'required',
    ])) {
        $data = [
            'idkelompokkerja' => $this->request->getPost('idkelompokkerja'),
            'pin'             => $this->request->getPost('pin'),
            'nama'            => $this->request->getPost('nama'),
        ];

        $m_karu->update($idkaru, $data);
        session()->setFlashdata('sukses', 'Data KARU berhasil diperbarui.');
        return redirect()->to(base_url('admin/karu'));
    }

    $data = [
        'title'         => 'Edit Kepala Regu',
        'karu'          => $karu,
        'pegawai'   => $m_pegawai->findAll(),
        'kelompok'      => $m_kelompok->findAll(),
        'used_kelompok' => array_column($m_karu->findAll(), 'idkelompokkerja'),
        'content'       => 'admin/karu/edit',
    ];

    echo view('admin/layout/wrapper', $data);
}


    public function delete($idkaru)
    {
        checklogin();

        $m_karu = new Karu_model();

        if (!$m_karu->find($idkaru)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Kepala Regu tidak ditemukan.');
        }

        $m_karu->delete($idkaru);
        $this->session->setFlashdata('sukses', 'Data KARU berhasil dihapus.');
        return redirect()->to(base_url('admin/karu'));
    }

    public function tambahPegawai($idkaru)
{
    checklogin();

    $m_pegawai = new Pegawai_model();
    $m_karu = new Karu_model();
    $m_kelompokkerja = new Kelompokkerja_model();
    $m_Karupegawai = new Karupegawai_model(); 

    // Ambil data KARU dan gabungkan dengan informasi kelompok kerja
    $karu = $m_karu->select('karu.*, kelompokkerja.namakelompok')
        ->join('kelompokkerja', 'kelompokkerja.idkelompokkerja = karu.idkelompokkerja', 'left')
        ->where('karu.idkaru', $idkaru)
        ->first();

    if (!$karu) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Kepala Regu tidak ditemukan.');
    }

    if ($this->request->getMethod() === 'post') {
        $idpegawai = $this->request->getPost('idpegawai');

        if ($idpegawai) {
            // Cek apakah pegawai sudah ada di kelompok kerja
            $existing = $m_Karupegawai->where(['idkaru' => $idkaru, 'pegawai_pin' => $idpegawai])->first();
            
            if ($existing) {
                session()->setFlashdata('error', 'Pegawai sudah ada di Kelompok Kerja.');
            } else {
                $m_Karupegawai->insert([
                    'idkaru' => $idkaru,
                    'pegawai_pin' => $idpegawai
                ]);

                session()->setFlashdata('sukses', 'Pegawai berhasil ditambahkan ke Kelompok Kerja.');
                return redirect()->to(base_url('admin/karu/tambahPegawai/' . $idkaru));
            }
        } else {
            session()->setFlashdata('error', 'Pilih pegawai terlebih dahulu.');
        }
    }

    // Ambil daftar pegawai yang sudah ada dalam kelompok kerja
    $Karupegawai = $m_Karupegawai->select('karupegawai.*, pegawai.pegawai_nama')
        ->join('pegawai', 'pegawai.pegawai_pin = karupegawai.pegawai_pin')
        ->where('karupegawai.idkaru', $idkaru)
        ->findAll();

    // Ambil semua pegawai untuk pilihan dropdown
    $pegawai = $m_pegawai->select('*')
    ->whereNotIn('bagian', ['nonaktif'])
    ->findAll();

    // Siapkan data untuk dikirim ke view
    $data = [
        'title'       => 'Tambah Pegawai ke Kelompok Kerja',
        'idkaru'      => $idkaru,
        'karu'        => $karu, // Data KARU + Kelompok Kerja
        'pegawai'     => $pegawai,
        'Karupegawai' => $Karupegawai,
        'idkaru' => $idkaru,
        'content'     => 'admin/karu/tambah_pegawai',
    ];

    echo view('admin/layout/wrapper', $data);
}

public function hapusPegawai($idkarupegawai)
{
    checklogin();

    $m_Karupegawai = new Karupegawai_model();

    // Cek apakah data pegawai dalam kelompok kerja ada
    $karuPegawai = $m_Karupegawai->find($idkarupegawai);
    if (!$karuPegawai) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Pegawai dalam Kelompok Kerja tidak ditemukan.');
    }

    // Hapus pegawai dari kelompok kerja
    $m_Karupegawai->delete($idkarupegawai);

    session()->setFlashdata('sukses', 'Pegawai berhasil dihapus dari Kelompok Kerja.');
    
    // Redirect kembali ke halaman tambahPegawai dengan idkaru yang sesuai
    return redirect()->to(base_url('admin/karu/tambahPegawai/' . $karuPegawai['idkaru']));
}


}
