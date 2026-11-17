<?php

namespace App\Services;

use App\Enums\RankingPeriod;
use App\Models\Group;
use App\Models\User;
use App\Services\Ranking\RankingBoard;
use App\Services\Ranking\RankingRow;
use Illuminate\Database\Query\JoinClause;

class RankingService
{
    /**
     * グループのランキングを 1 クエリで集計し、順位付けは PHP で行う（メンバー数に依存しない）。
     */
    public function board(Group $group, RankingPeriod $period): RankingBoard
    {
        $days = StepCalendar::lastSevenDays();
        $today = end($days);

        $query = User::query();
        $grammar = $query->getGrammar();
        $date = $grammar->wrap('step_records.date');
        $steps = $grammar->wrap('step_records.steps');

        $records = $query
            ->join('group_user', 'group_user.user_id', '=', 'users.id')
            ->where('group_user.group_id', $group->id)
            ->leftJoin('step_records', function (JoinClause $join) use ($days) {
                $join->on('step_records.user_id', '=', 'users.id')
                    ->whereBetween('step_records.date', [$days[0], end($days)]);
            })
            ->groupBy('users.id', 'users.name')
            ->select('users.id', 'users.name')
            ->selectRaw("COALESCE(SUM(CASE WHEN {$date} = ? THEN {$steps} ELSE 0 END), 0) AS today_steps", [$today])
            ->selectRaw("COALESCE(SUM({$steps}), 0) AS week_sum")
            ->toBase()
            ->get();

        $items = $records->map(function (object $r) use ($period) {
            $todaySteps = (int) $r->today_steps;
            $weekSum = (int) $r->week_sum;

            return [
                'id' => (int) $r->id,
                'name' => (string) $r->name,
                'today' => $todaySteps,
                'week_sum' => $weekSum,
                'value' => match ($period) {
                    RankingPeriod::Today => $todaySteps,
                    RankingPeriod::WeekSum => $weekSum,
                    RankingPeriod::WeekAvg => (int) round($weekSum / count(StepCalendar::lastSevenDays())),
                },
            ];
        })->all();

        usort($items, fn (array $a, array $b) => $b['value'] <=> $a['value']
            ?: strcmp($a['name'], $b['name'])
            ?: $a['id'] <=> $b['id']);

        return new RankingBoard($period, $this->rank($items));
    }

    /**
     * @param  list<array<string, mixed>>  $items  並べ替え済み
     * @return list<RankingRow>
     */
    private function rank(array $items): array
    {
        $rows = [];
        $rank = 0;
        $previous = null;

        foreach ($items as $i => $item) {
            if ($previous === null || $item['value'] !== $previous) {
                $rank = $i + 1;
            }
            $previous = $item['value'];
            $rows[] = new RankingRow($item['id'], $item['name'], $item['value'], $rank, $item['today'], $item['week_sum']);
        }

        return $rows;
    }
}
