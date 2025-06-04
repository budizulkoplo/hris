<?php

namespace App\Models;

use CodeIgniter\Model;

class Kalender_model extends Model
{
    public function getDataKalender($pegawaiPin, $bulan)
    {
        $db = \Config\Database::connect();

        $start_date = date('Y-m-d', strtotime($bulan . '-26 -1 month'));
        $end_date = date('Y-m-d', strtotime($bulan . '-25'));

        $query = $db->query("CALL spKalenderAbsensiPegawai(?, ?, ?)", [
            $pegawaiPin, $start_date, $end_date
        ]);

        $result = $query->getResultArray();

        return $result;
    }
}
