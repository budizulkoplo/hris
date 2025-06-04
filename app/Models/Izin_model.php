<?php
namespace App\Models;

use CodeIgniter\Model;

class Izin_model extends Model
{
    protected $table      = 'izin';
    protected $primaryKey = 'idizin';
    protected $allowedFields = ['pegawai_pin', 'tglizin', 'alasan', 'created_at'];
}
