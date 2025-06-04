<?php

namespace App\Controllers\Admin;

use App\Models\User_model;
use App\Models\Muzaki_model;

class Akun extends BaseController
{
    public function index()
{
    checklogin();

    $aksesLevel = $this->session->get('akses_level');
    $id_user    = $this->session->get('id_user');
    $username   = $this->session->get('username');

    if ($aksesLevel === 'pegawai') {
        $m_pegawai = new \App\Models\Pegawai_model();
        $user = $m_pegawai->select('pegawai_pin as id_user, pegawai_nama as nama, nik as username, password, nik, alamat, nohp, jabatan, email, bagian, "" as gambar, "" as keterangan')
                          ->where('nik', $username)
                          ->first();

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data pegawai tidak ditemukan.');
        }
    } else {
        $m_user = new \App\Models\User_model();
        $user = $m_user->detail($id_user);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data user tidak ditemukan.');
        }
    }

    if ($this->request->getMethod() === 'post' && $this->validate([
        'nama' => 'required',
    ])) {
        $data = [
            'pegawai_nama' => $this->request->getPost('nama'),
            'nik'          => $this->request->getPost('nik'),
            'alamat'       => $this->request->getPost('alamat'),
            'nohp'         => $this->request->getPost('nohp'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'email'        => $this->request->getPost('email')
        ];

        $password = $this->request->getPost('password');
        if (!empty($password) && strlen($password) >= 6 && strlen($password) <= 32) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($aksesLevel === 'pegawai') {
            $m_pegawai->update($user['id_user'], $data);
        } else {
            $m_user->update($id_user, $data);
        }

        $this->session->setFlashdata('sukses', 'Data telah diperbarui.');
        return redirect()->to(base_url('admin/akun'));
    }

    $data = [
        'title'      => 'Update Profile: ' . $user['nama'],
        'user'       => $user,
        'aksesLevel' => $aksesLevel,
        'content'    => 'admin/akun/index',
    ];

    echo view('admin/layout/wrapper', $data);
}

}
