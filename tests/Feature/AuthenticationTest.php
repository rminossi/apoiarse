<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get(route('sessao.login'));

        $response->assertOk();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson(route('sessao.enviar-login'), [
            'email' => 'user@test.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['redirect']);

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson(route('sessao.enviar-login'), [
            'email' => 'user@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message']);

        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson(route('sessao.enviar-login'), [
                'email' => 'fake@test.com',
                'password' => 'wrong',
            ]);
        }

        $response->assertStatus(429);
    }
}
