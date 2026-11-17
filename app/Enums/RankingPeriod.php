<?php

namespace App\Enums;

enum RankingPeriod: string
{
    case Today = 'today';
    case WeekSum = 'week_sum';
    case WeekAvg = 'week_avg';

    public function label(): string
    {
        return match ($this) {
            self::Today => '今日',
            self::WeekSum => '週合計',
            self::WeekAvg => '週平均',
        };
    }
}
