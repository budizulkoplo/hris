<?php

namespace App\Models;

use CodeIgniter\Model;

class Dasbor_model extends Model
{
    // berita
   

    // user
    public function user()
    {
        $builder = $this->db->table('users');
        $query   = $builder->get();

        return $query->getNumRows();
    }

    public function pegawai()
    {
        $builder = $this->db->table('pegawai');
        $builder->selectCount('*', 'pegawai'); // Perbaikan alias
        $builder->where('bagian !=', 'nonaktif'); 
        $query = $builder->get();
    
        return $query->getRow()->pegawai; // Ambil hasil sesuai alias yang benar
    }
    
    public function cutiHariIni()
    {
        $builder = $this->db->table('cuti');
        $builder->selectCount('*', 'jumlah_cuti'); // Hitung jumlah cuti hari ini
        $builder->where('tglcuti', date('Y-m-d')); // Filter berdasarkan tanggal hari ini
        $query = $builder->get();

        return $query->getRow()->jumlah_cuti; // Mengembalikan jumlah cuti hari ini
    }

    public function tlHariIni()
    {
        $builder = $this->db->table('tugasluar');
        $builder->selectCount('*', 'jmltl'); // Hitung jumlah cuti hari ini
        $builder->where('tgltugasluar', date('Y-m-d')); // Filter berdasarkan tanggal hari ini
        $query = $builder->get();

        return $query->getRow()->jmltl; // Mengembalikan jumlah cuti hari ini
    }

public function kehadiran()
{
    $session = session();
    $nik = $session->get('username'); // atau ganti sesuai key yang menyimpan NIK user

    $builder = $this->db->table('kehadiran_kajian');
    $builder->selectCount('*', 'jml_kehadiran');
    $builder->where('nik', $nik);
    
    $query = $builder->get();
    return $query->getRow()->jml_kehadiran;
}

    
}
