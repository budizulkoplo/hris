<?php
namespace App\Models;

use CodeIgniter\Model;

class Cuti_model extends Model
{
    // Tabel utama cuti header
    protected $table = 'cutihdr';
    protected $primaryKey = 'idcuti';
    protected $returnType = 'array';

    // Field yang boleh diisi
    protected $allowedFields = [
        'pegawai_pin',
        'tgl_mulai',
        'tgl_selesai',
        'jml_hari',
        'alasancuti',
        'jeniscuti',
        'idpengganti',
        'created_at'
    ];

    // Aktifkan otomatis timestamp (untuk created_at)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // tidak ada kolom updated_at

    // Hitung sisa cuti per tahun
    public function getSisaCuti($pegawai_pin)
    {
        $tahunIni = date('Y');

        $cutiTerpakai = $this->selectSum('jml_hari')
            ->where('pegawai_pin', $pegawai_pin)
            ->where('YEAR(tgl_mulai)', $tahunIni)
            ->first();

        return 12 - ($cutiTerpakai['jml_hari'] ?? 0);
    }

    // Ambil daftar cuti tahun berjalan
    public function getDaftarCutiTahunBerjalan($pegawai_pin, $tahun = null)
    {
        $tahun = $tahun ?? date('Y');

        return $this->where('pegawai_pin', $pegawai_pin)
            ->where('YEAR(tgl_mulai)', $tahun)
            ->orderBy('tgl_mulai', 'DESC')
            ->findAll();
    }

    // Ambil detail harian cuti (tabel cuti)
    public function getDetailHarian($idcuti)
    {
        return $this->db->table('cuti')
            ->where('idcuti', $idcuti)
            ->orderBy('tglcuti', 'ASC')
            ->get()
            ->getResultArray();
    }

    // Simpan header cuti + detail harian
    public function simpanCuti($dataHeader, $dataDetail = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert header
            $this->insert($dataHeader);
            $idcuti = $this->getInsertID(); // ambil ID yang baru

            // Insert detail jika ada
            if (!empty($dataDetail)) {
                $detailData = [];
                foreach ($dataDetail as $tanggal) {
                    $detailData[] = [
                        'idcuti'      => $idcuti,
                        'pegawai_pin' => $dataHeader['pegawai_pin'],
                        'tglcuti'     => $tanggal
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
