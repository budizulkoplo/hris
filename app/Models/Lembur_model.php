<?php
namespace App\Models;

use CodeIgniter\Model;

class Lembur_model extends Model
{
    protected $table      = 'lembur';
    protected $primaryKey = 'idlembur';
    protected $allowedFields = ['pegawai_pin', 'tgllembur', 'alasan', 'created_at'];

    public function getLemburByProcedure($bulanTahun)
    {
        $db = \Config\Database::connect();
    
        try {
            $query = $db->query("CALL spRptAbsensiLemburData(?)", [$bulanTahun]);
            $result = $query->getResultArray();
    
            // Debug hasil query
            if (empty($result)) {
                die('Data tidak ditemukan atau prosedur salah.');
            }
    
            return $result;
        } catch (\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getLemburBulanan($pin, $start, $end)
    {
        return $this->where('pegawai_pin', $pin)
                    ->where('tgllembur >=', $start)
                    ->where('tgllembur <=', $end)
                    ->where('alasan !=', '')
                    ->findAll();
    }
}
