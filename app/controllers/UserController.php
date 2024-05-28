<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Core\Response;

class UserController {
    private $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    /**
     * Get all users
     */
    public function getUsers() {
        $users = $this->userService->getAllUsers();
        Response::send($users);
    }

    /**
     * Get user by ID
     */
    public function getUser($id) {
        $user = $this->userService->getUserById($id);
        Response::send($user);
    }

    /**
     * Create new user
     */
    public function createUser() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            Response::send(['message' => 'Invalid input', 'code' => 400], 400);
            return;
        }

        $result = $this->userService->createUser($data);

        Response::send($result, $result['code']);
    }
}
