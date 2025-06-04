<?php
namespace App\Models;

use CodeIgniter\Model;

class Menuadmin_model extends Model
{
    protected $table = 'menu'; // Replace with your actual table name
    protected $primaryKey = 'idmenu'; // Your primary key
    protected $allowedFields = ['namamenu', 'link', 'icon', 'aktif', 'level']; // Add other fields as necessary
}
