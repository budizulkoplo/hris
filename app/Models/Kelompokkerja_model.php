<?php

namespace App\Models;

use CodeIgniter\Model;

class Kelompokkerja_model extends Model
{
    protected $table         = 'kelompokkerja';
    protected $primaryKey    = 'idkelompokkerja';
    protected $allowedFields = ['namakelompok'];
    protected $returnType    = 'array';

    // Ambil semua data kelompok kerja
    public function getAll()
    {
        return $this->orderBy('namakelompok', 'ASC')->findAll();
    }

    // Ambil data kelompok kerja berdasarkan ID
    public function getById($id)
    {
        return $this->where('idkelompokkerja', $id)->first();
    }

    // Tambah data kelompok kerja
    public function add($data)
    {
        return $this->insert($data);
    }

    // Update data kelompok kerja
    public function updateData($id, $data)
    {
        return $this->update($id, $data);
    }

    // Hapus data kelompok kerja
    public function deleteById($id)
    {
        return $this->delete($id);
    }
}
