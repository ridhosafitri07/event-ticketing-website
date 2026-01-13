<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'email', 'phone', 'password', 'registered_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'registered_at';
    protected $updatedField = 'updated_at';
    
    // Validation rules
    protected $validationRules = [
        'name'  => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'phone' => 'required|min_length[10]|max_length[20]',
        'password' => 'required|min_length[6]'
    ];
    
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar'
        ]
    ];
    
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
     * Verify login credentials
     */
    public function verifyLogin($email, $password)
    {
        $user = $this->where('email', $email)->first();
        
        if (!$user) {
            return false;
        }
        
        if (password_verify($password, $user->password)) {
            return $user;
        }
        
        return false;
    }
}
