<?php

namespace App\Models;

use CodeIgniter\Model;

class Komponengaji_model extends Model
{
    protected $table = 'penggajian';
    protected $primaryKey = 'idpenggajian';

    public function getRekapGaji($periode)
{
    return $this->select('
            pegawai.pegawai_nip,
            pegawai.pegawai_pin,
            pegawai.pegawai_nama,
            pegawai.nohp,
            pegawai.email,
            pegawai.jabatan,
            mastergaji.gajipokok,
            mastergaji.tunjstruktural,
            mastergaji.tunjfungsional,
            mastergaji.tunjkeluarga,
            mastergaji.tunjapotek,
            mastergaji.kehadiran,
            mastergaji.pph21,
            penggajian.jmlabsensi,
            penggajian.jmlterlambat,
            penggajian.konversilembur,
            penggajian.cuti,
            penggajian.tugasluar,
            penggajian.totalharikerja,
            penggajian.doubleshift,
            (select rujukan from nominaldasar limit 1) as rujukan,
            (select uangmakan from nominaldasar limit 1) as uangmakan,
            (select koperasi from nominaldasar limit 1) as koperasi
        ')
        ->join('pegawai', 'penggajian.pegawai_pin = pegawai.pegawai_pin')
        ->join('mastergaji', 'mastergaji.pegawai_pin = pegawai.pegawai_pin AND mastergaji.verifikasi = "1"', 'left')
        ->where('penggajian.periode', $periode)
        ->where("DATE_FORMAT(mastergaji.tglaktif, '%Y-%m') <=", $periode)
        ->findAll();
}


}
