<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'title' => $title,
            'description' => fake()->paragraph(),
            'type' => 'Social',
            'goal' => 10000,
            'status' => 1,
            'user_id' => User::factory(),
            'slug' => str($title)->slug().'-1',
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => 2]);
    }

    public function finished(): static
    {
        return $this->state(['status' => 3]);
    }
}
