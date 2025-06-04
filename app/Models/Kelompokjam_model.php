<?php

namespace App\Models;

use CodeIgniter\Model;

class Kelompokjam_model extends Model
{
    protected $table = 'kelompokjam';
    protected $primaryKey = 'id';
    protected $allowedFields = ['bagian', 'sif', 'jammasuk', 'jampulang'];

    public function getJamShift($bagian, $shift)
    {
        return $this->where('bagian', $bagian)
                    ->where('shift', $shift)
                    ->first();
    }
}
