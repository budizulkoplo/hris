<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Komponengaji_model;

class Komponengaji extends BaseController
{
    public function index()
{
    checklogin(); // validasi login

    $periode = $this->request->getGet('periode') ?? date('Y-m');
    $model   = new Komponengaji_model();

    // Ambil data rekap gaji per periode
    $rekap = $model->getRekapGaji($periode);

    // Ambil data rujukan yang sudah ada di tabel rujukan berdasarkan periode
    $db = \Config\Database::connect();
    $rujukanData = $db->table('rujukan')
                      ->where('periode', $periode)
                      ->get()->getResultArray();

    // Mapping rujukan: key-nya pakai pegawai_pin
    $mapRujukan = [];
    foreach ($rujukanData as $rj) {
        $mapRujukan[$rj['pegawai_pin']] = $rj['jmlrujukan'];
    }

    // Gabungkan data rujukan ke dalam rekap berdasarkan pegawai_pin
    foreach ($rekap as &$row) {
        // Jika tidak ada data rujukan untuk pegawai, set default 0
        $row['jmlrujukan'] = $mapRujukan[$row['pegawai_pin']] ?? 0;
    }

    // Data untuk view
    $data = [
        'title'   => 'Komponen Penggajian Pegawai',
        'periode' => $periode,
        'rekap'   => $rekap,
        'content' => 'admin/komponengaji/index',
    ];

    echo view('admin/layout/wrapper', $data);
}


    public function detail($pegawai_pin)
    {
        checklogin();

        $model = new Komponengaji_model();
        $periode = $this->request->getGet('periode') ?? date('Y-m');

        $detail = $model->getDetailGaji($pegawai_pin, $periode);

        if (!$detail) {
            return redirect()->to('/admin/komponengaji')->with('gagal', 'Data penggajian tidak ditemukan.');
        }

        $data = [
            'title'   => 'Detail Penggajian Pegawai',
            'data'    => $detail,
            'periode' => $periode,
            'content' => 'admin/komponengaji/detail',
        ];

        echo view('admin/layout/wrapper', $data);
    }

    public function updateRujukan()
{
    $pin = $this->request->getPost('pegawai_pin');
    $periode = $this->request->getPost('periode');
    $jml = (int) $this->request->getPost('jmlrujukan');

    $db = \Config\Database::connect();
    $builder = $db->table('rujukan');

    // Cek apakah data sudah ada
    $existing = $builder->where(['pegawai_pin' => $pin, 'periode' => $periode])->get()->getRow();

    if ($existing) {
        $builder->where(['pegawai_pin' => $pin, 'periode' => $periode])
                ->update(['jmlrujukan' => $jml]);
    } else {
        $builder->insert([
            'pegawai_pin' => $pin,
            'periode' => $periode,
            'jmlrujukan' => $jml
        ]);
    }

    return $this->response->setJSON(['status' => 'success']);
}

public function slip($pin)
{
    checklogin(); // validasi login

    $periode = $this->request->getGet('periode') ?? date('Y-m');
    $model   = new \App\Models\Komponengaji_model();

    // Ambil semua rekap gaji pada periode tersebut
    $rekapList = $model->getRekapGaji($periode);

    // Ambil data rujukan dari DB untuk periode ini
    $db = \Config\Database::connect();
    $rujukanData = $db->table('rujukan')
                      ->where('periode', $periode)
                      ->get()->getResultArray();

    // Mapping rujukan berdasarkan pegawai_pin
    $mapRujukan = [];
    foreach ($rujukanData as $rj) {
        $mapRujukan[$rj['pegawai_pin']] = $rj['jmlrujukan'];
    }

    // Cari data pegawai dengan pin yang cocok
    $rekap = null;
    foreach ($rekapList as $row) {
        if ($row['pegawai_pin'] == $pin) {
            // Tambahkan jmlrujukan dari mapping jika ada
            $row['jmlrujukan'] = $mapRujukan[$pin] ?? 0;
            $rekap = $row;
            break;
        }
    }

    if (!$rekap) {
        return redirect()->to(base_url('admin/komponengaji'))->with('warning', 'Data tidak ditemukan.');
    }

    // Data tambahan untuk tampilan slip
    $site = [
        'icon' => 'logopku.png',
        'namaweb' => 'RS PKU Muhammadiyah Boja',
    ];

    return view('admin/komponengaji/slip', [
        'rekap'   => $rekap,
        'periode' => $periode,
        'site'    => $site,
    ]);
}

public function kirim_wa($pin)
{
    checklogin();

    $periode = $this->request->getGet('periode') ?? date('Y-m');
    $model   = new \App\Models\Komponengaji_model();

    // Ambil semua rekap gaji pada periode tersebut
    $rekapList = $model->getRekapGaji($periode);

    // Ambil data rujukan dari DB untuk periode ini
    $db = \Config\Database::connect();
    $rujukanData = $db->table('rujukan')
                      ->where('periode', $periode)
                      ->get()->getResultArray();

    // Mapping rujukan berdasarkan pegawai_pin
    $mapRujukan = [];
    foreach ($rujukanData as $rj) {
        $mapRujukan[$rj['pegawai_pin']] = $rj['jmlrujukan'];
    }

    // Cari data pegawai dengan pin yang cocok
    $rekap = null;
    foreach ($rekapList as $row) {
        if ($row['pegawai_pin'] == $pin) {
            $row['jmlrujukan'] = $mapRujukan[$pin] ?? 0;
            $rekap = $row;
            break;
        }
    }

    if (!$rekap) {
        return redirect()->to(base_url('admin/komponengaji'))->with('warning', 'Data tidak ditemukan.');
    }

    // Nomor HP dan nama dari data rekapan
    $nama = $rekap['pegawai_nama'];
    $nohp = '62' . ltrim($rekap['nohp'], '0');

    // Generate file slip image (pastikan file slip sudah tersedia sebelumnya)
    $filename = 'slip_' . $pin . '_' . $periode . '.png';
    $imageUrl = base_url('slip/' . $filename);

    // Kirim via API
    $response = $this->sendSlipViaWablas($nohp, $nama, $periode);

    // if (is_array($response) && isset($response['status']) && $response['status']) {
    //     return redirect()->back()->with('success', 'Slip berhasil dikirim ke WhatsApp.');
    // } else {
    //     $msg = is_array($response) && isset($response['message']) ? $response['message'] : 'Unknown error';
    //     return redirect()->back()->with('error', 'Gagal mengirim slip: ' . $msg);
    // }
    
}

private function sendSlipViaWablas($phone, $nama, $periode)
{
    $curl = curl_init();
$token = "eCfIDYH9sNK4q8Z8HQNl3GL2n11pPTQrAPQfIOoP7S5otqERIoSOauf";
$secret_key = "Jh8wMJAJ";
$phone = $phone;
$message = "Assalamualaikum {$nama},\nBerikut slip gaji Anda bulan {$periode}.";

curl_setopt($curl, CURLOPT_URL, "https://tegal.wablas.com/api/send-message?token=$token.$secret_key&phone=$phone&message=$message");

$result = curl_exec($curl);
curl_close($curl);
echo "<pre>";
print_r($result);
// echo "https://tegal.wablas.com/api/send-message?token=$token.$secret_key&phone=$phone&message=$message";

}

private function generateSlipImage($htmlContent, $outputPath)
{
    $tmpHtml = WRITEPATH . 'temp_slip.html';
    file_put_contents($tmpHtml, $htmlContent);

    $command = "wkhtmltoimage --width 800 {$tmpHtml} {$outputPath}";
    exec($command);

    return $outputPath;
}


}
