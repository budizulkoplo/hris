<?php

namespace App\Controllers\Admin;

use App\Models\Menusetting_model;
use App\Models\LevelModel; // Pastikan LevelModel diimpor

class Menu extends BaseController
{
    public function index()
    {
        // Membuat instance model
        $menuModel = new Menusetting_model();
        
        // Ambil data menu dari model menggunakan metode listing()
        $menus = $menuModel->listing();

        // Membuat instance model LevelModel
        $levelModel = new LevelModel();
        
        // Ambil semua data level dari tabel level
        $levels = $levelModel->getAllLevels(); // Menggunakan metode yang sudah dibuat

        // Data yang dikirim ke view
        $data = [
            'menus' => $menus,
            'level' => $levels, // Mengirimkan data level ke view
            'title' => 'Manajemen Menu',
            'content' => 'admin/menu/index',
        ];

        // Render view
        echo view('admin/layout/wrapper', $data);
    }

    public function getMenuByLevel()
    {
        $levelid = $this->request->getVar('levelid');
        
        if (!$levelid) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Level ID is required.'
            ]);
        }

        // Mendapatkan koneksi database
        $db = \Config\Database::connect();

        // Menyusun query SQL
        $sql = "SELECT 
                    menu.idmenu AS id, 
                    menu.namamenu AS text, 
                    menu.icon AS icon, 
                    menu.parentid AS parent, 
                    menu.aktif, 
                    CASE WHEN menu.level LIKE '%" . $this->db->escapeString($levelid) . "%' THEN 'true' ELSE '' END AS selected 
                FROM menu";

        // Menjalankan query dan mengambil hasilnya
        $menus = $db->query($sql)->getResultArray();


        // Kirim data ke frontend dalam format JSON
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $menus
        ]);
    }
        
    public function getMenuByLevelName($levelName)
    {
        $menuModel = new Menusetting_model();
        
        try {
            // Query untuk mendapatkan menu berdasarkan levelname
            $menus = $menuModel->like('level', $levelName)->findAll();

            // Mengembalikan data menu dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $menus
            ]);
        } catch (\Exception $e) {
            // Menangani error jika terjadi
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
 * Memperbarui status menu.
 */
public function updateMenuStatus()
{
    // Ambil data dari request
    $updates = $this->request->getJSON(true);
    
    if (!isset($updates['updates']) || !isset($updates['role'])) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid request. Updates and role are required.'
        ])->setStatusCode(400);
    }

    $menuModel = new \App\Models\Menusetting_model();

    try {
        foreach ($updates['updates'] as $update) {
            $menuId = $update['idmenu'];
            $action = $update['action']; // "add" atau "remove"

            // Ambil data menu saat ini
            $menu = $menuModel->find($menuId);

            if (!$menu) {
                continue; // Skip jika ID menu tidak ditemukan
            }

            // Ubah field level sesuai dengan aksi
            $currentLevel = explode(',', $menu['level']); // Pisahkan level yang ada
            $role = $updates['role'];

            if ($action === 'add' && !in_array($role, $currentLevel)) {
                // Tambahkan role jika belum ada
                $currentLevel[] = $role;
            } elseif ($action === 'remove' && in_array($role, $currentLevel)) {
                // Hapus role jika ada
                $currentLevel = array_diff($currentLevel, [$role]);
            }

            // Simpan perubahan ke database
            $menuModel->update($menuId, [
                'level' => implode(',', $currentLevel) // Gabungkan kembali menjadi string
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Menu status updated successfully.'
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update menu status: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}


}
