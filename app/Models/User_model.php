<?php

namespace App\Models;

use CodeIgniter\Model;

class User_model extends Model
{
    protected $table              = 'users';
    protected $primaryKey         = 'id_user';
    protected $returnType         = 'array';
    protected $useSoftDeletes     = false;
    protected $allowedFields      = [
        'id_user', 'nama', 'email', 'username', 'password', 
        'nik', 'alamat', 'nohp', 'keterangan', 
        'akses_level', 'gambar', 'created_at', 'updated_at'
    ];
    protected $useTimestamps      = true;
    protected $createdField       = 'created_at';
    protected $updatedField       = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // login
    public function login($username, $password)
    {
        $user = $this->asArray()
            ->where(['username' => $username])
            ->first();

        // Verifikasi password jika user ditemukan
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    // listing
    public function listing()
    {
        $builder = $this->db->table($this->table);
        $builder->orderBy('id_user', 'DESC');
        $query = $builder->get();

        return $query->getResultArray();
    }

    // total
    public function total()
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(*) AS total');
        $query = $builder->get();

        return $query->getRowArray();
    }

    // detail
    public function detail($id_user)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_user', $id_user);
        $query = $builder->get();

        return $query->getRowArray();
    }

    // tambah log
    public function user_log($data)
    {
        $builder = $this->db->table('user_logs');
        $builder->insert($data);
    }
}
