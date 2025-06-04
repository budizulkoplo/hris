<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Pegawai_model;
use App\Models\Kalender_model;

class Kalenderpegawai extends BaseController
{
    public function index()
    {
        checklogin();

        $pegawaiModel = new Pegawai_model();

        $data = [
            'title' => 'Kalender Absensi Pegawai',
            'pegawai' => $pegawaiModel->findAll(),
            'bulan' => date('Y-m'), // default bulan saat ini
            'dataKalender' => [],
            'pegawaiTerpilih' => null,
            'minggu' => [],
            'content' => 'admin/kalenderpegawai/index'
        ];

        return view('admin/layout/wrapper', $data);
    }

    public function lihat()
    {
        $pegawaiPin = $this->request->getPost('pegawai_pin');
        $bulan = $this->request->getPost('bulan'); // format YYYY-MM

        if (!$pegawaiPin || !$bulan) {
            return redirect()->to(base_url('admin/kalenderpegawai'));
        }

        $pegawaiModel = new Pegawai_model();
        $kalenderModel = new Kalender_model();

        $pegawai = $pegawaiModel->where('pegawai_pin', $pegawaiPin)->first();
        $rows = $kalenderModel->getDataKalender($pegawaiPin, $bulan);

        // Susun data per tanggal
        $dataKalender = [];
        foreach ($rows as $row) {
            $tgl = $row['tgl'];
            $dataKalender[$tgl] = $row; // per tanggal
        }

        $minggu = $this->generateKalenderBulan($bulan);

        $data = [
            'title' => 'Kalender Absensi Pegawai',
            'pegawai' => $pegawaiModel->findAll(),
            'pegawaiTerpilih' => $pegawai,
            'bulan' => $bulan,
            'dataKalender' => $dataKalender,
            'minggu' => $minggu,
            'content' => 'admin/kalenderpegawai/index'
        ];

        return view('admin/layout/wrapper', $data);
    }

private function generateKalenderBulan($bulan)
{
    // Buat tanggal awal: 26 bulan sebelumnya
    $start = new \DateTime(date('Y-m-d', strtotime($bulan . '-26 -1 month')));
    
    // Buat tanggal akhir: 25 bulan ini
    $end = new \DateTime(date('Y-m-d', strtotime($bulan . '-25')));

    // Geser ke hari Senin di minggu awal
    $start->modify('monday this week');
    // Geser ke hari Minggu di minggu akhir
    $end->modify('sunday this week');

    $interval = new \DateInterval('P1D');
    $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));

    $weeks = [];
    $week = [];

    foreach ($period as $date) {
        $week[] = $date->format('Y-m-d');
        if (count($week) === 7) {
            $weeks[] = $week;
            $week = [];
        }
    }

    return $weeks;
}

}
