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
                mastergaji.lemburkhusus,
                (select rujukan from nominaldasar limit 1) as rujukan,
                (select uangmakan from nominaldasar limit 1) as uangmakan,
                (select koperasi from nominaldasar limit 1) as koperasi,
                (select bpjs from nominaldasar limit 1) as bpjstk,
                potongan.leasing_kendaraan,
                potongan.iuran_amal_soleh,
                potongan.simpanan_pokok,
                potongan.simpanan_wajib,
                potongan.simpanan_hari_raya,
                potongan.simpanan_gerakan_menabung,
                potongan.angsuran_koperasi,
                potongan.belanja_koperasi_tdm,
                potongan.simpanan_dplk_bni,
                potongan.angsuran_bri,
                potongan.angsuran_bank_jateng,
                potongan.angsuran_darmawanita,
                potongan.arisan_darmawanita,
                potongan.tabungan_darmawanita,
                potongan.lain_lain
            ')
            ->join('pegawai', 'penggajian.pegawai_pin = pegawai.pegawai_pin')
            ->join('mastergaji', 'mastergaji.pegawai_pin = pegawai.pegawai_pin AND mastergaji.verifikasi = "1"', 'left')
            ->join('potongan', "potongan.pegawai_pin = pegawai.pegawai_pin AND potongan.periode = '$periode'", 'left')
            ->where('penggajian.periode', $periode)
            ->where("DATE_FORMAT(mastergaji.tglaktif, '%Y-%m') <=", $periode)
            ->findAll();
    }

    // Method untuk mendapatkan data rujukan
    public function getRujukanData($periode)
    {
        $db = \Config\Database::connect();
        $startDate = $periode . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        return $db->table('rujukan')
                ->select('pegawai_pin, COUNT(*) as jmlrujukan')
                ->where('tglrujukan >=', $startDate)
                ->where('tglrujukan <=', $endDate)
                ->groupBy('pegawai_pin')
                ->get()
                ->getResultArray();
    }
}