<?php
namespace App\Controllers;

use App\Services\PaymentService;
use App\Core\Response;

class PaymentController {
    private $paymentService;

    public function __construct() {
        $this->paymentService = new PaymentService();
    }

    /**
     * Create payment url and store data in DB
     */
    public function createPayment() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['customerId']) || empty($data['amount']) || empty($data['currency'])) {
            return Response::send(['message' => 'Invalid input', 'code' => 400], 400);
        }

        return Response::send($this->paymentService->createPaymentUrl($data), 200);
    }

    /**
     * Update payment in DB with WebHook
     */
    public function webhookUpdate() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['paymentId']) && empty($data['signature'])) return Response::send(['message' => 'Invalid input', 'code' => 400], 400);

       /*
         Here should go checking signature $signature = sha256(id+status+signatureKey)
         but I coudn't find what is signatureKey in Noda API documentation
        */ 

        $paymentId = $this->paymentService->findPaymentByNodaID($data['paymentId']);

        if($paymentId['code'] === 200 && empty($paymentId['data'])) return Response::send(['message' => 'Invalid input', 'code' => 400], 400);

        return Response::send($this->paymentService->updatePaymentData($paymentId['data']['payment_id'], $data['paymentId'], $data['status'], null, null, json_encode($data)), 200);
    }

}
