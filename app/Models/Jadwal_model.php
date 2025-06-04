<?php
namespace App\Models;

use CodeIgniter\Model;

class Jadwal_model extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'idjadwal'; // Primary key yang benar
    protected $allowedFields = ['tgl', 'pegawai_pin', 'shift']; // Pastikan shift disertakan

    public function getJadwalBulanan($pin, $start, $end)
    {
        return $this->where('pegawai_pin', $pin)
                    ->where('tgl >=', $start)
                    ->where('tgl <=', $end)
                    ->findAll();
    }
}

