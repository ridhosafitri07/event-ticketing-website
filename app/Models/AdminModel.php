<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 'email', 'password', 'full_name', 
        'role', 'is_active', 'last_login'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }
    
    /**
     * Verify admin login
     */
    public function verifyLogin($username, $password)
    {
        $admin = $this->where('username', $username)
                      ->where('is_active', true)
                      ->first();
        
        if (!$admin) {
            return false;
        }
        
        if (password_verify($password, $admin->password)) {
            // Update last login
            $this->update($admin->id, ['last_login' => date('Y-m-d H:i:s')]);
            return $admin;
        }
        
        return false;
    }
}
