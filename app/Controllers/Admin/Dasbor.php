<?php

namespace App\Controllers\Admin;

class Dasbor extends BaseController
{
    public function index()
{
    // Pastikan pengguna sudah login
    checklogin();

    // Ambil data session pengguna
    $session = \Config\Services::session();
    $namaUser = $session->get('nama');
    $aksesLevel = $session->get('akses_level');

    // Load model untuk dasbor dan rekening
    $m_dasbor = new \App\Models\Dasbor_model();

    // Ambil data dasbor
    $dataDasbor = [
        
        'user' => $m_dasbor->user(),
        
    ];

    // Gabungkan data untuk view
    $data = [
        'title'       => 'Dashboard Aplikasi',
        'namaUser'    => $namaUser,
        'aksesLevel'  => $aksesLevel,
        'dataDasbor'  => $dataDasbor,
        'content'     => 'admin/dasbor/index', // Path view
    ];

    // Render view dengan layout
    echo view('admin/layout/wrapper', $data);
}

}
