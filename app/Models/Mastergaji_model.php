<?php

namespace App\Models;

use CodeIgniter\Model;

class Mastergaji_model extends Model
{
    protected $table         = 'mastergaji';
    protected $primaryKey    = 'idgaji';
    protected $allowedFields = [
        'pegawai_pin', 'tglaktif', 'gajipokok', 'tunjstruktural', 'tunjkeluarga',
        'tunjapotek', 'created_at', 'updated_at', 'verifikasi', 'jabatan', 'kehadiran','tunjfungsional'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Ambil data gaji berdasarkan pegawai_pin
    public function getByPin($pin)
    {
        return $this->where('pegawai_pin', $pin)->orderBy('tglaktif', 'DESC')->first();
    }

    // Ambil status verifikasi terakhir per pegawai
    public function getLatestVerification()
    {
        return $this->select('pegawai_pin, verifikasi')
                    ->groupBy('pegawai_pin')
                    ->orderBy('tglaktif', 'DESC')
                    ->findAll();
    }

    // Ambil gabungan data pegawai dan gaji terakhir (untuk tampilan view)
    public function getPegawaiWithGajiStatus()
{
    // Subquery: ambil tglaktif terakhir per pegawai_pin
    $subQuery = $this->db->table('mastergaji')
        ->select('pegawai_pin, MAX(tglaktif) AS max_tglaktif')
        ->groupBy('pegawai_pin');

    // Convert to SQL string, tanpa parameter kedua akan reset binding
    $subSql = $subQuery->getCompiledSelect(false);

    // Main query
    $builder = $this->db->table('pegawai p');
    $builder->select('m.idgaji, p.pegawai_id, p.pegawai_pin, p.pegawai_nip, p.pegawai_nama, p.bagian, g.verifikasi');
    $builder->join(
        '(' .
        'SELECT mg.idgaji, mg.pegawai_pin, mg.verifikasi, mg.tglaktif 
         FROM mastergaji mg
         INNER JOIN (' . $subSql . ') latest 
         ON mg.pegawai_pin = latest.pegawai_pin AND mg.tglaktif = latest.max_tglaktif
        ) g',
        'g.pegawai_pin = p.pegawai_pin',
        'left'
    );
    $builder->join('mastergaji m', 'm.pegawai_pin = g.pegawai_pin AND m.tglaktif = g.tglaktif', 'left');
    $builder->orderBy('p.pegawai_nama', 'ASC');

    return $builder->get()->getResultArray();
}


}
