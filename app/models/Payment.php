<?php
namespace App\Models;

use App\Services\DatabaseService;

class Payment {
    private $databaseService;

    public function __construct() {
        $this->databaseService = new DatabaseService();
    }

    /**
     * Fins payment by ID
     */
    public function find($paymentId) {
        try {
            $result = $this->databaseService->executeQuery("SELECT * FROM payments WHERE payment_id = ?", [$paymentId]);

            if (empty($result)) {
                return ['message' => 'Not Found', 'code' => 404];
            }
    
            return ['message' => 'OK', 'code' => 200, 'data' => $result[0]];
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    /**
     *  Noda API returns their ID, and this method return enrty from DB by that ID
     */
    public function findPaymentByNodaID($id) {
        try {
            $result = $this->databaseService->executeQuery("SELECT * FROM payments WHERE id = ?", [$id]);

            if (empty($result)) {
                return ['message' => 'Not Found', 'code' => 404];
            }
    
            return ['message' => 'OK', 'code' => 200, 'data' => $result[0]];
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
    
    /**
     * Create new payment
     */
    public function insert($data) {
        $paymentId = $this->generateUUID();
   
        $data = array_merge($data, [
            "paymentId" => $paymentId,
            "returnUrl" => "http://localhost:8090/public/payment/returnurl",
            "webhookUrl" => "http://localhost:8090/public/payment/hookurl"
        ]);
       
        $data['description'] = (!empty($data['description'])) ? $data['description'] : 'Order ' . $data['paymentId'];

        try {   
            $sql = "INSERT INTO payments (payment_id, user_id, amount, currency, description, returnUrl, webhookUrl, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

            $result = $this->databaseService->executeQuery($sql, [
                $data['paymentId'], 
                $data['customerId'], 
                $data['amount'], 
                $data['currency'], 
                $data['description'], 
                $data['returnUrl'], 
                $data['webhookUrl']
            ]);
         
            if(is_array($result)) return ['message' => $result['message'], 'code' => 500];

            return ['message' => 'Created', 'code' => 201, 'data' => $data];
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    /**
     * Update payment data; url, status, webhookResponse, createResponse
     */
    public function update($paymentId, $id, $status = null, $url = null, $createResponse = null, $webhookResponse = null) {

        if($status == null && $url == null && $webhookResponse == null) return ['message' => 'Invalid Input', 'code' => 400];

        try {
            $check_payment = $this->find($paymentId);
    
            if ($check_payment['code'] !== 200) {
                return ['message' => 'Payment data not found', 'code' => 404];
            }

            $sql = "UPDATE payments SET";
            $params = [];
    
            if ($url !== null) {
                $sql .= " url = ?,";
                $params[] = $url;
            }
            if ($status !== null) {
                $sql .= " status = ?,";
                $params[] = $status;
            }
            if ($createResponse !== null) {
                $sql .= " create_response = ?,";
                $params[] = $createResponse;
            }
            if ($webhookResponse !== null) {
                $sql .= " webhook_response = ?,";
                $params[] = $webhookResponse;
            }
    
            $sql .= " id = ?,";
            $params[] = $id;

            $sql = rtrim($sql, ',') . " WHERE payment_id = ?";
            $params[] = $paymentId;

            $result = $this->databaseService->executeQuery($sql, $params);
    
            if (is_array($result)) {
                return ['message' => $result['message'], 'code' => 500];
            }
    
            return ['message' => 'Updated', 'code' => 200];
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
    
    /**
     * Generate UUID for payments table
     */
    private function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
