<?php

namespace Tests\Feature;

use App\Models\GameProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class GameFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_user_profile_and_starts_session(): void
    {
        $response = $this->post('/register', [
            'nick' => 'SeniorNick',
            'email' => 'senior@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('game.show'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'senior@example.com',
            'name' => 'SeniorNick',
        ]);
        $this->assertDatabaseHas('game_profiles', [
            'level' => 1,
            'gold' => 100,
            'pa' => 20,
        ]);
    }

    public function test_game_screen_requires_authentication(): void
    {
        $this->get('/game')->assertRedirect('/login');
    }

    public function test_guest_auth_entrypoints_return_home_screen(): void
    {
        $this->get('/login')->assertRedirect(route('home'));
        $this->get('/register')->assertRedirect(route('home'));
    }

    public function test_authenticated_game_screen_returns_snapshot(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/game')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Game/Show')
                ->has('game.data.user')
                ->where('game.data.user.level', 1)
                ->where('game.data.currentMap.name', 'Ithan')
            );
    }

    public function test_stage_battle_consumes_pa_and_returns_updated_snapshot(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/game')->assertOk();

        $response = $this->postJson('/game/actions/battle/stage', [
            'locationId' => 'ithan-hunters-cave',
            'stage' => 1,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('game.user.pa', 19)
            ->assertJsonStructure([
                'battle' => ['name', 'enemy', 'won', 'log', 'rewards'],
                'game' => ['user', 'currentMap', 'worldMaps', 'shops'],
            ]);

        $this->assertSame(19, GameProfile::query()->whereBelongsTo($user)->value('pa'));
    }
}
