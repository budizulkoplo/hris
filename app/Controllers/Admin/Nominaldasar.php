<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Nominaldasar_model;

class Nominaldasar extends BaseController
{
    public function index()
    {
        checklogin();

        $model = new Nominaldasar_model();
        $data = [
            'title' => 'Edit Nominal Dasar',
            'nominal' => $model->first(), // ambil data tunggal
            'content' => 'admin/nominaldasar/index',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function update()
    {
        $model = new Nominaldasar_model();

        $data = [
            'rujukan'    => $this->request->getPost('rujukan'),
            'uangmakan'  => $this->request->getPost('uangmakan'),
            'bpjs'       => $this->request->getPost('bpjs'),
            'koperasi'   => $this->request->getPost('koperasi'),
        ];

        $model->update(1, $data); // karena hanya 1 record, ID diasumsikan 1

        return redirect()->to('/admin/nominaldasar')->with('sukses', 'Nominal dasar berhasil diperbarui.');
    }
}
