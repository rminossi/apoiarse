<?php

namespace App\Support;

use CoffeeCode\Optimizer\Optimizer;

class Seo
{
    private $optimizer;

    public function __construct()
    {
        $this->optimizer = new Optimizer;
        $this->optimizer->openGraph(
            config('app.name', 'Apoiar-se'),
            'pt_br',
            'website'
        );
    }

    public function render(string $title, string $description, string $url, string $image, bool $follow = true)
    {
        $description = mb_substr(strip_tags($description), 0, 160);

        return $this->optimizer->optimize($title, $description, $url, $image, $follow)->render();
    }
}
