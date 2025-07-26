<?php

namespace App\Models;

use CodeIgniter\Model;

class Nominaldasar_model extends Model
{
    protected $table      = 'nominaldasar';
    protected $primaryKey = 'id'; // sesuaikan jika tidak ada id, bisa kosongkan
    protected $allowedFields = ['rujukan', 'uangmakan', 'bpjs', 'koperasi'];
    public $useTimestamps = false;
}
