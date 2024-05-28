<?php
namespace App\Services;

use App\Models\User;

class UserService {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    /**
     * Get all users
     */
    public function getAllUsers() {
        return $this->user->all();
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        return $this->user->find($id);
    }

    /**
     * Create new user
     */
    public function createUser($user) {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

        return $this->user->insert($user['username'], $user['email'], $hashedPassword);
    }
}
