<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Services\AsaasService;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{

    private $asaasService;
    private $mercadoPagoService;

    public function __construct(AsaasService $asaasService, MercadoPagoService $mercadoPagoService)
    {
        $this->asaasService = $asaasService;
        $this->mercadoPagoService = $mercadoPagoService;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $donation = Donation::where('id', $id)->first();
        $campaign_id = $donation->campaign_id;
        $status = $request->status;
        if($status === 1) {
            $request->request->add(['user_id' => null]);
        }
        $request->request->add(['status' => $status]);
        if(!$donation->update($request->all())) {
            return redirect()->back()->withInput()->withErrors();
        }
        return redirect()->route('admin.campaigns.edit', [
            'campaign' => $campaign_id,
            'donations' => true
        ])->with(['message' => 'Campaign atualizada com sucesso!']);
    }

    public function myDonations()
    {
        $user = auth()->user();
        $donations = $user->donations()->paginate();
        return view('users.donations.index', [
            'donations' => $donations
        ]);
    }

    public function getPixQrCode(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $campaign = Campaign::where('id', $request->campaign_id)->first();
        //remove R$ da string
        $amount = str_replace('R$ ', '', $request->amount);
        $amount = str_replace('.', '', $amount);
        $amount = str_replace(',', '.', $amount);

        if ($amount > 200) {
            $cobranca = $this->asaasService->getPixQrCodeAsaas($user, $amount);

            Donation::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'asaas_operation_id' => $cobranca['operation_id'],
                'payment_method' => 'PIX',
                'status' => 1
            ]);

        } else {
            $cobranca = $this->mercadoPagoService->getQrCodeMercadoPago($user, $amount);
            Donation::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'mp_operation_id' => $cobranca['operation_id'],
                'payment_method' => 'PIX',
                'status' => 1
            ]);
        }
        DB::commit();

        return [
            'slug' => $campaign->slug,
            'confirmed' => 'confirmar',
            'qrCode' => $cobranca['qrCode']
        ];
    }

    public function payWithCard(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $campaign = Campaign::where('id', $request->campaign_id)->first();
        $cardNumber = $request['cardNumber'];
        $cardHolderName = $request['cardHoldersName'];
        $cardCvv = $request['cardCvv'];
        $cardMonth = $request['cardMonth'];
        $cardYear = $request['cardYear'];
        $cardPhone = $request['cardPhone'];
        $cardEmail = $request['cardEmail'];
        $cardCpf = $request['cardCpf'];
        $cardPostalCode = $request['cardPostalCode'];
        $cardAddressNumber = $request['cardAddressNumber'];
        $cardAddressComplement = $request['cardAddressComplement'];

        //remove R$ da string
        $amount = str_replace('R$ ', '', $request->amount);
        $amount = str_replace('.', '', $amount);
        $amount = str_replace(',', '.', $amount);

        //sanitize cpf and phone
        $cardCpf = preg_replace('/[^0-9]/', '', $cardCpf);
        $cardPhone = preg_replace('/[^0-9]/', '', $cardPhone);

        $cobranca = $this->asaasService->criarCobrancaCartao(
            $user->asaas_id,
            $amount,
            'Apoiar-se Online',
            $cardNumber,
            $cardHolderName,
            $cardCvv,
            $cardMonth,
            $cardYear,
            $cardPhone,
            $cardEmail,
            $cardCpf,
            $cardPostalCode,
            $cardAddressNumber,
            $cardAddressComplement,
            $request->ip()
        );

        if (isset($cobranca['errors'])) {
            DB::rollBack();
            return $cobranca;
        }

        Donation::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => $amount,
            'asaas_operation_id' => $cobranca['id'],
            'payment_method' => 'CC',
            'status' => 1
        ]);

        DB::commit();

        return  [
            'status' => $cobranca['status'],
            'invoice' => $cobranca['invoiceUrl'],
        ];
    }
}
