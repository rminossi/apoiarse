<?php

namespace App\Services;

use App\Models\Assinatura;
use App\Models\Cobranca;
use App\Models\Plano;
use GuzzleHttp\Exception\ClientException;

class AsaasService
{
    /**
     * @var \GuzzleHttp\Client
     */
    private \GuzzleHttp\Client $client;

    /**
     * @var string
     */
    private mixed $token;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $this->token = env('ASAAS_API_KEY');
    }

    //Customers
    public function createCustomer($name, $document, $email, $phone, $id)
    {

        $response = $this->client->request('POST', 'https://asaas.com/api/v3/customers', [
            'body' => '{"name":"' . $name . '","cpfCnpj":"' . $document . '","email":"' . $email . '","phone":"' . $phone . '","externalReference":"' . $id . '"}',
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    public function getCustomer($id)
    {
        $response = $this->client->request('GET', 'https://asaas.com/api/v3/users/' . $id, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        return $response;
    }

    public function updateCustomer($id, $name, $phone, $email)
    {
        $response = $this->client->request('PUT', 'https://asaas.com/api/v3/users/' . $id, [
            'body' => '{"name":"' . $name . '","email":"' . $email . '","phone":"' . $phone . '"}',
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        return $response;
    }

    public function getPixQrCodeAsaas($user, float|int $valor): mixed
    {
        $cobranca = $this->criarCobrancaPix($user->asaas_id, $valor, 'Apoiar-se Online', now()->addDays(1)->format('Y-m-d'));
        $qrCode = $this->gerarPixQrCode($cobranca['id']);
        $qrCode['qrCode'] = $qrCode['payload'];
        return [
            "qrCode" => $qrCode,
            "operation_id" => $cobranca['id']
        ];
    }


    //Pix
    public function gerarPixQrCode($id)
    {
        $response = $this->client->request('POST', 'https://asaas.com/api/v3/payments/' . $id . '/pixQrCode', [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        return $response;
    }

    //Cobranças
    public function criarCobrancaPix($id, $valor, $descricao, $data_vencimento)
    {
        $response = $this->client->request('POST', 'https://asaas.com/api/v3/payments', [
            'body' => '{"customer":"' . $id . '","value":"' . $valor . '","description":"' . $descricao . '", "billingType":"PIX","dueDate":"' . $data_vencimento . '"}',
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        return $response;
    }

    public function criarCobrancaCartao(
        $asaas_id,
        $valor,
        $descricao,
        $cardNumber,
        $cardHolderName,
        $cardCvv,
        $cardMonth,
        $cardYear,
        $cardPhone,
        $cardEmail,
        $cardCpfCnpj,
        $cardPostalCode,
        $cardAddressNumber,
        $cardAddressComplement,
        $remoteIp
    )
    {
        $data_vencimento = now()->format('Y-m-d');
        try {
            $response = $this->client->request('POST', 'https://asaas.com/api/v3/payments', [
                'body' => '{"customer":"' . $asaas_id . '", "billingType":"CREDIT_CARD", "value":"' . $valor . '","description":"' . $descricao . '", "dueDate":"' . $data_vencimento . '", "creditCard":{ "number":"' . $cardNumber . '", "holderName":"' . $cardHolderName . '", "expiryMonth":"' . $cardMonth . '", "expiryYear":"' . $cardYear . '", "ccv":"' . $cardCvv . '"}, "creditCardHolderInfo":{ "name":"' . $cardHolderName . '", "phone":"' . $cardPhone . '", "email":"' . $cardEmail . '", "cpfCnpj":"' . $cardCpfCnpj . '", "postalCode":"' . $cardPostalCode . '", "addressNumber":"' . $cardAddressNumber . '", "addressComplement":"' . $cardAddressComplement . '"}, "remoteIp":"' . $remoteIp . '"}',
                'headers' => [
                    'accept' => 'application/json',
                    'access_token' => $this->token,
                    'content-type' => 'application/json',
                ],
            ]);

            $response = json_decode($response->getBody(), true);
            return $response;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $error = json_decode($response->getBody(), true);
            return $error;
        }
    }
}
