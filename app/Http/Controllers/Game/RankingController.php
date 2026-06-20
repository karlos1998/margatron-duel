<?php

namespace App\Http\Controllers\Game;

use App\Game\Services\ActionPointRegenerationScheduler;
use App\Game\Services\GameProfileService;
use App\Game\Services\GameStateService;
use App\Game\Services\PlayerRankingService;
use App\Http\Controllers\Controller;
use App\Http\Resources\GameSnapshotResource;
use App\Http\Resources\RankingEntryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class RankingController extends Controller
{
    public function index(
        Request $request,
        GameProfileService $profiles,
        GameStateService $gameState,
        ActionPointRegenerationScheduler $actionPoints,
        PlayerRankingService $rankings,
    ): Response {
        $profile = $profiles->ensureFor($request->user());
        $actionPoints->schedule($profile);
        $ranking = $rankings->levelRanking($profile);

        return Inertia::render('Game/Rankings', [
            'game' => new GameSnapshotResource($gameState->snapshot($profile)),
            'ranking' => [
                'entries' => RankingEntryResource::collection($ranking->entries)->resolve($request),
                'currentPosition' => $ranking->currentPosition,
                'activeSort' => 'level',
            ],
        ]);
    }
}
