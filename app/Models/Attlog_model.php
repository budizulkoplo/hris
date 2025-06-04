<?php
namespace App\Models;

use CodeIgniter\Model;

class AttLog_model extends Model
{
    protected $DBGroup = 'absensi'; // koneksi kedua
    protected $table = 'att_log';
    protected $primaryKey = 'sn';
    protected $allowedFields = ['scan_date', 'pin', 'verifymode', 'inoutmode'];

    public function getLogBulanan($pin, $start, $end)
    {
        return $this->where('pin', $pin)
                    ->where('scan_date >=', $start)
                    ->where('scan_date <=', $end)
                    ->orderBy('scan_date')
                    ->findAll();
    }
}
