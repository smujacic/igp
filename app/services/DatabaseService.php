<?php
namespace App\Services;

use PDO;
use PDOException;

class DatabaseService {
    private $pdo;

    public function __construct() {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    /**
     * Execute any type of MySQL query
     */
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
    
            $queryType = strtoupper(substr(trim($sql), 0, strpos(trim($sql), ' ')));
    
            if ($queryType === 'SELECT' || $queryType === 'SHOW') {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } elseif ($queryType === 'INSERT') {
                return $this->pdo->lastInsertId();
            } else {
                return $stmt->rowCount();
            }
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
    
}

