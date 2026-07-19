<?php

namespace App\Http\Controllers;

use App\Mail\Contact;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\Site;
use App\Models\SiteContent;
use App\Models\User;
use App\Services\AsaasService;
use App\Support\Message;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        $categories = Cache::remember('categories.active', 3600, fn () => Category::active()->get());

        $featuredCampaigns = Campaign::with(['category', 'user', 'donations'])
            ->active()
            ->featured()
            ->limit(6)
            ->get();

        $activeCampaigns = Campaign::with(['category', 'user', 'donations'])
            ->active()
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $finishedCampaigns = Campaign::with(['category', 'donations'])
            ->finished()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $stats = [
            'total_raised' => Donation::where('status', 3)->sum('amount'),
            'active_campaigns' => Campaign::active()->count(),
            'supporters' => Donation::where('status', 3)->distinct('user_id')->count('user_id'),
        ];

        $testimonials = SiteContent::get('testimonials', []);
        $howItWorks = SiteContent::get('how_it_works', []);

        $head = $this->seo->render(
            'Apoiar-se Online - Participe de campanhas com maior segurança!',
            'Plataforma de crowdfunding para causas sociais. Crie campanhas, arrecade via PIX ou cartão.',
            url('/'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );

        return view('web.home', compact(
            'head', 'categories', 'featuredCampaigns', 'activeCampaigns',
            'finishedCampaigns', 'stats', 'testimonials', 'howItWorks'
        ));
    }

    public function campaigns(Request $request)
    {
        $categories = Cache::remember('categories.active', 3600, fn () => Category::active()->get());

        $query = Campaign::with(['category', 'user', 'donations']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $status = $request->get('status', 'active');
        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'finished') {
            $query->finished();
        }

        $sort = $request->get('sort', 'recent');
        match ($sort) {
            'supporters' => $query->withCount(['donations as supporters' => fn ($q) => $q->where('status', 3)])->orderByDesc('supporters'),
            'almost_goal' => $query->orderByRaw('(SELECT COALESCE(SUM(amount),0) FROM donations WHERE donations.campaign_id = campaigns.id AND donations.status = 3) / NULLIF(campaigns.goal, 0) DESC'),
            default => $query->orderByDesc('created_at'),
        };

        $campaigns = $query->paginate(12)->withQueryString();

        $head = $this->seo->render(
            'Campanhas - Apoiar-se Online',
            'Explore campanhas ativas e apoie causas que importam.',
            route('web.campaigns'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );

        return view('web.campaigns', compact('head', 'campaigns', 'categories'));
    }

    public function campaign($slug, $confirmed = null)
    {
        $campaign = Campaign::with(['category', 'user', 'donations.user', 'updates.user', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        $coverUrl = url($campaign->cover());
        $head = $this->seo->render(
            "Apoiar-se - {$campaign->title}",
            strip_tags($campaign->short_description ?? $campaign->description),
            route('web.campaign', $campaign->slug),
            $coverUrl
        );

        $supporters = $campaign->donations()->where('status', 3)->with('user')->latest()->get();

        return view('web.campaign', compact('head', 'campaign', 'supporters', 'confirmed'));
    }

    public function contato()
    {
        $site = Site::where('id', '1')->first();
        $head = $this->seo->render(
            'Contato - Apoiar-se Online',
            'Entre em contato com a equipe Apoiar-se.',
            route('web.contato'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );

        return view('web.contato', compact('site', 'head'));
    }

    public function sobre()
    {
        $faq = SiteContent::get('faq', []);
        $howItWorks = SiteContent::get('how_it_works', []);

        $head = $this->seo->render(
            'Sobre - Apoiar-se Online',
            'Conheça a plataforma Apoiar-se e como funciona o crowdfunding.',
            route('web.sobre'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );

        return view('web.sobre', compact('head', 'faq', 'howItWorks'));
    }

    public function enviarContato(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $data = [
            'reply_name' => $validated['fullname'],
            'reply_email' => $validated['email'],
            'message' => $validated['message'],
        ];
        try {
            Mail::send(new Contact($data));
            $json['message'] = $this->message->success('Mensagem enviada com sucesso.')->render();
        } catch (\Exception $e) {
            Log::error('Erro ao enviar contato', ['error' => $e->getMessage()]);
            $json['message'] = $this->message->error('Erro ao enviar mensagem, tente novamente.')->render();
        }

        return response()->json($json);
    }

    public function getUserByCpf($cpf)
    {
        $user = User::where('cpf', $cpf)->get();

        if (! empty($user)) {
            return $user;
        } else {
            return null;
        }
    }

    public function minhasDoacoes(Request $request)
    {
        $user = Auth::user();
        $donations = Donation::where('user_id', $user->id)
            ->where('status', 3)
            ->with('campaign')
            ->orderByDesc('created_at')
            ->get();

        $head = $this->seo->render(
            'Minhas Doações - Apoiar-se',
            'Histórico das suas doações.',
            route('web.my-donations'),
            asset('assets/frontend/images/apoiarse_logo.png')
        );

        return view('web.minhas_doacoes', compact('user', 'donations', 'head'));
    }
}
