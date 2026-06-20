<?php

namespace App\Game\Data;

use Illuminate\Support\Collection;

final readonly class PlayerRanking
{
    /**
     * @param  Collection<int, PlayerRankingEntry>  $entries
     */
    public function __construct(
        public Collection $entries,
        public int $currentPosition,
    ) {}
}
