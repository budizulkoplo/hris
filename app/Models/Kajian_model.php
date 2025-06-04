<?php

namespace App\Models;

use CodeIgniter\Model;

class Kajian_model extends Model
{
    protected $table = 'kajian';
    protected $primaryKey = 'idkajian';
    protected $allowedFields = [
        'namakajian', 'tanggal', 'lokasi', 'qrcode', 'keterangan', 'created_at', 'updated_at', 'deleted_at'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}
