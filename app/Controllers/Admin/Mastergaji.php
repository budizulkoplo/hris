<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Mastergaji_model;

class Mastergaji extends BaseController
{
    public function index()
{
    checklogin();

    $m_mastergaji = new Mastergaji_model();

    $data = [
        'title'      => 'Data Master Gaji',
        'mastergaji' => $m_mastergaji->getPegawaiWithGajiStatus(),
        'content'    => 'admin/mastergaji/index',
    ];

    echo view('admin/layout/wrapper', $data);
}


public function input($pegawai_pin)
{
    $m_pegawai = new \App\Models\Pegawai_model();

    $pegawai = $m_pegawai->where('pegawai_pin', $pegawai_pin)->first();

    if (!$pegawai) {
        return redirect()->to('/admin/mastergaji')->with('gagal', 'Pegawai tidak ditemukan.');
    }

    $data = [
        'title'    => 'Input Gaji Pegawai',
        'pegawai'  => $pegawai,
        'content'  => 'admin/mastergaji/input',
    ];

    echo view('admin/layout/wrapper', $data);
}

public function edit($idgaji)
{
    $m_mastergaji = new \App\Models\Mastergaji_model();
    $gaji = $m_mastergaji->find($idgaji); // Mengambil data gaji berdasarkan idgaji

    if (!$gaji) {
        return redirect()->to('/admin/mastergaji')->with('gagal', 'Data gaji tidak ditemukan.');
    }

    // Ambil data pegawai berdasarkan pegawai_pin yang ada di tabel mastergaji
    $m_pegawai = new \App\Models\Pegawai_model();
    $pegawai = $m_pegawai->where('pegawai_pin', $gaji['pegawai_pin'])->first();

    // Pastikan pegawai ada
    if (!$pegawai) {
        return redirect()->to('/admin/mastergaji')->with('gagal', 'Pegawai tidak ditemukan.');
    }

    $data = [
        'title'      => 'Edit Gaji Pegawai',
        'gaji'       => $gaji,
        'pegawai'    => $pegawai,
        'content'    => 'admin/mastergaji/edit', // Tempatkan view untuk form edit
    ];

    echo view('admin/layout/wrapper', $data);
}

public function save()
{
    // Ambil data dari form
    $pegawai_pin = $this->request->getPost('pegawai_pin');
    $tglaktif = $this->request->getPost('tglaktif');
    $gajipokok = $this->request->getPost('gajipokok');
    $tunjstruktural = $this->request->getPost('tunjstruktural');
    $tunjfungsional = $this->request->getPost('tunjfungsional');
    $tunjkeluarga = $this->request->getPost('tunjkeluarga');
    $tunjapotek = $this->request->getPost('tunjapotek');
    $kehadiran = $this->request->getPost('kehadiran');
    $verifikasi = $this->request->getPost('verifikasi') ?? '0'; // Default '0' jika tidak dipilih

    // Validasi input (misalnya pastikan semua field ada isinya)
    if (!$gajipokok || !$tglaktif) {
        return redirect()->back()->with('gagal', 'Gaji pokok dan tanggal aktif harus diisi.');
    }

    // Siapkan data untuk disimpan ke tabel mastergaji
    $data = [
        'pegawai_pin'     => $pegawai_pin,
        'tglaktif'        => $tglaktif,
        'gajipokok'       => $gajipokok,
        'tunjstruktural'  => $tunjstruktural,
        'tunjfungsional'  => $tunjfungsional,
        'tunjkeluarga'    => $tunjkeluarga,
        'tunjapotek'      => $tunjapotek,
        'kehadiran'       => $kehadiran,
        'verifikasi'      => $verifikasi,
        'created_at'      => date('Y-m-d H:i:s'),
        'updated_at'      => date('Y-m-d H:i:s'),
    ];

    // Panggil model untuk menyimpan data ke database
    $m_mastergaji = new \App\Models\Mastergaji_model();
    $m_mastergaji->save($data);

    // Redirect ke halaman master gaji dengan pesan sukses
    return redirect()->to('/admin/mastergaji')->with('sukses', 'Gaji pegawai berhasil disimpan.');
}

public function update($idgaji)
{
    // Ambil data yang dikirim melalui form
    $pegawai_pin = $this->request->getPost('pegawai_pin');
    $gajipokok = $this->request->getPost('gajipokok');
    $tunjstruktural = $this->request->getPost('tunjstruktural');
    $tunjfungsional = $this->request->getPost('tunjfungsional');
    $tunjkeluarga = $this->request->getPost('tunjkeluarga');
    $tunjapotek = $this->request->getPost('tunjapotek');
    $kehadiran = $this->request->getPost('kehadiran');
    $tglaktif = $this->request->getPost('tglaktif');
    $verifikasi = $this->request->getPost('verifikasi') ?? '0'; // Default '0' jika tidak dipilih

    // Validasi input (misalnya pastikan semua field ada isinya)
    if (!$gajipokok) {
        return redirect()->back()->with('gagal', 'gaji pokok harus diisi.');
    }

    // Siapkan data untuk diperbarui di tabel mastergaji
    $data = [
        'tglaktif'        => $tglaktif,
        'gajipokok'       => $gajipokok,
        'tunjstruktural'  => $tunjstruktural,
        'tunjfungsional'  => $tunjfungsional,
        'tunjkeluarga'    => $tunjkeluarga,
        'tunjapotek'      => $tunjapotek,
        'kehadiran'       => $kehadiran,
        'verifikasi'      => $verifikasi,
        'updated_at'      => date('Y-m-d H:i:s'),
    ];

    // Panggil model untuk memperbarui data di database
    $m_mastergaji = new \App\Models\Mastergaji_model();
    $m_mastergaji->update($idgaji, $data);

    // Redirect ke halaman master gaji dengan pesan sukses
    return redirect()->to('/admin/mastergaji')->with('sukses', 'Gaji pegawai berhasil diperbarui.');
}


}
