<?php

namespace App\Http\Controllers;
use App\Support\Message;
use App\Support\Seo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $seo;
    protected $message;

    public function __construct() {
        $this->seo = new Seo();
        $this->message = new Message();
    }
}
