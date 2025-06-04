<?php namespace App\Controllers\Admin;

use App\Models\JadwalDokterModel;

class Jadwaldokter extends BaseController
{
    public function index()
    {
        checklogin();
        $db = \Config\Database::connect('webrs');
        $model = new JadwalDokterModel();
        $jadwal = $model->getJadwal();

        $data = [
            'title'   => 'Jadwal Dokter',
            'jadwal'  => $jadwal,
            'content' => 'admin/jadwaldokter/index',
        ];
        echo view('admin/layout/wrapper', $data);
    }

    public function edit($id_jadwal)
    {
        checklogin();
        $db = \Config\Database::connect('webrs');
        $model = new JadwalDokterModel();
        $jadwal = $model->getDetail($id_jadwal);

        if (!$jadwal) {
            session()->setFlashdata('error', 'Jadwal tidak ditemukan');
            return redirect()->to(base_url('admin/jadwaldokter'));
        }

        if ($this->request->getMethod() === 'post') {
            if ($this->validate([
                'jam_mulai'   => 'required',
                'jam_selesai' => 'required',
                'status'      => 'required|in_list[0,1]',
            ])) {
                $data = [
                    'jam_mulai'   => $this->request->getPost('jam_mulai'),
                    'jam_selesai' => $this->request->getPost('jam_selesai'),
                    'status'      => $this->request->getPost('status'),
                ];

                $model->update($id_jadwal, $data);
                session()->setFlashdata('sukses', 'Jadwal berhasil diperbarui');
                return redirect()->to(base_url('admin/jadwaldokter'));
            }
        }

        $data = [
            'title'   => 'Edit Jadwal: ' . esc($jadwal['nama_dokter']),
            'jadwal'  => $jadwal,
            'content' => 'admin/jadwaldokter/edit',
        ];
        echo view('admin/layout/wrapper', $data);
    }

    public function update()
    {
        checklogin();

        $id = $this->request->getPost('id_jadwal');
        $data = [
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'status'     => $this->request->getPost('status'),
        ];

        $model = new \App\Models\JadwalDokterModel();
        $model->update($id, $data);

        session()->setFlashdata('sukses', 'Jadwal berhasil diperbarui.');
        return redirect()->to(base_url('admin/jadwaldokter'));
    }

}
