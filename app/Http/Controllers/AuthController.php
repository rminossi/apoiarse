<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Campaign;
use App\Models\User;
use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function showRegisterForm()
    {
        if (Auth::check() === true) {
            return redirect()->route('admin.home');
        }
        return view('auth.register');
    }

    protected function register(Request $request)
    {
        $request['cpf'] = preg_replace('/[^0-9]/', '', (string)$request->cpf);
        $request['phone'] = preg_replace('/[^0-9]/', '', (string)$request->phone);
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cpf' => ['required', 'cpf', 'min:11', 'max:11', 'unique:users'],
            'phone' => ['required', 'min:10', 'max:11']
        ]);

        if ($validator->fails()) {
            $json['message'] = $this->message->error($validator->errors()->first())->render();
            return response()->json($json);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_admin' => false
        ]);

        $asaasService = new AsaasService();

        $asaas_user = $asaasService->createCustomer($user->name, $user->cpf, $user->email, $user->phone, $user->id);
        $user->update([
            'asaas_id' => $asaas_user['id']
        ]);


        Auth::login($user, true);
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.home');
        } else {
            $campaign = session()->get('campaign');
            if ($campaign) {
                session()->forget('campaign');
                return redirect()->route('web.campaign', $campaign);
            }
            return redirect()->route('usuario.home');
        }
    }

    public function showLoginForm(Request $request)
    {
        if (Auth::check() === true) {
            return redirect()->route('admin.home');
        }

        if ($request->get('campaign')) {
            session()->put('campaign', $request->get('campaign'));
        }

        return view('auth.index');
    }

    public function home()
    {
        if (Auth::user()->is_admin) {
            $campaigns = Campaign::all();
            $donations = Donation::all();
            $users = User::all();
            $latest_campaigns = Campaign::orderBy('created_at', 'DESC')->limit(3)->get();
            return view('admin.dashboard', [
                'campaigns' => $campaigns,
                'donations' => $donations,
                'users' => $users,
                'latest_campaigns' => $latest_campaigns,
            ]);
        } else {
            $user = Auth::user();
            return view('users.dashboard', [
                'campaigns' => $user->campaigns,
                'donations' => $user->donations,
                'last_campaigns' => $user->campaigns()->orderBy('created_at', 'DESC')->limit(3)->get(),
                'last_donations' => $user->donations()->orderBy('created_at', 'DESC')->limit(3)->get(),
            ]);
        }
    }

    public function login(Request $request)
    {
        if (in_array('', $request->only('email', 'password'))) {
            $json['message'] = $this->message->error('Informe todos os dados para avançar ;)')->render();
            return response()->json($json);
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $json['message'] = $this->message->error('Email inválido')->render();
            return response()->json($json);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            $json['message'] = $this->message->error('Email ou senha inválidos, favor checar os dados.')->render();
            return response()->json($json);
        }
        $this->authenticated($request->getClientIp());
        if (Auth::user()->is_admin) {
            $json['redirect'] = route('admin.home');
        } else {
            $campaign = session()->get('campaign');
            if ($campaign) {
                session()->forget('campaign');
                $json['redirect'] = route('web.campaign', ['slug' => $campaign]);
            } else {
                $json['redirect'] = route('usuario.home');
            }
        }
        return response()->json($json);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('web.home');
    }

    private function authenticated(string $ip)
    {
        $user = User::where('id', Auth::user()->id);
        $user->update([
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip
        ]);
    }
}
