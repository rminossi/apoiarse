<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\AsaasService;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CobrancasController extends Controller
{
    private $donation;
    private $mpService;
    private $asaasService;

    public function __construct()
    {
        $this->mpService = new MercadoPagoService();
        $this->asaasService = new AsaasService();
        $this->donation = new Donation();
    }

    //receive webhook
    public function webhook(Request $request): void
    {
        $event = $request->event;

        if (isset($event)) {
            if ($event == 'PAYMENT_CONFIRMED') {
                $this->marcarCobrancaAsaasComoRecebida($request->payment);
            } elseif ($event == 'PAYMENT_OVERDUE') {
                $this->marcarCobrancaAsaasComoVencida($request->payment);
            }
        } else {
            if (isset($request->data_id)) {
                $cobranca = $this->mpService->getPayment($request->data_id);

                if ($cobranca['status'] == 'approved') {
                    Log::debug($cobranca);
                    Log::debug($request);
                    $this->marcarCobrancaMPComoRecebida($request->data_id);
                } elseif ($cobranca['status'] == 'cancelled') {
                    $this->marcarCobrancaMPComoVencida($request->data_id);
                }
            }
        }
    }

    private function marcarCobrancaAsaasComoRecebida(mixed $payment): void
    {
        $this->donation
            ->where('asaas_operation_id', $payment['id'])
            ->update(['status' => 3]);
    }

    private function marcarCobrancaAsaasComoVencida(mixed $payment): void
    {
        $this->donation
            ->where('asaas_operation_id', $payment['id'])
            ->update(['status' => 1]);
    }

    private function marcarCobrancaMPComoRecebida(mixed $transaction_id): void
    {
        Log::debug($transaction_id);
        $this->donation
            ->where('mp_operation_id', $transaction_id)
            ->update(['status' => 3]);
    }

    private function marcarCobrancaMPComoVencida(mixed $transaction_id): void
    {
        $this->donation
            ->where('mp_operation_id', $transaction_id)
            ->update(['status' => 1]);
    }
}
