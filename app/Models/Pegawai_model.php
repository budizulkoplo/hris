<?php

namespace App\Models;

use CodeIgniter\Model;

class Pegawai_model extends Model
{
    protected $table      = 'pegawai';
    protected $primaryKey = 'pegawai_id';

    protected $allowedFields = ['pegawai_nama', 'pegawai_pin', 'pegawai_nip', 'nik', 'jabatan', 'nohp', 'email', 'alamat', 'password'];
    protected $returnType    = 'array';

    // Ambil semua data pegawai
    public function getAll()
    {
        return $this->orderBy('pegawai_nama', 'ASC')->findAll();
    }

    // Ambil data pegawai berdasarkan ID
    public function getById($id)
    {
        return $this->where('pegawai_id', $id)->first();
    }

    // Cari pegawai berdasarkan nama atau NIP
    public function search($keyword)
    {
        return $this->like('pegawai_nama', $keyword)
                    ->orLike('pegawai_nip', $keyword)
                    ->findAll();
    }
}
