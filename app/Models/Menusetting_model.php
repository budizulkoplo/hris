<?php

namespace App\Models;

use CodeIgniter\Model;

class Menusetting_model extends Model
{
    protected $table         = 'menu';           // Nama tabel menu
    protected $primaryKey    = 'idmenu';         // Primary key tabel
    protected $allowedFields = [
        'namamenu', 'link', 'icon', 'parentid', 'submenu', 'level', 'aktif'
    ]; // Kolom yang bisa diisi

    /**
     * Mengambil semua data menu, diurutkan berdasarkan idmenu.
     *
     * @return array
     */
    public function listing()
    {
        return $this->orderBy('idmenu', 'ASC')->findAll();
    }

    /**
     * Mengambil detail menu berdasarkan idmenu.
     *
     * @param int $idmenu
     * @return array|null
     */
    public function detail($idmenu)
    {
        return $this->where('idmenu', $idmenu)->first();
    }

    /**
     * Menghitung jumlah total menu yang ada.
     *
     * @return int
     */
    public function total()
    {
        return $this->countAll();
    }

    /**
     * Mengambil submenu berdasarkan parentid.
     *
     * @param int $parentid
     * @return array
     */
    public function getSubmenus($parentid)
    {
        return $this->where('parentid', $parentid)->orderBy('idmenu', 'ASC')->findAll();
    }

    /**
     * Mengambil menu aktif berdasarkan level.
     *
     * @param string $level
     * @return array
     */
    public function getActiveMenuByLevel($level)
    {
        return $this->like('level', $level)->where('aktif', 'Y')->findAll();
    }

    /**
     * Mengupdate status aktif menu berdasarkan idmenu.
     *
     * @param int $idmenu
     * @param string $status ('Y' atau 'N')
     * @return bool
     */
    public function updateStatus($idmenu, $status)
    {
        return $this->update($idmenu, ['aktif' => $status]);
    }

    /**
     * Menambahkan menu baru ke database.
     *
     * @param array $data
     * @return int|false ID menu yang baru atau false jika gagal
     */
    public function addMenu($data)
    {
        return $this->insert($data);
    }

    /**
     * Mengedit menu yang sudah ada berdasarkan idmenu.
     *
     * @param int $idmenu
     * @param array $data
     * @return bool
     */
    public function editMenu($idmenu, $data)
    {
        return $this->update($idmenu, $data);
    }

    /**
     * Menghapus menu berdasarkan idmenu.
     *
     * @param int $idmenu
     * @return bool
     */
    public function deleteMenu($idmenu)
    {
        return $this->delete($idmenu);
    }

    /**
     * Menambahkan atau menghapus role tertentu di field level menu.
     *
     * @param int $idmenu
     * @param string $role
     * @param string $action ('add' atau 'remove')
     * @return bool
     */
    public function updateRole($idmenu, $role, $action)
    {
        $menu = $this->find($idmenu);
        if (!$menu) {
            return false;
        }

        $currentRoles = explode(',', $menu['level']);
        if ($action === 'add' && !in_array($role, $currentRoles)) {
            $currentRoles[] = $role;
        } elseif ($action === 'remove' && in_array($role, $currentRoles)) {
            $currentRoles = array_diff($currentRoles, [$role]);
        }

        $updatedLevel = implode(',', $currentRoles);

        return $this->update($idmenu, ['level' => $updatedLevel]);
    }
}
