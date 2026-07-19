<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Services\AsaasService;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    public function __construct(
        private AsaasService $asaasService,
        private MercadoPagoService $mercadoPagoService
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

        $campaign = Campaign::findOrFail($request->campaign_id);

        if ((int) $campaign->status !== 1) {
            return response()->json(['error' => 'Esta campanha não está ativa para receber doações.'], 422);
        }

        $amount = $this->parseAmount($request->amount);

        if ($amount <= 0) {
            return response()->json(['error' => 'Valor inválido.'], 422);
        }

        DB::beginTransaction();

        try {
            if ($amount > 200) {
                $cobranca = $this->asaasService->getPixQrCodeAsaas($user, $amount);

                Donation::create([
                    'user_id' => $user->id,
                    'campaign_id' => $campaign->id,
                    'amount' => $amount,
                    'asaas_operation_id' => $cobranca['operation_id'],
                    'payment_method' => 'PIX',
                    'status' => 1,
                    'pix_qrcode' => $cobranca['qrCode']['encodedImage'],
                    'pix_key' => $cobranca['qrCode']['qrCode'],
                ]);
            } else {
                $cobranca = $this->mercadoPagoService->getQrCodeMercadoPago($user, $amount);

                Donation::create([
                    'user_id' => $user->id,
                    'campaign_id' => $campaign->id,
                    'amount' => $amount,
                    'mp_operation_id' => $cobranca['operation_id'],
                    'payment_method' => 'PIX',
                    'status' => 1,
                    'pix_qrcode' => $cobranca['qrCode']['encodedImage'],
                    'pix_key' => $cobranca['qrCode']['qrCode'] ?? null,
                ]);
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

        $campaign = Campaign::findOrFail($request->campaign_id);

        if ((int) $campaign->status !== 1) {
            return response()->json(['error' => 'Esta campanha não está ativa para receber doações.'], 422);
        }

        $amount = $this->parseAmount($request->amount);

        if ($amount <= 0) {
            return response()->json(['error' => 'Valor inválido.'], 422);
        }

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
                $request->ip()
            );

            if (isset($cobranca['errors'])) {
                DB::rollBack();

                return response()->json($cobranca, 422);
            }

            Donation::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'asaas_operation_id' => $cobranca['id'],
                'payment_method' => 'CC',
                'status' => 1,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar cartão', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Erro ao processar pagamento.'], 500);
        }

        return [
            'status' => $cobranca['status'],
            'invoice' => $cobranca['invoiceUrl'] ?? null,
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
