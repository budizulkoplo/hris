<?php

// app/Models/LevelModel.php
// app/Models/LevelModel.php
namespace App\Models;

use CodeIgniter\Model;

class LevelModel extends Model
{
    protected $table         = 'level';      // Nama tabel level
    protected $primaryKey    = 'idlevel';    // Primary key tabel level
    protected $allowedFields = ['name'];      // Kolom yang bisa diisi, sesuaikan dengan struktur tabel level

    // Fungsi untuk mengambil semua data level
    public function getAllLevels()
    {
        return $this->findAll();  // Mengambil semua level
    }

    // Menambahkan konfigurasi agar hasil query berupa objek
    protected $returnType = 'object'; // Mengubah hasil dari array ke objek
}

