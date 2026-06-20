<?php

namespace App\Game\Repositories;

use App\Models\GameProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final readonly class PlayerRankingRepository
{
    /**
     * @return Collection<int, GameProfile>
     */
    public function topByLevel(int $limit = 20): Collection
    {
        return $this->levelOrderedQuery()
            ->with('user')
            ->limit($limit)
            ->get();
    }

    public function levelPosition(GameProfile $profile): int
    {
        return $this->levelOrderedQuery()
            ->where(function (Builder $query) use ($profile): void {
                $query
                    ->where('level', '>', $profile->level)
                    ->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('level', $profile->level)
                            ->where('exp', '>', $profile->exp);
                    })
                    ->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('level', $profile->level)
                            ->where('exp', $profile->exp)
                            ->where('id', '<', $profile->id);
                    });
            })
            ->count() + 1;
    }

    /**
     * @return Builder<GameProfile>
     */
    private function levelOrderedQuery(): Builder
    {
        return GameProfile::query()
            ->orderByDesc('level')
            ->orderByDesc('exp')
            ->orderBy('id');
    }
}
