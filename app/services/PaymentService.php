<?php
namespace App\Services;

use App\Core\Request;
use App\Models\Payment;
use App\Models\User;

class PaymentService {
    private $nodaUrl;

    private $nodaApiKey;

    private $payment;

    private $user;

    public function __construct() {
        $this->nodaUrl = $_ENV['NODA_API_URL'];
        $this->nodaApiKey = $_ENV['NODA_API_KEY'];
        $this->payment = new Payment();
        $this->user = new User();
    }

    /**
     * Call Noda API for create payment url
     * Store all data in DB
     */
    public function createPaymentUrl($data) {
    
        $user_exist = $this->user->find($data['customerId']);

        if($user_exist['code'] === 200) {
            $stored_payment = $this->payment->insert($data);

            if($stored_payment['code'] === 201) {
                $payment_created = Request::curlRequest($this->nodaUrl, 'POST', $this->nodaApiKey, $stored_payment['data']);
                $payment_created_decoded = json_decode($payment_created, true);

                if(isset($payment_created_decoded['Errors']) && count($payment_created_decoded['Errors']) > 0){
                    return ['message' => $payment_created_decoded['Errors'][0]['Message'], 'code' => $payment_created_decoded['Errors'][0]['Code']];
                }

                return $this->updatePaymentData(
                    $stored_payment['data']['paymentId'], 
                    $payment_created_decoded['id'], 
                    $payment_created_decoded['status'], 
                    $payment_created_decoded['url'], 
                    json_encode($payment_created_decoded)
                );
            } 
            
            return $stored_payment;
           
        } else {
            return ['message' => 'User with that ID not found', 'code' => 404];
        }        
    }


    public function updatePaymentData($paymentId, $id, $status = null, $url = null, $createResponse = null, $webhookResponse = null) {
        return $this->payment->update($paymentId, $id, $status, $url, $createResponse, $webhookResponse);
    }

    public function findPaymentByNodaID($id) {
        return $this->payment->findPaymentByNodaID($id);
    }

}
