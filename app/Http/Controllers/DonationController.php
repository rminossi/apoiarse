<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidDonationAmountException;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\AsaasService;
use App\Services\DonationFeeCalculator;
use App\Services\MercadoPagoService;
use App\Services\PaymentSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    public function __construct(
        private AsaasService $asaasService,
        private MercadoPagoService $mercadoPagoService,
        private DonationFeeCalculator $feeCalculator,
        private PaymentSettingsService $paymentSettings
    ) {}

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:1,2,3',
        ]);

        $donation = Donation::findOrFail($id);
        $campaign_id = $donation->campaign_id;
        $data = ['status' => $validated['status']];

        if ((int) $validated['status'] === 1) {
            $data['user_id'] = null;
        }

        $donation->update($data);

        return redirect()->route('admin.campaigns.edit', [
            'campaign' => $campaign_id,
            'donations' => true,
        ])->with(['message' => 'Doação atualizada com sucesso!']);
    }

    public function myDonations()
    {
        $user = auth()->user();
        $donations = $user->donations()->paginate();

        return view('users.donations.index', [
            'donations' => $donations,
        ]);
    }

    public function getPixQrCode(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'amount' => 'required|string',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Autenticação necessária.'], 401);
        }

        $campaign = Campaign::with('user')->findOrFail($request->campaign_id);

        if ((int) $campaign->status !== 1) {
            return response()->json(['error' => 'Esta campanha não está ativa para receber doações.'], 422);
        }

        $amount = $this->parseAmount($request->amount);

        try {
            $feeResult = $this->feeCalculator->calculate($amount, 'pix', $campaign);
        } catch (InvalidDonationAmountException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $settings = $this->paymentSettings->get();
        $split = $this->feeCalculator->canApplyAsaasSplit($campaign, $feeResult, $settings)
            ? $this->feeCalculator->buildAsaasSplit($campaign, $feeResult, $settings)
            : [];

        DB::beginTransaction();

        try {
            if ($feeResult['gateway'] === 'asaas') {
                $cobranca = $this->asaasService->getPixQrCodeAsaas($user, $amount, $split);

                Donation::create($this->donationPayload($user, $campaign, $amount, $feeResult, [
                    'asaas_operation_id' => $cobranca['operation_id'],
                    'payment_method' => 'PIX',
                    'pix_qrcode' => $cobranca['qrCode']['encodedImage'],
                    'pix_key' => $cobranca['qrCode']['qrCode'],
                ], $split !== []));
            } else {
                $cobranca = $this->mercadoPagoService->getQrCodeMercadoPago($user, $amount);

                Donation::create($this->donationPayload($user, $campaign, $amount, $feeResult, [
                    'mp_operation_id' => $cobranca['operation_id'],
                    'payment_method' => 'PIX',
                    'pix_qrcode' => $cobranca['qrCode']['encodedImage'],
                    'pix_key' => $cobranca['qrCode']['qrCode'] ?? null,
                ], false));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao gerar PIX', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Erro ao processar pagamento. Tente novamente.'], 500);
        }

        return [
            'slug' => $campaign->slug,
            'confirmed' => 'confirmar',
            'qrCode' => $cobranca['qrCode'],
            'fees' => $this->publicFeeSummary($feeResult),
        ];
    }

    public function payWithCard(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'amount' => 'required|string',
            'cardNumber' => 'required|string',
            'cardHoldersName' => 'required|string',
            'cardCvv' => 'required|string',
            'cardMonth' => 'required|string',
            'cardYear' => 'required|string',
            'cardPhone' => 'required|string',
            'cardEmail' => 'required|email',
            'cardCpf' => 'required|string',
            'cardPostalCode' => 'required|string',
            'cardAddressNumber' => 'required|string',
            'cardAddressComplement' => 'nullable|string',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Autenticação necessária.'], 401);
        }

        $campaign = Campaign::with('user')->findOrFail($request->campaign_id);

        if ((int) $campaign->status !== 1) {
            return response()->json(['error' => 'Esta campanha não está ativa para receber doações.'], 422);
        }

        $amount = $this->parseAmount($request->amount);

        try {
            $feeResult = $this->feeCalculator->calculate($amount, 'card', $campaign);
        } catch (InvalidDonationAmountException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $settings = $this->paymentSettings->get();
        $split = $this->feeCalculator->canApplyAsaasSplit($campaign, $feeResult, $settings)
            ? $this->feeCalculator->buildAsaasSplit($campaign, $feeResult, $settings)
            : [];

        $cardCpf = preg_replace('/[^0-9]/', '', $request->cardCpf);
        $cardPhone = preg_replace('/[^0-9]/', '', $request->cardPhone);

        DB::beginTransaction();

        try {
            $cobranca = $this->asaasService->criarCobrancaCartao(
                $user->asaas_id,
                $amount,
                'Apoiar-se Online',
                $request->cardNumber,
                $request->cardHoldersName,
                $request->cardCvv,
                $request->cardMonth,
                $request->cardYear,
                $cardPhone,
                $request->cardEmail,
                $cardCpf,
                $request->cardPostalCode,
                $request->cardAddressNumber,
                $request->cardAddressComplement ?? '',
                $request->ip(),
                $split
            );

            if (isset($cobranca['errors'])) {
                DB::rollBack();

                return response()->json($cobranca, 422);
            }

            Donation::create($this->donationPayload($user, $campaign, $amount, $feeResult, [
                'asaas_operation_id' => $cobranca['id'],
                'payment_method' => 'CC',
            ], $split !== []));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar cartão', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Erro ao processar pagamento.'], 500);
        }

        return [
            'status' => $cobranca['status'],
            'invoice' => $cobranca['invoiceUrl'] ?? null,
            'fees' => $this->publicFeeSummary($feeResult),
        ];
    }

    private function donationPayload($user, Campaign $campaign, float $amount, array $feeResult, array $extra, bool $splitApplied): array
    {
        return array_merge([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => $amount,
            'status' => 1,
            'gateway' => $feeResult['gateway'],
            'platform_fee' => $feeResult['platform_fee'],
            'gateway_fee' => $feeResult['gateway_fee'],
            'net_amount' => $feeResult['net_amount'],
            'fee_mode_applied' => $feeResult['fee_mode_applied'],
            'split_applied' => $splitApplied,
            'fee_breakdown' => $feeResult['breakdown'],
        ], $extra);
    }

    private function publicFeeSummary(array $feeResult): array
    {
        return [
            'platform_fee' => $feeResult['platform_fee'],
            'gateway_fee' => $feeResult['gateway_fee'],
            'net_amount' => $feeResult['net_amount'],
            'fee_mode_applied' => $feeResult['fee_mode_applied'],
        ];
    }

    private function parseAmount(string $amount): float
    {
        $amount = str_replace('R$ ', '', $amount);
        $amount = str_replace('.', '', $amount);
        $amount = str_replace(',', '.', $amount);

        return (float) $amount;
    }
}
