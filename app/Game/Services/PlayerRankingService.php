<?php

namespace App\Game\Services;

use App\Game\Data\PlayerRanking;
use App\Game\Data\PlayerRankingEntry;
use App\Game\Repositories\PlayerRankingRepository;
use App\Models\GameProfile;

final readonly class PlayerRankingService
{
    public function __construct(
        private PlayerRankingRepository $rankings,
    ) {}

    public function levelRanking(GameProfile $currentProfile, int $limit = 20): PlayerRanking
    {
        $currentProfile->loadMissing('user');

        $entries = $this->rankings
            ->topByLevel($limit)
            ->values()
            ->map(fn (GameProfile $profile, int $index): PlayerRankingEntry => new PlayerRankingEntry(
                position: $index + 1,
                profileId: $profile->id,
                userId: $profile->user_id,
                nick: $profile->user?->name ?? 'Nieznany',
                level: $profile->level,
                currentUser: $profile->is($currentProfile),
            ));

        return new PlayerRanking(
            entries: $entries,
            currentPosition: $this->rankings->levelPosition($currentProfile),
        );
    }
}
