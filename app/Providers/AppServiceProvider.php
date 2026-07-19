<?php

namespace App\Providers;

use App\Validation\CpfValidation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Validator::extend('cpf', CpfValidation::class.'@validate');

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
