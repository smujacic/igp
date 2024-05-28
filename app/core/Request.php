<?php
namespace App\Core;

class Request {
    public static function uri() {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * cURL request
     */
    public static function curlRequest($url, $method, $apiKey, $data = []) {
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json'
            ),
            CURLOPT_RETURNTRANSFER => true,
        ));
    
        $response = curl_exec($curl);
    
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
    
        curl_close($curl);
    
        if (isset($error_msg)) {
            return ['error' => $error_msg];
        }
    
        return $response;
    }
}