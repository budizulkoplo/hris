<?php
namespace App\Models;

use CodeIgniter\Model;

class Tugasluar_model extends Model
{
    protected $table      = 'tugasluar';
    protected $primaryKey = 'idtugasluar';
    protected $allowedFields = ['pegawai_pin', 'tgltugasluar', 'alasan', 'created_at','lokasi','waktu'];
}
