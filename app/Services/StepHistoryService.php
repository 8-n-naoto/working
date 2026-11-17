<?php

namespace App\Services;

use App\Models\StepRecord;
use App\Models\User;
use App\Services\StepRecords\DailySteps;

class StepHistoryService
{
    /**
     * 今日を含む直近 7 日の歩数（日付昇順）。記録のない日は steps=0・source=null。
     *
     * @return list<DailySteps>
     */
    public function lastSevenDays(User $user): array
    {
        $days = StepCalendar::lastSevenDays();

        $records = StepRecord::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$days[0], end($days)])
            ->get()
            ->keyBy('date');

        return array_map(function (string $date) use ($records) {
            $record = $records->get($date);

            return new DailySteps($date, $record ? (int) $record->steps : 0, $record?->source);
        }, $days);
    }
}
