<?php

namespace App\Models;

use CodeIgniter\Model;

class RekapLemburModel extends Model
{
    protected $table = 'rekap_lembur';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pegawai_id',
        'bulan',
        'tahun',
        'sisa_lembur_detik',
        'total_lembur',
        'konversi_hari'
    ];
    protected $useTimestamps = true;
}
