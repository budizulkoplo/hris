<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Masterpegawai_model;

class Masterpegawai extends BaseController
{
    public function index()
    {
        checklogin();

        $m_pegawai = new Masterpegawai_model();
        $data = [
            'title'   => 'Data Master Pegawai',
            'pegawai' => $m_pegawai->orderBy('pegawai_nama', 'ASC')->findAll(),
            'content' => 'admin/masterpegawai/index',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function input()
    {
        if ($this->request->getMethod() === 'post') {
            $model = new Masterpegawai_model();

            // Ambil data dari form
            $data = $this->request->getPost([
                'pegawai_nip', 'nik', 'pegawai_nama', 'jabatan', 'nohp', 'email', 'alamat', 'bagian'
            ]);

            // Simpan data awal tanpa pegawai_pin
            $model->insert($data);
            $id = $model->getInsertID(); // ambil ID terakhir

            // Update pegawai_pin agar sama dengan pegawai_id
            $model->update($id, ['pegawai_pin' => $id]);

            return redirect()->to(base_url('admin/masterpegawai'))->with('sukses', 'Data pegawai berhasil ditambahkan.');
        }

        $data = [
            'title'   => 'Input Data Pegawai',
            'content' => 'admin/masterpegawai/input'
        ];
        echo view('admin/layout/wrapper', $data);
    }

    public function simpan()
    {
        $m_pegawai = new Masterpegawai_model();

        $data = [
            'pegawai_nip'   => $this->request->getPost('pegawai_nip'),
            'nik'           => $this->request->getPost('nik'),
            'pegawai_nama'  => $this->request->getPost('pegawai_nama'),
            'jabatan'       => $this->request->getPost('jabatan'),
            'nohp'          => $this->request->getPost('nohp'),
            'email'         => $this->request->getPost('email'),
            'alamat'        => $this->request->getPost('alamat'),
            'bagian'        => $this->request->getPost('bagian'),
        ];

        $m_pegawai->insert($data);
        $id = $m_pegawai->getInsertID();
        $m_pegawai->update($id, ['pegawai_pin' => $id]);

        return redirect()->to('/admin/masterpegawai')->with('sukses', 'Data pegawai berhasil ditambahkan.');
    }

    public function edit($id)
    {
        checklogin();

        $m_pegawai = new Masterpegawai_model();
        $pegawai = $m_pegawai->find($id);

        if (!$pegawai) {
            return redirect()->to('/admin/masterpegawai')->with('gagal', 'Pegawai tidak ditemukan.');
        }

        $data = [
            'title'   => 'Edit Pegawai',
            'pegawai' => $pegawai,
            'content' => 'admin/masterpegawai/edit',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function update($id)
    {
        $m_pegawai = new Masterpegawai_model();

        $data = [
            'pegawai_nip'   => $this->request->getPost('pegawai_nip'),
            'nik'           => $this->request->getPost('nik'),
            'pegawai_nama'  => $this->request->getPost('pegawai_nama'),
            'jabatan'       => $this->request->getPost('jabatan'),
            'nohp'          => $this->request->getPost('nohp'),
            'email'         => $this->request->getPost('email'),
            'alamat'        => $this->request->getPost('alamat'),
            'bagian'        => $this->request->getPost('bagian'),
        ];

        $m_pegawai->update($id, $data);

        return redirect()->to('/admin/masterpegawai')->with('sukses', 'Data pegawai berhasil diperbarui.');
    }
}
