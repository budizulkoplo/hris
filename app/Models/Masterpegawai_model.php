<?php

namespace App\Models;

use CodeIgniter\Model;

class Masterpegawai_model extends Model
{
    protected $table         = 'pegawai';
    protected $primaryKey    = 'pegawai_id';
    protected $allowedFields = [
        'pegawai_nip', 'nik', 'pegawai_nama', 'jabatan', 'nohp', 'email', 'alamat', 'bagian'
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    // Ambil semua data pegawai tanpa menampilkan pegawai_id dan pegawai_pin
    public function getAllPegawai()
    {
        return $this->select('pegawai_nip, nik, pegawai_nama, jabatan, nohp, email, alamat, bagian')
                    ->orderBy('pegawai_nama', 'ASC')
                    ->findAll();
    }

    // Ambil satu data pegawai berdasarkan ID (untuk edit, tapi tidak boleh ubah pegawai_id dan pegawai_pin)
    public function getPegawaiById($id)
    {
        return $this->select('pegawai_nip, nik, pegawai_nama, jabatan, nohp, email, alamat, bagian')
                    ->where('pegawai_id', $id)
                    ->first();
    }

    // Update data pegawai tanpa mengubah pegawai_id dan pegawai_pin
    public function updatePegawai($id, $data)
    {
        return $this->where('pegawai_id', $id)
                    ->set($data)
                    ->update();
    }
}
