<?php
namespace App\Controllers\Admin;

use App\Models\Pegawai_model;
use App\Models\Potongan_model;

class Potongan extends BaseController
{
    public function index()
    {
        checklogin();

        $bulanTahun = $this->request->getGet('bulanTahun') ?? date('Y-m');
        $mPegawai   = new Pegawai_model();
        $mPotongan  = new Potongan_model();

        // Ambil daftar pegawai
        $pegawai = $mPegawai->findAll();

        // Ambil data potongan per periode
        $potongan = $mPotongan->getPotonganByPeriode($bulanTahun);

        $data = [
            'title'      => 'Input Potongan Gaji ' . $bulanTahun,
            'bulanTahun' => $bulanTahun,
            'pegawai'    => $pegawai,
            'potongan'   => $potongan,
            'content'    => 'admin/potongan/index'
        ];
        return view('admin/layout/wrapper', $data);
    }

    // Proses simpan (AJAX)
    public function save()
    {
        $mPotongan = new Potongan_model();
        $pegawai_pin = $this->request->getPost('pegawai_pin');
        $periode     = $this->request->getPost('periode');
        $field       = $this->request->getPost('field');
        $value       = $this->request->getPost('value');

        $mPotongan->savePotongan($pegawai_pin, $periode, $field, $value);

        return $this->response->setJSON(['status' => 'ok']);
    }
}
