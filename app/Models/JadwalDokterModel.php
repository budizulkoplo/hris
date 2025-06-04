<?php namespace App\Models;

use CodeIgniter\Model;

class JadwalDokterModel extends Model
{
    protected $DBGroup = 'webrs'; // Koneksi ke database webrs
    protected $table      = 'jadwal_dokters';
    protected $primaryKey = 'id_jadwal';

    protected $allowedFields = ['jam_mulai', 'jam_selesai', 'status'];
    protected $useTimestamps = false; // jika tidak ada created_at/updated_at

    public function getJadwal()
    {
        return $this->db->table('jadwal_dokters a')
            ->select('a.id_jadwal, a.id_dokter, b.nama_dokter, hari, jam_mulai, jam_selesai, a.status')
            ->join('dokters b', 'a.id_dokter = b.id_dokter')
            ->orderBy('b.nama_dokter')
            ->orderBy("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')", '', false)
            ->get()->getResultArray();
    }

    public function getDetail($id_jadwal)
    {
        return $this->db->table('jadwal_dokters a')
            ->select('a.id_jadwal, a.id_dokter, b.nama_dokter, hari, jam_mulai, jam_selesai, a.status')
            ->join('dokters b', 'a.id_dokter = b.id_dokter')
            ->where('a.id_jadwal', $id_jadwal)
            ->get()->getRowArray();
    }
}
