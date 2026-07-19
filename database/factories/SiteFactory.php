<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'phone' => '51999999999',
            'email' => 'contato@apoiarse.com',
            'whatsapp_group' => null,
            'bank' => 'Bradesco',
            'type' => 'Conta Corrente',
            'acc' => '12345-6',
            'ag' => '1234',
            'cpf' => '52998224725',
            'fullName' => fake()->name(),
        ];
    }
}
