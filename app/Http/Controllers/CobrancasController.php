<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CobrancasController extends Controller
{
    public function __construct(
        private MercadoPagoService $mpService
    ) {}

    public function webhook(Request $request): JsonResponse
    {
        $asaasToken = config('payments.asaas.webhook_token');

        if ($request->header('asaas-access-token') && $asaasToken) {
            if ($request->header('asaas-access-token') !== $asaasToken) {
                Log::warning('Webhook Asaas rejeitado: token inválido');

                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->handleAsaasWebhook($request);
        }

        $paymentId = $request->input('data.id')
            ?? $request->input('data_id')
            ?? $request->input('id');

        if ($paymentId) {
            return $this->handleMercadoPagoWebhook($paymentId);
        }

        Log::warning('Webhook recebido com formato desconhecido', ['payload' => $request->all()]);

        return response()->json(['status' => 'ignored'], 200);
    }

    private function handleAsaasWebhook(Request $request): JsonResponse
    {
        $event = $request->input('event');
        $payment = $request->input('payment');

        if (! $event || ! $payment || ! isset($payment['id'])) {
            return response()->json(['status' => 'ignored'], 200);
        }

        if ($event === 'PAYMENT_CONFIRMED') {
            $this->updateDonationStatus('asaas_operation_id', $payment['id'], 3);
        } elseif ($event === 'PAYMENT_OVERDUE' || $event === 'PAYMENT_DELETED') {
            $this->updateDonationStatus('asaas_operation_id', $payment['id'], 2);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    private function handleMercadoPagoWebhook(string $paymentId): JsonResponse
    {
        try {
            $cobranca = $this->mpService->getPayment($paymentId);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar pagamento MP', ['id' => $paymentId, 'error' => $e->getMessage()]);

            return response()->json(['error' => 'Payment lookup failed'], 422);
        }

        if (($cobranca['status'] ?? '') === 'approved') {
            $this->updateDonationStatus('mp_operation_id', $paymentId, 3);
        } elseif (in_array($cobranca['status'] ?? '', ['cancelled', 'rejected'], true)) {
            $this->updateDonationStatus('mp_operation_id', $paymentId, 2);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    private function updateDonationStatus(string $column, mixed $operationId, int $status): void
    {
        Donation::where($column, $operationId)
            ->where('status', '!=', $status)
            ->update(['status' => $status]);
    }
}
