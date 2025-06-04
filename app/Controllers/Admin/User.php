<?php

namespace App\Controllers\Admin;

use App\Models\User_model;
use App\Models\LevelModel;

class User extends BaseController
{
    // mainpage
    public function index()
{
    checklogin();
    $m_user = new User_model();
    $user   = $m_user->listing();
    $m_level = new LevelModel();
    $total  = $m_user->total();
    $levels = $m_level->findAll();

    // Start validasi
    if ($this->request->getMethod() === 'post' && $this->validate([
        'nama'        => 'required',
        'username'    => 'required|min_length[3]|is_unique[users.username]',
        'nik'         => 'required|numeric',
        'nohp'        => 'required|numeric',
        'email'       => 'required|valid_email',
        'password'    => 'required|min_length[5]|max_length[32]', // Validasi password
    ])) {
        // Menyimpan data
        $data = [
            'nama'        => esc($this->request->getPost('nama')),
            'email'       => esc($this->request->getPost('email')),
            'username'    => esc($this->request->getPost('username')),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nik'         => esc($this->request->getPost('nik')),
            'alamat'      => esc($this->request->getPost('alamat')),
            'nohp'        => esc($this->request->getPost('nohp')),
            'keterangan'  => esc($this->request->getPost('keterangan')),
            'akses_level' => esc($this->request->getPost('akses_level')),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        // Menyimpan ke database
        $m_user->save($data);
        $this->session->setFlashdata('sukses', 'Data telah ditambah');
        return redirect()->to(base_url('admin/user'));
    }

    // Menampilkan halaman dengan data pengguna
    $data = [
        'title'   => 'Pengguna Website: ' . $total['total'],
        'levels'  => $levels,
        'user'    => $user,
        'content' => 'admin/user/index',
    ];
    echo view('admin/layout/wrapper', $data);
}


    // edit
    public function edit($id_user)
    {
        checklogin();

        $m_user = new User_model();
        $m_level = new LevelModel(); // Pastikan ada LevelModel untuk mengambil data level
        $user = $m_user->detail($id_user);

        if (!$user) {
            $this->session->setFlashdata('error', 'Data pengguna tidak ditemukan.');
            return redirect()->to(base_url('admin/user'));
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'nama' => 'required|min_length[3]',
                'email' => 'required|valid_email',
                'nik' => 'required|numeric',
                'nohp' => 'required|numeric',
                'akses_level' => 'required',
            ];

            if (!empty($this->request->getPost('password'))) {
                $rules['password'] = 'min_length[5]|max_length[32]';
            }

            if ($this->validate($rules)) {
                $data = [
                    'nama'        => $this->request->getPost('nama'),
                    'email'       => $this->request->getPost('email'),
                    'username'    => $this->request->getPost('username'),
                    'nik'         => $this->request->getPost('nik'),
                    'alamat'      => $this->request->getPost('alamat'),
                    'nohp'        => $this->request->getPost('nohp'),
                    'keterangan'  => $this->request->getPost('keterangan'),
                    'akses_level' => $this->request->getPost('akses_level'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                if (!empty($this->request->getPost('password'))) {
                    $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                }

                $m_user->update($id_user, $data);
                $this->session->setFlashdata('sukses', 'Data pengguna telah berhasil diperbarui.');
                return redirect()->to(base_url('admin/user'));
            } else {
                $this->session->setFlashdata('error', 'Terdapat kesalahan dalam pengisian form.');
            }
        }

        $levels = $m_level->findAll();

        $data = [
            'title'   => 'Edit Pengguna: ' . esc($user['nama']),
            'user'    => $user,
            'levels'  => $levels,
            'content' => 'admin/user/edit',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    // delete
    public function delete($id_user)
    {
        checklogin();
        $m_user = new User_model();
        $data   = ['id_user' => $id_user];
        $m_user->delete($data);

        $this->session->setFlashdata('sukses', 'Data telah dihapus');
        return redirect()->to(base_url('admin/user'));
    }
}
