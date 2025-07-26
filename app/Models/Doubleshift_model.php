<?php namespace App\Models;

use CodeIgniter\Model;

class DoubleShift_model extends Model
{
    protected $table = 'doubleshift';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pegawai_pin', 'tglshift', 'created_at'];
    protected $useTimestamps = false;
    
    // Add any custom methods if needed
}