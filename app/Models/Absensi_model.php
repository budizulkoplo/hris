<?php

namespace App\Models;

use CodeIgniter\Model;

class Absensi_model extends Model
{
    // Ambil data absensi dari stored procedure
    public function getAbsensiByProcedure($bulanTahun)
    {
        $db = \Config\Database::connect();
    
        try {
            $query = $db->query("CALL spRptAbsensiDatav2(?)", [$bulanTahun]);
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
    
}

