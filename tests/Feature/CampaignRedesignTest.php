<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignRedesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_home_page_loads_with_redesign(): void
    {
        $response = $this->get(route('web.home'));
        $response->assertOk();
        $response->assertSee('Apoie causas que');
    }

    public function test_campaign_list_filters_by_category(): void
    {
        $category = Category::where('slug', 'saude')->first();
        $user = User::factory()->create();

        Campaign::create([
            'title' => 'Campanha Saúde Teste',
            'description' => 'Descrição teste',
            'short_description' => 'Resumo saúde',
            'type' => 'other',
            'category_id' => $category->id,
            'goal' => 1000,
            'status' => 1,
            'user_id' => $user->id,
            'slug' => 'campanha-saude-teste-1',
        ]);

        $response = $this->get(route('web.campaigns', ['category' => 'saude']));
        $response->assertOk();
        $response->assertSee('Campanha Saúde Teste');
    }

    public function test_campaign_search_by_title(): void
    {
        $category = Category::first();
        $user = User::factory()->create();

        Campaign::create([
            'title' => 'Busca Unica XYZ123',
            'description' => 'Descrição',
            'type' => 'other',
            'category_id' => $category->id,
            'status' => 1,
            'user_id' => $user->id,
            'slug' => 'busca-unica-xyz-1',
        ]);

        $response = $this->get(route('web.campaigns', ['q' => 'XYZ123']));
        $response->assertOk();
        $response->assertSee('Busca Unica XYZ123');
    }

    public function test_featured_campaigns_appear_on_home(): void
    {
        $category = Category::first();
        $user = User::factory()->create();

        Campaign::create([
            'title' => 'Campanha Destaque Especial',
            'description' => 'Descrição',
            'type' => 'other',
            'category_id' => $category->id,
            'status' => 1,
            'user_id' => $user->id,
            'slug' => 'campanha-destaque-1',
            'is_featured' => true,
            'featured_order' => 1,
        ]);

        $response = $this->get(route('web.home'));
        $response->assertOk();
        $response->assertSee('Campanha Destaque Especial');
        $response->assertSee('Em destaque');
    }

    public function test_campaign_progress_percent_accessor(): void
    {
        $category = Category::first();
        $user = User::factory()->create();

        $campaign = Campaign::create([
            'title' => 'Progresso Teste',
            'description' => 'Descrição',
            'type' => 'other',
            'category_id' => $category->id,
            'goal' => 1000,
            'status' => 1,
            'user_id' => $user->id,
            'slug' => 'progresso-teste-1',
        ]);

        Donation::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => 250,
            'status' => 3,
            'payment_method' => 'PIX',
        ]);

        $campaign->refresh();

        $this->assertEquals(25.0, $campaign->progress_percent);
        $this->assertEquals(1, $campaign->supporters_count);
    }

    public function test_creator_profile_page(): void
    {
        $user = User::factory()->create([
            'public_slug' => 'criador-teste',
            'bio' => 'Bio do criador',
        ]);

        $response = $this->get(route('web.creator', 'criador-teste'));
        $response->assertOk();
        $response->assertSee($user->name);
        $response->assertSee('Bio do criador');
    }
}
