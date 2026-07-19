<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AsaasService
{
    private Client $client;

    private string $token;

    private string $baseUrl;

    public function __construct()
    {
        $this->token = config('payments.asaas.api_key', '');
        $this->baseUrl = rtrim(config('payments.asaas.base_url', 'https://api.asaas.com/v3'), '/');
        $this->client = new Client([
            'verify' => config('app.env') !== 'local',
        ]);
    }

    public function createCustomer($name, $document, $email, $phone, $id)
    {
        return $this->request('POST', '/customers', [
            'name' => $name,
            'cpfCnpj' => $document,
            'email' => $email,
            'phone' => $phone,
            'externalReference' => (string) $id,
        ]);
    }

    public function getCustomer($id)
    {
        return $this->request('GET', '/customers/'.$id);
    }

    public function updateCustomer($id, $name, $phone, $email)
    {
        return $this->request('PUT', '/customers/'.$id, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);
    }

    public function getPixQrCodeAsaas($user, float|int $valor): mixed
    {
        $cobranca = $this->criarCobrancaPix(
            $user->asaas_id,
            $valor,
            'Apoiar-se Online',
            now()->addDays(1)->format('Y-m-d')
        );
        $qrCode = $this->gerarPixQrCode($cobranca['id']);
        $qrCode['qrCode'] = $qrCode['payload'];

        return [
            'qrCode' => $qrCode,
            'operation_id' => $cobranca['id'],
        ];
    }

    public function gerarPixQrCode($id)
    {
        return $this->request('POST', '/payments/'.$id.'/pixQrCode');
    }

    public function criarCobrancaPix($id, $valor, $descricao, $data_vencimento)
    {
        return $this->request('POST', '/payments', [
            'customer' => $id,
            'value' => $valor,
            'description' => $descricao,
            'billingType' => 'PIX',
            'dueDate' => $data_vencimento,
        ]);
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
    ) {
        try {
            return $this->request('POST', '/payments', [
                'customer' => $asaas_id,
                'billingType' => 'CREDIT_CARD',
                'value' => $valor,
                'description' => $descricao,
                'dueDate' => now()->format('Y-m-d'),
                'creditCard' => [
                    'number' => $cardNumber,
                    'holderName' => $cardHolderName,
                    'expiryMonth' => $cardMonth,
                    'expiryYear' => $cardYear,
                    'ccv' => $cardCvv,
                ],
                'creditCardHolderInfo' => [
                    'name' => $cardHolderName,
                    'phone' => $cardPhone,
                    'email' => $cardEmail,
                    'cpfCnpj' => $cardCpfCnpj,
                    'postalCode' => $cardPostalCode,
                    'addressNumber' => $cardAddressNumber,
                    'addressComplement' => $cardAddressComplement,
                ],
                'remoteIp' => $remoteIp,
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if ($response) {
                return json_decode($response->getBody(), true);
            }

            return ['errors' => [['description' => 'Erro ao processar pagamento.']]];
        }
    }

    private function request(string $method, string $path, array $body = []): array
    {
        $options = [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ];

        if (! empty($body)) {
            $options['json'] = $body;
        }

        $response = $this->client->request($method, $this->baseUrl.$path, $options);

        return json_decode($response->getBody(), true);
    }
}
