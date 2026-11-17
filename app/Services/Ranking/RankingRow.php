<?php

namespace App\Services\Ranking;

readonly class RankingRow
{
    public function __construct(
        public int $userId,
        public string $name,
        public int $value,
        public int $rank,
        public int $todaySteps,
        public int $weekSum,
    ) {}
}
