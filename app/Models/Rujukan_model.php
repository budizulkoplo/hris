<?php

namespace App\Models;

use CodeIgniter\Model;

class Rujukan_model extends Model
{
    protected $table      = 'rujukan';
    protected $primaryKey = 'idrujukan';

    protected $allowedFields = ['pegawai_pin', 'tglrujukan', 'keterangan', 'created_at'];
}
