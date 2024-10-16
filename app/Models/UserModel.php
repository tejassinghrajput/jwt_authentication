<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'api';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'name', 'email', 'password'];
    public function saveUser($data)
    // Function to add a new user
    {
        $fields = implode(', ', $this->allowedFields);
        $placeholders = rtrim(str_repeat('?, ', count($this->allowedFields)), ', ');

        $query = $this->query("INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})", [
            $data['username'],
            $data['name'],
            $data['email'],
            $data['password']
        ]);
        return $this->insertID();
    }
    public function removeUser($id)
    // function to delete user
    {
        $query = $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
        return $query;
    }
    public function updateUser($id, $data)
    {
        // function to update user
        $setFields = [];
        foreach ($this->allowedFields as $field) {
            $setFields[] = "{$field} = ?";
        }
        $setClause = implode(', ', $setFields);

        $query = $this->query("UPDATE {$this->table} SET {$setClause} WHERE id = ?", array_merge(array_values($data), [$id]));
        return $query;
    }
    public function getUserById($id)
    {
        // function to fetch details of user through ID.
        $query = $this->query("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        return $query->getRowArray();
    }
    public function getUserByEmail($email)
    {
        // function to fetch details of user through Email.
        $query = $this->query("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
        return $query->getRowArray();
    }
    public function getUserByUsername($username)
    {
        // function to fetch details of user through username.
        $query = $this->query("SELECT * FROM {$this->table} WHERE username = ?", [$username]);
        return $query->getRowArray();
    }
    public function getAllUsers()
    {
        // function to fetch all users.
        $query = $this->query("SELECT * FROM {$this->table}");
        return $query->getResultArray();
    }
}
