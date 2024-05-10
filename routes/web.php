<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/enviar-contato', [WebController::class, 'enviarContato'])->name('enviar-contato');

Route::group(['namespace' => 'Web', 'as' => 'web.'], function () {
    Route::get('/', [WebController::class, 'home'])->name('home');
    Route::get('/campanhas', [WebController::class, 'campaigns'])->name('campaigns');
    Route::get('/campanhas/{slug}', [WebController::class, 'campaign'])->name('campaign');
    Route::get('/contato', [WebController::class, 'contato'])->name('contato');
    Route::post('/getPixQrCode', [DonationController::class, 'getPixQrCode'])->name('getPixQrCode');
    Route::post('/payWithCard', [DonationController::class, 'payWithCard'])->name('payWithCard');
    Route::get('/minhas-doacoes', [WebController::class, 'minhasDoacoes'])->name('my-donations');
});

//only access if user is_admin
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/cancelReserve', [AuthController::class, 'cancelReserve'])->name('cancelReserve');
    Route::group(['middleware' => ['auth']], function () {
        Route::get('home', [AuthController::class, 'home'])->name('home');
        Route::resource('site', SiteController::class);
        Route::resource('users', UserController::class);
        Route::resource('campaigns', CampaignController::class);
        Route::resource('donations', DonationController::class);
        Route::delete('remove-image', [CampaignController::class, 'removeImage'])->name('campaigns.removeImage');
        Route::post('image-set-cover', [CampaignController::class, 'imageSetCover'])->name('campaigns.imageSetCover');
    });
})->middleware('admin');

Route::group(['prefix' => 'usuario', 'as' => 'usuario.'], function () {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('inicio', [AuthController::class, 'home'])->name('home');
        Route::resource('campanhas', CampaignController::class);
        Route::get('apoios', [DonationController::class, 'myDonations'])->name('my-donations');
        Route::delete('remove-image', [CampaignController::class, 'removeImage'])->name('campaigns.removeImage');
        Route::post('image-set-cover', [CampaignController::class, 'imageSetCover'])->name('campaigns.imageSetCover');
    });
});

Route::group(['prefix' => 'sessao', 'as' => 'sessao.'], function () {
    Route::get('/entrar', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
    Route::get('/registrar', [AuthController::class, 'showRegisterForm'])->middleware('guest')->name('register');
    Route::post('/entrar', [AuthController::class, 'login'])->name('enviar-login');
    Route::post('/registrar', [AuthController::class, 'register'])->name('enviar-registro');
    Route::get('/sair', [AuthController::class, 'logout'])->name('logout');
});
