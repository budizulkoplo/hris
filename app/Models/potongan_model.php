<?php
namespace App\Models;

use CodeIgniter\Model;

class Potongan_model extends Model
{
    protected $table      = 'potongan';
    protected $primaryKey = 'id_potongan';
    protected $allowedFields = [
        'pegawai_pin','periode',
        'leasing_kendaraan','iuran_amal_soleh','simpanan_pokok','simpanan_wajib',
        'simpanan_hari_raya','simpanan_gerakan_menabung','angsuran_koperasi','belanja_koperasi_tdm',
        'simpanan_dplk_bni','angsuran_bri','angsuran_bank_jateng','angsuran_darmawanita',
        'arisan_darmawanita','tabungan_darmawanita','lain_lain',
        'jumlah_potongan','gaji_bersih','jumlah_diterima','lazis','jumlah_setelah_lazis'
    ];

    public function getPotonganByPeriode($periode)
    {
        return $this->where('periode', $periode)->findAll();
    }

    public function savePotongan($pegawai_pin, $periode, $field, $value)
    {
        $row = $this->where(['pegawai_pin'=>$pegawai_pin,'periode'=>$periode])->first();
        if ($row) {
            $this->update($row['id_potongan'], [$field => $value]);
        } else {
            $this->insert([
                'pegawai_pin' => $pegawai_pin,
                'periode'     => $periode,
                $field        => $value
            ]);
        }
    }
}
