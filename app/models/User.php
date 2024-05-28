<?php
namespace App\Models;

use App\Services\DatabaseService;

class User {
    private $databaseService;

    private $columnsList;

    public function __construct() {
        $this->databaseService = new DatabaseService();
        $this->columnsList = $this->excludeFields();
    }

    /**
     * Define the fields that should be omitted from the response
     */
    private function excludeFields() {
        $columnsResult = $this->databaseService->executeQuery("SHOW COLUMNS FROM users");

        if (!is_array($columnsResult)) {
            throw new PDOException("Failed to retrieve columns from users table.");
        }

        $columns = array_column($columnsResult, 'Field');

        $columns = array_diff($columns, ['password']);

        $columnsList = implode(', ', $columns);

        return $columnsList;
    }

    /**
     * Fetch all suers
     */
    public function all() {
        try {
            $result = $this->databaseService->executeQuery("SELECT $this->columnsList FROM users");
            
            return ['message' => 'OK', 'code' => 200, 'data' => $result];
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    /**
     * Get user by ID
     */
    public function find($id) {
        try {
            $query = "SELECT $this->columnsList FROM users WHERE id = ?";

            $result = $this->databaseService->executeQuery($query, [$id]);
    
            if (empty($result)) {
                return ['message' => 'Not Found', 'code' => 404];
            }
    
            return ['message' => 'OK', 'code' => 200, 'data' => $result[0]];
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
    
    /**
     * Create new user
     */
    public function insert($username, $email, $password) {
        try {   
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

            $result = $this->databaseService->executeQuery($sql, [$username, $email, $password]);
         
            if(is_array($result)) return ['message' => $result['message'], 'code' => 500];

            return ['message' => 'CREATED', 'code' => 201, 'data' => ['userId' => $result]];
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
}
