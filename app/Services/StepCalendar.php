<?php

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * 日付境界（JST 暦日）を 1 か所に集約する。
 */
class StepCalendar
{
    /**
     * 今日（アプリのタイムゾーンの 00:00）。
     */
    public static function today(): CarbonImmutable
    {
        return CarbonImmutable::now(config('app.timezone'))->startOfDay();
    }

    /**
     * 受け付ける最も古い日付（今日 - max_past_days 日）。
     */
    public static function earliestAcceptedDate(): CarbonImmutable
    {
        return self::today()->subDays((int) config('steps.max_past_days'));
    }

    /**
     * 今日を含む直近 history_days 日の Y-m-d（昇順）。
     *
     * @return list<string>
     */
    public static function lastSevenDays(): array
    {
        $days = (int) config('steps.history_days');
        $today = self::today();
        $dates = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = $today->subDays($i)->toDateString();
        }

        return $dates;
    }
}
