<?php

namespace App\Http\Controllers;

use App\Mail\Contact;
use App\Models\Donation;
use App\Models\Campaign;
use App\Models\Site;
use App\Models\User;
use App\Services\AsaasService;
use App\Services\MercadoPagoService;
use App\Support\Message;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;


class WebController extends Controller
{

    public function __construct(Seo $seo, AsaasService $asaasService, Message $message)
    {
        $this->seo = $seo;
        $this->message = $message;
        $this->asaasService = $asaasService;
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
                'campaign' => $campaign,
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


    public function sobre()
    {
        $head = $this->seo->render(
            'Apoiar-se Online - Sobre',
            'Apoiar-se Online - Sobre',
            route('web.sobre'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );
        return view('web.sobre', [
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
        try {
            Mail::send(new Contact($data));
            $json['message'] = $this->message->success('Mensagem enviada com sucesso.')->render();
        } catch (\Exception $e) {
            $json['message'] = $this->message->error('Erro ao enviar mensagem, tente novamente.')->render();
        }

        return response()->json($json);
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

    public function testLog(): void
    {
        Log::debug('aaaaaaaaa');
    }
}
