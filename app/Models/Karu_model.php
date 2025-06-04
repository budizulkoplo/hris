<?php

namespace App\Models;

use CodeIgniter\Model;

class Karu_model extends Model
{
    protected $table              = 'karu';
    protected $primaryKey         = 'idkaru';
    protected $returnType         = 'array';
    protected $useSoftDeletes     = false;
    protected $allowedFields      = ['idkaru', 'idkelompokkerja', 'pin', 'nama'];

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Ambil semua data Karu
    public function listing()
    {
        return $this->select('karu.*, kelompokkerja.namakelompok as kelompok_nama')
                    ->join('kelompokkerja', 'kelompokkerja.idkelompokkerja = karu.idkelompokkerja', 'join')
                    ->orderBy('karu.idkaru', 'DESC')
                    ->findAll();
    }

    // Ambil detail Karu berdasarkan ID
    public function detail($idkaru)
    {
        return $this->select('karu.*, kelompokkerja.namakelompok as kelompok_nama')
                    ->join('kelompokkerja', 'kelompokkerja.idkelompokkerja = karu.idkelompokkerja', 'left')
                    ->where('karu.idkaru', $idkaru)
                    ->first();
    }

    public function getAll()
    {
        return $this->select('karu.*, kelompokkerja.namakelompok as kelompok_nama')
        ->join('kelompokkerja', 'kelompokkerja.idkelompokkerja = karu.idkelompokkerja', 'left')
        ->orderBy('karu.idkaru', 'DESC')
        ->findAll();
    }
}
