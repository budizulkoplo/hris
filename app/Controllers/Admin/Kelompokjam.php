<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Kelompokjam_model;

class Kelompokjam extends BaseController
{
    public function index()
    {
        checklogin();

        $model = new Kelompokjam_model();
        $data = [
            'title' => 'Kelompok Jam',
            'kelompokjam' => $model->findAll(),
            'content' => 'admin/kelompokjam/index'
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function tambah()
    {
        if ($this->request->getMethod() === 'post') {
            $model = new Kelompokjam_model();
            $model->save([
                'bagian' => $this->request->getPost('bagian'),
                'shift' => $this->request->getPost('shift'),
                'jammasuk' => $this->request->getPost('jammasuk'),
                'jampulang' => $this->request->getPost('jampulang')
            ]);
            session()->setFlashdata('sukses', 'Data berhasil ditambahkan');
            return redirect()->to(base_url('admin/kelompokjam'));
        }

        $data = [
            'title' => 'Tambah Kelompok Jam',
            'content' => 'admin/kelompokjam/tambah'
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function edit($id)
    {
        $model = new Kelompokjam_model();
        $kelompokjam = $model->find($id);

        if (!$kelompokjam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $model->update($id, [
                'bagian' => $this->request->getPost('bagian'),
                'shift' => $this->request->getPost('shift'),
                'jammasuk' => $this->request->getPost('jammasuk'),
                'jampulang' => $this->request->getPost('jampulang')
            ]);
            session()->setFlashdata('sukses', 'Data berhasil diperbarui');
            return redirect()->to(base_url('admin/kelompokjam'));
        }

        $data = [
            'title' => 'Edit Kelompok Jam',
            'kelompokjam' => $kelompokjam,
            'content' => 'admin/kelompokjam/edit'
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function hapus($id)
    {
        $model = new Kelompokjam_model();
        $model->delete($id);
        session()->setFlashdata('sukses', 'Data berhasil dihapus');
        return redirect()->to(base_url('admin/kelompokjam'));
    }
}
