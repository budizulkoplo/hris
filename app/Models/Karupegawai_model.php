<?php
namespace App\Models;

use CodeIgniter\Model;

class KaruPegawai_model extends Model
{
    protected $table      = 'karupegawai';
    protected $primaryKey = 'idkarupegawai';
    protected $allowedFields = ['idkaru', 'pegawai_pin'];
}
