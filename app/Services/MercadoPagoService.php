<?php

namespace App\Services;

class MercadoPagoService
{
    private mixed $accessToken;
    private $notificationUrl;
    private $client;

    public function __construct()
    {
        $this->accessToken = env('MP_ACCESS_TOKEN');
        $this->notificationUrl = env('MP_NOTIFICATION_URL');
        $this->client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
    }

    public function generatePixPayment($name, $email, $phone, $cpf, $customer_id, $valueToPay)
    {
        $areaCode = substr($phone, 0, 2);
        $phone = substr($phone, 3);
        // Informações
        $payer = [
            //split para pegar o primeiro nome
            "first_name" => explode(' ', $name)[0],
            "last_name" => array_reverse((array)explode(' ', $name)[0]),
            "email" => $email,
            "phone" => [
                "area_code" => $areaCode,
                "number" => $phone
            ],
            'identification' => [
                'type' => 'cpf',
                'number' => $cpf
            ]
        ];

        $informations = [
            "description" => "Compra de Campaign",
            "transaction_amount" => $valueToPay,
            "payment_method_id" => "pix",
            "notification_url" => $this->notificationUrl
        ];

        $payment = json_encode(array_merge(["payer" => $payer], $informations));

        $response = $this->client->request('POST', 'https://api.mercadopago.com/v1/payments', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ],
            'body' => $payment
        ]);
        $httpCode = $response->getStatusCode();
        $response = json_decode($response->getBody(), true);

        if ($httpCode >= 200 && $httpCode < 300) {
            // Mercado Pago response
            $code = $response['point_of_interaction']['transaction_data']['qr_code'] ?? '';
            $base64 = $response['point_of_interaction']['transaction_data']['qr_code_base64'] ?? '';
            $operation_id = $response['id'] ?? '';
            return $this->jsonResponse("success", "", [
                "status" => "success",
                "code" => $code,
                "base64" => $base64,
                "operation_id" => $operation_id
            ]);
        } else {
            return $this->jsonResponse("error", "Failed to communicate with Mercado Pago.");
        }
    }

    private function jsonResponse($status, $message, $data = null)
    {
        $response = [
            "status" => $status,
            "message" => $message,
            "data" => $data
        ];

        return json_encode($response, JSON_PRETTY_PRINT);
    }

    public function getPayment($operation_id)
    {
        $response = $this->client->request('GET', 'https://api.mercadopago.com/v1/payments/' . $operation_id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
