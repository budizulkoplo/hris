<?php

namespace App\Controllers\Admin;

use App\Models\Kajian_model;

class Kajian extends BaseController
{
    public function index()
    {
        checklogin();

        $m_kajian = new Kajian_model();

        $data = [
            'title'   => 'Data Kajian',
            'kajian'  => $m_kajian->orderBy('tanggal', 'DESC')->findAll(),
            'content' => 'admin/kajian/index',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function tambah()
{
    checklogin();

    $m_kajian = new Kajian_model();

    if ($this->request->getMethod() === 'post' && $this->validate([
        'namakajian' => 'required',
        'tanggal'    => 'required',
        'lokasi'     => 'required',
    ])) {
        // Buat kode unik untuk qrcode, misalnya 8 digit acak
        helper('text');
        $kode_qr = random_string('alnum', 8);

        $data = [
            'namakajian' => $this->request->getPost('namakajian'),
            'tanggal'    => $this->request->getPost('tanggal'),
            'lokasi'     => $this->request->getPost('lokasi'),
            'qrcode'     => $kode_qr,
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        $m_kajian->insert($data);
        session()->setFlashdata('sukses', 'Data Kajian berhasil ditambahkan.');
        return redirect()->to(base_url('admin/kajian'));
    }

    $data = [
        'title'   => 'Tambah Kajian',
        'content' => 'admin/kajian/tambah',
    ];

    echo view('admin/layout/wrapper', $data);
}


    public function edit($idkajian)
    {
        checklogin();

        $m_kajian = new Kajian_model();
        $kajian   = $m_kajian->find($idkajian);

        if (!$kajian) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Kajian tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post' && $this->validate([
            'namakajian' => 'required',
            'tanggal'    => 'required',
            'lokasi'     => 'required',
        ])) {
            $data = [
                'namakajian' => $this->request->getPost('namakajian'),
                'tanggal'    => $this->request->getPost('tanggal'),
                'lokasi'     => $this->request->getPost('lokasi'),
                'qrcode'     => $this->request->getPost('qrcode'),
                'keterangan' => $this->request->getPost('keterangan'),
            ];

            $m_kajian->update($idkajian, $data);
            session()->setFlashdata('sukses', 'Data Kajian berhasil diperbarui.');
            return redirect()->to(base_url('admin/kajian'));
        }

        $data = [
            'title'  => 'Edit Kajian',
            'kajian' => $kajian,
            'content'=> 'admin/kajian/edit',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function delete($idkajian)
    {
        checklogin();

        $m_kajian = new Kajian_model();

        if (!$m_kajian->find($idkajian)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Kajian tidak ditemukan.');
        }

        $m_kajian->delete($idkajian);
        session()->setFlashdata('sukses', 'Data Kajian berhasil dihapus.');
        return redirect()->to(base_url('admin/kajian'));
    }

public function qrcode($idkajian)
{
    checklogin();

    // Ambil data kajian
    $m_kajian = new Kajian_model();
    $kajian = $m_kajian->find($idkajian);

    if (!$kajian) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Kajian tidak ditemukan.');
    }

    $db = \Config\Database::connect();
    $builder = $db->table('kehadiran_kajian');

    $current_date = date('Ymd'); // Format YYYYMMDD

    $last_scan = $builder
        ->select('barcodeuniq')
        ->like('barcodeuniq', $idkajian.$current_date, 'after') // LIKE 'YYYYMMDD%'
        ->where('idkajian', $idkajian)
        ->orderBy('barcodeuniq', 'DESC')
        ->limit(1)
        ->get()
        ->getRow();

    $last_number = $last_scan ? (int) substr($last_scan->barcodeuniq, 9) : 0;
    $next_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT); // 0001, 0002, dst

    $barcodeuniq = $idkajian . $current_date . $next_number;


    $qr_link = base_url("https://mobile.rspkuboja.com/kehadiran/{$idkajian}/{$barcodeuniq}");

    $data = [
        'title'   => 'QR Code Kehadiran Kajian',
        'kajian'  => $kajian,
        'barcode'  => $barcodeuniq,
        'qr_link' => $qr_link,
    ];

    return view('admin/kajian/qrcode', $data);
}

    public function kehadiran($idkajian = null, $barcode = null)
{
    checklogin();
    $session = \Config\Services::session();

    if (!$idkajian || !$barcode) {
        return redirect()->to(base_url('admin/kajian/formScan'))
            ->with('error', 'Parameter kode QR tidak lengkap.');
    }

    $mKajian = new Kajian_model();
    $kajian = $mKajian->find($idkajian);

    if (!$kajian) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Kajian tidak ditemukan.');
    }

    $db = \Config\Database::connect();
    $builder = $db->table('kehadiran_kajian');

    // Cek apakah barcode sudah dipakai (duplikat barcode)
    $cekBarcode = $builder->where([
        'idkajian'    => $idkajian,
        'barcodeuniq' => $barcode,
    ])->get()->getRow();

    if ($cekBarcode) {
        $data = [
            'title'    => 'Barcode Sudah Digunakan',
            'kajian'   => $kajian,
            'barcode'  => $barcode,
            'duplikat' => true,
            'pesan'    => 'Barcode Sudah di Scan, Mohon Scan kembali dengan benar!',
            'content'  => 'admin/kajian/kehadiran',
        ];
        return view('admin/layout/wrapper', $data);
    }

    $nik = $session->get('username');
    $cekNik = $builder->where([
        'idkajian' => $idkajian,
        'nik'      => $nik,
    ])
    ->where('DATE(waktu_scan)', date('Y-m-d'))
    ->get()
    ->getRow();


    if ($cekNik) {
        $data = [
            'title'    => 'Scan Sudah Dilakukan',
            'kajian'   => $kajian,
            'barcode'  => $barcode,
            'duplikat' => true,
            'pesan'    => 'Anda sudah melakukan scan kehadiran untuk kajian ini sebelumnya!',
            'content'  => 'admin/kajian/kehadiran',
        ];
        return view('admin/layout/wrapper', $data);
    }

    // Insert kehadiran baru
    $builder->insert([
        'idkajian'    => $idkajian,
        'barcodeuniq' => $barcode,
        'ip_address'  => $this->request->getIPAddress(),
        'user_agent'  => $this->request->getUserAgent()->getAgentString(),
        'waktu_scan'  => date('Y-m-d H:i:s'),
        'nik'         => $nik,
        'nama'        => $session->get('nama'),
    ]);

    $data = [
        'title'    => 'Kehadiran Tercatat',
        'kajian'   => $kajian,
        'barcode'  => $barcode,
        'duplikat' => false,
        'content'  => 'admin/kajian/kehadiran',
    ];
    return view('admin/layout/wrapper', $data);
}

public function formScan()
{
    checklogin();
    $data = [
        'title'   => 'Scan QR Kehadiran',
        'content' => 'admin/kajian/form_scan',
    ];

    echo view('admin/layout/wrapper', $data);
}

public function handleScanInput()
{
    $input = $this->request->getGet('scan_input'); // format: 5/202505280001

    if ($input && strpos($input, '/') !== false) {
        list($idkajian, $barcode) = explode('/', $input, 2);
        return redirect()->to(base_url("kajian/kehadiran/$idkajian/$barcode"));
    }

    session()->setFlashdata('error', 'Format QR tidak valid.');
    return redirect()->back();
}

public function cekScan($idkajian, $barcodeuniq)
{
    $db = \Config\Database::connect();
    $builder = $db->table('kehadiran_kajian');
    $cek = $builder->where([
        'idkajian' => $idkajian,
        'barcodeuniq' => $barcodeuniq
    ])->get()->getRow();

    return $this->response->setJSON([
        'sudah_scan' => $cek ? true : false
    ]);
}



}
