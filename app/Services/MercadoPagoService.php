<?php

namespace App\Services;

use GuzzleHttp\Client;

class MercadoPagoService
{
    private string $accessToken;

    private ?string $notificationUrl;

    private Client $client;

    public function __construct()
    {
        $this->accessToken = config('payments.mercadopago.access_token', '');
        $this->notificationUrl = config('payments.mercadopago.notification_url');
        $this->client = new Client([
            'verify' => config('app.env') !== 'local',
        ]);
    }

    public function getQrCodeMercadoPago($user, float|int $valor): array
    {
        $cobranca = json_decode($this->generatePixPayment(
            $user->name,
            $user->email,
            $user->phone,
            $user->getRawOriginal('cpf'),
            $user->id,
            $valor
        ));
        $qrCode['qrCode'] = $cobranca->data->code;
        $qrCode['encodedImage'] = $cobranca->data->base64;
        $operation_id = $cobranca->data->operation_id;

        return [
            'qrCode' => $qrCode,
            'operation_id' => $operation_id,
        ];
    }

    public function generatePixPayment($name, $email, $phone, $cpf, $customer_id, $valueToPay)
    {
        $phoneDigits = preg_replace('/[^0-9]/', '', (string) $phone);
        $areaCode = substr($phoneDigits, 0, 2);
        $phoneNumber = substr($phoneDigits, 2);
        $nameParts = explode(' ', trim($name));
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : $firstName;

        $payer = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => [
                'area_code' => $areaCode,
                'number' => $phoneNumber,
            ],
            'identification' => [
                'type' => 'CPF',
                'number' => preg_replace('/[^0-9]/', '', (string) $cpf),
            ],
        ];

        $informations = [
            'description' => 'Apoiar-se - Pagamento de contribuições',
            'transaction_amount' => (float) $valueToPay,
            'payment_method_id' => 'pix',
            'notification_url' => $this->notificationUrl,
        ];

        $response = $this->client->request('POST', 'https://api.mercadopago.com/v1/payments', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->accessToken,
                'Content-Type' => 'application/json',
                'X-Idempotency-Key' => $customer_id.'_'.now()->timestamp,
            ],
            'json' => array_merge(['payer' => $payer], $informations),
        ]);

        $httpCode = $response->getStatusCode();
        $responseData = json_decode($response->getBody(), true);

        if ($httpCode >= 200 && $httpCode < 300) {
            $code = $responseData['point_of_interaction']['transaction_data']['qr_code'] ?? '';
            $base64 = $responseData['point_of_interaction']['transaction_data']['qr_code_base64'] ?? '';
            $operation_id = $responseData['id'] ?? '';

            return $this->jsonResponse('success', '', [
                'status' => 'success',
                'code' => $code,
                'base64' => $base64,
                'operation_id' => $operation_id,
            ]);
        }

        return $this->jsonResponse('error', 'Failed to communicate with Mercado Pago.');
    }

    private function jsonResponse($status, $message, $data = null)
    {
        return json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], JSON_PRETTY_PRINT);
    }

    public function getPayment($operation_id)
    {
        $response = $this->client->request('GET', 'https://api.mercadopago.com/v1/payments/'.$operation_id, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->accessToken,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
