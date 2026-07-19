<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Tests\TestCase;

class CampaignAuthorizationTest extends TestCase
{
    public function test_user_can_edit_own_campaign(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('usuario.campanhas.edit', $campaign->id));

        $response->assertOk();
    }

    public function test_user_cannot_edit_another_users_campaign(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($intruder)
            ->get(route('usuario.campanhas.edit', $campaign->id));

        $response->assertForbidden();
    }

    public function test_admin_can_edit_any_campaign(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)
            ->get(route('admin.campaigns.edit', $campaign->id));

        $response->assertOk();
    }
}
