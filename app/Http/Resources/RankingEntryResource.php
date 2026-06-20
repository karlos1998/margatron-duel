<?php

namespace App\Http\Resources;

use App\Game\Data\PlayerRankingEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PlayerRankingEntry
 */
final class RankingEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'position' => $this->position,
            'profileId' => $this->profileId,
            'userId' => $this->userId,
            'nick' => $this->nick,
            'level' => $this->level,
            'currentUser' => $this->currentUser,
        ];
    }
}
