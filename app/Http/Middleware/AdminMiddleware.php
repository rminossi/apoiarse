<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            // Se o usuário não está logado ou não é um administrador, redirecione para a página inicial
            return redirect('/');
        }

        return $next($request);
    }
}
