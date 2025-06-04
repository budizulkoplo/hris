<?php

namespace App\Controllers;

use App\Models\Konfigurasi_model;
use App\Models\Login_model;

class Login extends BaseController
{
    public function index()
{
    $session = \Config\Services::session();
    $m_konfigurasi = new Konfigurasi_model();
    $m_login = new Login_model();  // Gunakan Login_model
    $konfigurasi = $m_konfigurasi->listing();

    // Start validasi
    if ($this->request->getMethod() === 'post' && $this->validate(
        [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[3]',
        ]
    )) {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $user = $m_login->login($username, $password);  // Panggil method login dari Login_model

        // Proses login
        if ($user) {
            // Jika username password benar
            $this->session->set('username', $username);
            $this->session->set('id_user', $user['id']);
            $this->session->set('akses_level', $user['akses_level']);
            $this->session->set('nama', $user['nama']);
            $this->session->setFlashdata('sukses', 'Hai ' . $user['nama'] . ', Anda berhasil login');
            $this->session->set('gambar', $user['foto']);  // Gunakan gambar/foto dari query
            $this->session->set('idranting', $user['idranting']);
            // Cek apakah ada parameter redirect
            $redirectUrl = $this->request->getPost('redirect') ? $this->request->getPost('redirect') : base_url('admin/dasbor');
            return redirect()->to($redirectUrl); // Arahkan ke halaman yang diinginkan setelah login
        }

        // jika username password salah
        $this->session->setFlashdata('warning', 'Username atau password salah');
        return redirect()->to(base_url('login'));
    }

    // End validasi
    $data = [
        'title' => 'Login Administrator',
        'description' => $konfigurasi['namaweb'] . ', ' . $konfigurasi['tentang'],
        'keywords' => $konfigurasi['namaweb'] . ', ' . $konfigurasi['keywords'],
        'session' => $session,
    ];
    echo view('login/index', $data);

    // End proses
}


    // lupa
    public function lupa()
    {
        $session       = \Config\Services::session();
        $m_konfigurasi = new Konfigurasi_model();
        $konfigurasi   = $m_konfigurasi->listing();

        $data = [
            'title'       => 'Lupa Password',
            'description' => $konfigurasi['namaweb'] . ', ' . $konfigurasi['tentang'],
            'keywords'    => $konfigurasi['namaweb'] . ', ' . $konfigurasi['keywords'],
            'session'     => $session,
        ];
        echo view('login/lupa', $data);
    }

    // Logout
    public function logout()
    {
        $this->session->destroy();

        return redirect()->to(base_url('login?logout=sukses'));
    }
}
