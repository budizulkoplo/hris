<?php
namespace App\Models;

use CodeIgniter\Model;

class KehadiranKajianModel extends Model
{
    protected $table = 'kehadiran_kajian';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'idkajian', 'barcodeuniq', 'waktu_scan', 'ip_address',
        'user_agent', 'nik', 'nama'
    ];
}
