<?php
namespace App\Models;

use CodeIgniter\Model;

class Cuti_model extends Model
{
    protected $table = 'cuti';
    protected $primaryKey = 'idcuti';
    protected $allowedFields = ['pegawai_pin', 'tglcuti', 'jml_hari', 'alasancuti', 'created_at'];

    public function getSisaCuti($pegawai_pin)
    {
        $tahunIni = date('Y');
        $cutiTerpakai = $this->where('pegawai_pin', $pegawai_pin)
                             ->where('YEAR(tglcuti)', $tahunIni)
                             ->selectSum('jml_hari')
                             ->first();

        return 12 - ($cutiTerpakai['jml_hari'] ?? 0);
    }
}
