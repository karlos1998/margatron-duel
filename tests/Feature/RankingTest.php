<?php

namespace Tests\Feature;

use App\Models\GameProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_rankings_screen_requires_authentication(): void
    {
        $this->get('/rankings')->assertRedirect('/login');
    }

    public function test_rankings_screen_returns_level_ordered_entries(): void
    {
        $currentUser = User::factory()->create(['name' => 'Current']);
        $topUser = User::factory()->create(['name' => 'TopLevel']);
        $tieUser = User::factory()->create(['name' => 'TieBreaker']);
        $lowUser = User::factory()->create(['name' => 'LowLevel']);

        GameProfile::factory()->for($currentUser)->create([
            'level' => 5,
            'exp' => 10,
        ]);
        GameProfile::factory()->for($topUser)->create([
            'level' => 12,
            'exp' => 0,
        ]);
        GameProfile::factory()->for($tieUser)->create([
            'level' => 5,
            'exp' => 20,
        ]);
        GameProfile::factory()->for($lowUser)->create([
            'level' => 2,
            'exp' => 0,
        ]);

        $this->actingAs($currentUser)
            ->get('/rankings')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Game/Rankings')
                ->has('game.data.user')
                ->has('ranking.entries', 4)
                ->where('ranking.activeSort', 'level')
                ->where('ranking.currentPosition', 3)
                ->where('ranking.entries.0.position', 1)
                ->where('ranking.entries.0.nick', 'TopLevel')
                ->where('ranking.entries.0.level', 12)
                ->where('ranking.entries.1.nick', 'TieBreaker')
                ->where('ranking.entries.1.level', 5)
                ->where('ranking.entries.2.nick', 'Current')
                ->where('ranking.entries.2.currentUser', true)
                ->where('ranking.entries.3.nick', 'LowLevel')
            );
    }
}
