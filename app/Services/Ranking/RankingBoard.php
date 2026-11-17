<?php

namespace App\Services\Ranking;

use App\Enums\RankingPeriod;

readonly class RankingBoard
{
    /**
     * @param  list<RankingRow>  $rows  順位順（値降順 → 名前昇順 → id 昇順）
     */
    public function __construct(
        public RankingPeriod $period,
        public array $rows,
    ) {}

    public function rowFor(int $userId): ?RankingRow
    {
        foreach ($this->rows as $row) {
            if ($row->userId === $userId) {
                return $row;
            }
        }

        return null;
    }

    /**
     * 自分より大きい値のうち最小のものとの差。1 位（上に誰もいない）・未所属は null。
     */
    public function gapToNextAbove(int $userId): ?int
    {
        $me = $this->rowFor($userId);
        if ($me === null) {
            return null;
        }

        $smallestAbove = null;
        foreach ($this->rows as $row) {
            if ($row->value > $me->value && ($smallestAbove === null || $row->value < $smallestAbove)) {
                $smallestAbove = $row->value;
            }
        }

        return $smallestAbove === null ? null : $smallestAbove - $me->value;
    }
}
