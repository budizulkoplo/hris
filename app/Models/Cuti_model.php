<?php
namespace App\Models;

use CodeIgniter\Model;

class Cuti_model extends Model
{
    // Untuk tabel cutihdr (header cuti)
    protected $table = 'cutihdr';
    protected $primaryKey = 'idcuti';
    protected $allowedFields = ['pegawai_pin', 'tgl_mulai', 'tgl_selesai', 'jml_hari', 'alasancuti', 'jeniscuti', 'idpengganti', 'created_at'];
    protected $returnType = 'array';

    // Untuk menghitung sisa cuti
    public function getSisaCuti($pegawai_pin)
    {
        $tahunIni = date('Y');
        $cutiTerpakai = $this->where('pegawai_pin', $pegawai_pin)
                             ->where('YEAR(tgl_mulai)', $tahunIni)
                             ->selectSum('jml_hari')
                             ->first();

        return 12 - ($cutiTerpakai['jml_hari'] ?? 0);
    }

    // Untuk mendapatkan daftar cuti tahun berjalan
    public function getDaftarCutiTahunBerjalan($pegawai_pin, $tahun = null)
    {
        if ($tahun === null) {
            $tahun = date('Y');
        }

        return $this->where('pegawai_pin', $pegawai_pin)
                    ->where('YEAR(tgl_mulai)', $tahun)
                    ->orderBy('tgl_mulai', 'DESC')
                    ->findAll();
    }

    // Untuk mendapatkan detail harian cuti
    public function getDetailHarian($idcuti)
    {
        return $this->db->table('cuti')
                       ->where('idcuti', $idcuti)
                       ->orderBy('tglcuti', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    // Untuk menyimpan data cuti (header dan detail)
    public function simpanCuti($dataHeader, $dataDetail = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Simpan header cuti
            $this->insert($dataHeader);
            $idcuti = $this->insertID();

            // Jika ada detail, simpan detail harian
            if (!empty($dataDetail)) {
                $detailData = [];
                foreach ($dataDetail as $tanggal) {
                    $detailData[] = [
                        'idcuti' => $idcuti,
                        'pegawai_pin' => $dataHeader['pegawai_pin'],
                        'tglcuti' => $tanggal
                    ];
                }

                $db->table('cuti')->insertBatch($detailData);
            }

            $db->transComplete();
            return $idcuti;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Gagal menyimpan cuti: ' . $e->getMessage());
            return false;
        }
    }
}