<?php

namespace App\Http\Controllers;

use App\Mail\Contact;
use App\Models\Donation;
use App\Models\Campaign;
use App\Models\Site;
use App\Models\User;
use App\Services\AsaasService;
use App\Services\MercadoPagoService;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;


class WebController extends Controller
{

    private AsaasService $asaasService;
    private MercadoPagoService $mercadoPagoService;

    public function __construct(Seo $seo, AsaasService $asaasService, MercadoPagoService $mercadoPagoService)
    {
        $this->seo = $seo;
        $this->asaasService = $asaasService;
        $this->mercadoPagoService = $mercadoPagoService;
        $site = Site::where('id', '1')->first();
        Session::put('site-data', $site);
        Session::save();
    }

    public function home()
    {
        $activeCampaigns = Campaign::where('status', 1)->orderBy('created_at', 'DESC')->limit(3)->get();
        $finishedCampaigns = Campaign::where('status', 3)->orderBy('created_at', 'DESC')->limit(3)->get();
        $head = $this->seo->render(
            'Apoiar-se Online - Participe de campanhas com maior segurança!',
            "Apoiar-se Online - Participe de campanhas com maior segurança!",
            url('/'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );
        return view('web.home', [
            'head' => $head,
            'activeCampaigns' => $activeCampaigns,
            'finishedCampaigns' => $finishedCampaigns,
        ]);
    }

    public function campaigns()
    {
        $activeCampaigns = Campaign::where('status', 1)->orderBy('created_at', 'DESC')->get();
        $finishedCampaigns = Campaign::where('status', 3)->orderBy('created_at', 'DESC')->get();
        $head = $this->seo->render(
            'Apoiar-se Online - Nossas Campanhas',
            'Apoiar-se Online - Nossas Campanhas',
            route('web.campaigns'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );
        return view('web.campaigns', [
            'head' => $head,
            'activeCampaigns' => $activeCampaigns,
            'finishedCampaigns' => $finishedCampaigns,
        ]);
    }

    public function campaign($slug, $confirmed = null)
    {
        $campaign = Campaign::where('slug', $slug)->first();
        $site = Site::where('id', '1')->first();
        $head = $this->seo->render(
            "Apoiar-se Online - $campaign->title",
            $campaign->description,
            route('web.campaign', $campaign->slug),
            asset('assets/frontend/images/apoiarse_logo.png')
        );
        if ($confirmed) {
            return view('web.campaign', [
                'head' => $head,
                'campaign' => $campaign,
                'site' => $site,
                'confirmed' => $confirmed
            ]);
        } else {
            return view('web.campaign', [
                'head' => $head,
                'campaign' => $campaign
            ]);
        }
    }

    public function contato()
    {
        $site = Site::where('id', '1')->first();
        $head = $this->seo->render(
            'Apoiar-se Online - Contato',
            'Apoiar-se Online - Contato',
            route('web.contato'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );
        return view('web.contato', [
            'site' => $site,
            'head' => $head
        ]);
    }

    public function enviarContato(Request $request)
    {
        $data = [
            'reply_name' => $request->fullname,
            'reply_email' => $request->email,
            'message' => $request->message,
        ];
        Mail::send(new Contact($data));
        return redirect()->route('contato');
        //return new Contact($data);
    }

    public function getUserByCpf($cpf)
    {
        $user = User::where('cpf', $cpf)->get();

        if (!empty($user)) {
            return $user;
        } else {
            return null;
        }
    }

    public function doar(Request $request)
    {
        DB::beginTransaction();
        $user_id = $this->updateOrInsertUser($request);
        $user = User::find($user_id);
        $campaign = Campaign::where('id', $request->campaign_id)->first();
        //remove R$ da string
        $amount = str_replace('R$ ', '', $request->amount);
        $amount = str_replace('.', '', $amount);
        $amount = str_replace(',', '.', $amount);

        if ($amount > 200) {
            $cobranca = $this->getPixQrCodeAsaas($user, $amount);

            Donation::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'asaas_operation_id' => $cobranca['operation_id'],
                'status' => 1
            ]);

        } else {
            $cobranca = $this->getQrCodeMercadoPago($user, $amount);
            Donation::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'mp_operation_id' => $cobranca['operation_id'],
                'status' => 1
            ]);
        }
        DB::commit();

        return redirect()->route('web.campaign', [
            'slug' => $campaign->slug,
            'confirmed' => 'confirmar',
            'qrCode' => $cobranca['qrCode']
        ]);
    }

    private function updateOrInsertUser(Request $request)
    {
        $user_id = null;
        if ($request->user_id) {
            $user = User::where('id', $request->user_id)->first();
            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email
            ]);
            if ($user->asaas_id) {
                $this->asaasService->updateCustomer($user->asaas_id, $user->name, $user->phone, $user->email);
            }
            $user_id = $user->id;
        } else {
            $user = User::create([
                'cpf' => $request->cpf,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $asaas_user = $this->asaasService->createCustomer($user->name, $user->cpf, $user->email, $user->phone, $user_id);
            $user->update([
                'asaas_id' => $asaas_user['id']
            ]);
            $user_id = $user->id;
        }

        return $user_id;
    }

    public function minhasDoacoes(Request $request)
    {
        $cpf = $request->cpf;
        if ($cpf) {
            $user = User::where('cpf', $cpf)->first();
            $campaigns = Campaign::whereHas('donations', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 3);
            })->get();
            return view('web.minhas_doacoes', [
                'user' => $user,
                'campaigns' => $campaigns
            ]);
        }
        return view('web.minhas_doacoes');

    }

    /**
     * @param $user
     * @param float|int $valor
     * @return array
     */
    public function getQrCodeMercadoPago($user, float|int $valor): array
    {
        $cobranca = json_decode($this->mercadoPagoService->generatePixPayment($user->name, $user->email, $user->phone, $user->cpf, $user->id, $valor));
        $qrCode['qrCode'] = $cobranca->data->code;
        $qrCode['encodedImage'] = $cobranca->data->base64;
        $operation_id = $cobranca->data->operation_id;
        return [
            "qrCode" => $qrCode,
            "operation_id" => $operation_id
        ];
    }

    /**
     * @param $user
     * @param float|int $valor
     * @return mixed
     */
    public function getPixQrCodeAsaas($user, float|int $valor): mixed
    {
        $cobranca = $this->asaasService->criarCobranca($user->asaas_id, $valor, 'Apoiar-se Online', now()->addDays(1)->format('Y-m-d'));
        $qrCode = $this->asaasService->gerarPixQrCode($cobranca['id']);
        return [
            "qrCode" => $qrCode,
            "operation_id" => $cobranca['id']
        ];
    }
}
