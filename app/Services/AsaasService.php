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

        $response = $this->client->request('POST', 'https://sandbox.asaas.com/api/v3/customers', [
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
        $response = $this->client->request('GET', 'https://sandbox.asaas.com/api/v3/users/' . $id, [
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
        $response = $this->client->request('PUT', 'https://sandbox.asaas.com/api/v3/users/' . $id, [
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

    public function deleteCustomer($id)
    {
        $response = $this->client->request('DELETE', 'https://sandbox.asaas.com/api/v3/users/' . $id, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        return $response;
    }

    public function restoreCustomer($id)
    {
        $response = $this->client->request('POST', 'https://sandbox.asaas.com/api/v3/users/' . $id . '/restore', [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        return $response;
    }


    //Pix
    public function gerarPixQrCode($id)
    {
        $response = $this->client->request('POST', 'https://sandbox.asaas.com/api/v3/payments/' . $id . '/pixQrCode', [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        return $response;
    }


    //Assinatura
    public function criarAssinatura($asaas_id, $plano_id, $data_vencimento, $assinatura_interna)
    {
        try {
            $plano = Plano::find($plano_id);
            $data_vencimento = date('Y-m-d', strtotime($data_vencimento));

            $response = $this->client->request('POST', 'https://sandbox.asaas.com/api/v3/subscriptions', [
                'body' => '{"customer":"' . $asaas_id . '","billingType":"BOLETO","value":"' . $plano['preco'] . '","nextDueDate":"' . $data_vencimento . '","cycle":"MONTHLY","description":"Assinatura - Plano ' . $plano['titulo'] . '","externalReference":"' . $assinatura_interna . '",}',
                'headers' => [
                    'accept' => 'application/json',
                    'access_token' => $this->token,
                    'content-type' => 'application/json',
                ],
            ]);

            $response = json_decode($response->getBody(), true);
            return $response;
        } catch (ClientException $e) {
            dd($e->getRequest()->getBody());
        }
    }

    public function cancelarAssinatura($id)
    {
        $response = $this->client->request('DELETE', 'https://sandbox.asaas.com/api/v3/subscriptions/' . $id, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);
        $response = json_decode($response->getBody(), true);

        return $response;
    }

    public function obterAssinatura($id)
    {
        $response = $this->client->request('GET', 'https://sandbox.asaas.com/api/v3/subscriptions/' . $id, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);
        $response = json_decode($response->getBody(), true);

        return $response;
    }

    public function obterAssinaturas()
    {
        $response = $this->client->request('GET', 'https://sandbox.asaas.com/api/v3/subscriptions', [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        return $response;
    }

    public function obterAssinaturasPorCliente($id)
    {
        $response = $this->client->request('GET', 'https://sandbox.asaas.com/api/v3/subscriptions?customer=' . $id, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
    }

    public function atualizarAssinatura($id, $plano_id, $data_vencimento, $assinatura_interna)
    {
        $plano = Plano::find($plano_id);

        $response = $this->client->request('PUT', 'https://sandbox.asaas.com/api/v3/subscriptions/' . $id, [
            'body' => '{"value":"' . $plano['preco'] . '","nextDueDate":"' . $data_vencimento . '","externalReference":"' . $assinatura_interna . '","description":"Assinatura - Plano ' . $plano['titulo'] . '"}',
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
    public function criarCobranca($id, $valor, $descricao, $data_vencimento)
    {
        $response = $this->client->request('POST', 'https://sandbox.asaas.com/api/v3/payments', [
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

    public function obterCobranca($id)
    {
        $response = $this->client->request('GET', 'https://sandbox.asaas.com/api/v3/payments/' . $id, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
    }

    public function listarCobrancasPorCliente($id)
    {
        $response = $this->client->request('GET', 'https://sandbox.asaas.com/api/v3/payments?customer=' . $id, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
    }

    //    //curl --request POST \
    //    //     --url https://sandbox.asaas.com/api/v3/payments/id/receiveInCash \
    //    //     --header 'accept: application/json' \
    //    //     --header 'content-type: application/json'
    //paymentDate, value

    public function receberCobranca($id)
    {
        $cobranca = Cobranca::find($id);
        $assinatura = Assinatura::where( 'asaas_id', $cobranca->assinatura_id)->first();
        $plano = Plano::find($assinatura->plano_id);
        $response = $this->client->request('POST', 'https://sandbox.asaas.com/api/v3/payments/' . $cobranca->id_asaas . '/receiveInCash', [
            'body' => '{"paymentDate":"' . date('Y-m-d') . '","value":"' . $plano->preco . '"}',
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $this->token,
                'content-type' => 'application/json',
            ],
        ]);
        $response = json_decode($response->getBody(), true);
        return $response;
    }
}
