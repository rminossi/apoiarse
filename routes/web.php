<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignUpdateController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
})->name('health');

Route::post('/enviar-contato', [WebController::class, 'enviarContato'])
    ->middleware('throttle:5,1')
    ->name('enviar-contato');

Route::group(['namespace' => 'Web', 'as' => 'web.'], function () {
    Route::get('/', [WebController::class, 'home'])->name('home');
    Route::get('/campanhas', [WebController::class, 'campaigns'])->name('campaigns');
    Route::get('/campanhas/{slug}', [WebController::class, 'campaign'])->name('campaign');
    Route::get('/contato', [WebController::class, 'contato'])->name('contato');
    Route::get('/sobre', [WebController::class, 'sobre'])->name('sobre');
    Route::get('/criadores/{slug}', [CreatorController::class, 'show'])->name('creator');

    Route::middleware(['auth', 'throttle:10,1'])->group(function () {
        Route::post('/getPixQrCode', [DonationController::class, 'getPixQrCode'])->name('getPixQrCode');
        Route::post('/payWithCard', [DonationController::class, 'payWithCard'])->name('payWithCard');
    });

    Route::get('/minhas-doacoes', [WebController::class, 'minhasDoacoes'])
        ->middleware(['auth', 'throttle:10,1'])
        ->name('my-donations');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('home', [AuthController::class, 'home'])->name('home');
        Route::resource('site', SiteController::class)->only(['edit', 'update']);
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('campaigns', CampaignController::class);
        Route::resource('donations', DonationController::class)->only(['update']);
        Route::post('campaigns/{campaign}/updates', [CampaignUpdateController::class, 'store'])->name('campaigns.updates.store');
        Route::delete('campaign-updates/{id}', [CampaignUpdateController::class, 'destroy'])->name('campaigns.updates.destroy');
        Route::delete('remove-image', [CampaignController::class, 'removeImage'])->name('campaigns.removeImage');
        Route::post('image-set-cover', [CampaignController::class, 'imageSetCover'])->name('campaigns.imageSetCover');
    });
})->middleware('admin');

Route::group(['prefix' => 'usuario', 'as' => 'usuario.'], function () {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('inicio', [AuthController::class, 'home'])->name('home');
        Route::resource('campanhas', CampaignController::class)->names('campanhas');
        Route::post('campanhas/{campaign}/updates', [CampaignUpdateController::class, 'store'])->name('campanhas.updates.store');
        Route::delete('campanhas-updates/{id}', [CampaignUpdateController::class, 'destroy'])->name('campanhas.updates.destroy');
        Route::get('apoios', [DonationController::class, 'myDonations'])->name('my-donations');
        Route::delete('remove-image', [CampaignController::class, 'removeImage'])->name('campaigns.removeImage');
        Route::post('image-set-cover', [CampaignController::class, 'imageSetCover'])->name('campaigns.imageSetCover');
    });
});

Route::group(['prefix' => 'sessao', 'as' => 'sessao.'], function () {
    Route::get('/entrar', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
    Route::get('/registrar', [AuthController::class, 'showRegisterForm'])->middleware('guest')->name('register');
    Route::post('/entrar', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('enviar-login');
    Route::post('/registrar', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('enviar-registro');
    Route::post('/recuperar-senha', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1')->name('forgotPassword');
    Route::get('/recuperar-senha', [AuthController::class, 'forgotPasswordForm'])->name('forgotPasswordForm');
    Route::post('/recuperar-senha/{token}', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('resetPassword');
    Route::get('/recuperar-senha/{token}', [AuthController::class, 'resetPasswordForm'])->name('resetPasswordForm');
    Route::get('/sair', [AuthController::class, 'logout'])->name('logout');
});
