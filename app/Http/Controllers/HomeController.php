<?php

namespace App\Http\Controllers;

use App\Enums\RankingPeriod;
use App\Services\RankingService;
use App\Services\StepHistoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request, RankingService $ranking, StepHistoryService $history): View
    {
        $user = $request->user();
        $groups = $user->groups()->get();
        $days = $history->lastSevenDays($user);
        $todaySteps = end($days)->steps;
        $goal = (int) config('steps.daily_goal');

        // ?group= は本人の所属の中でのみ解決し、見つからなければ最初に参加したグループ
        // 配列などスカラー以外の指定は既定グループ（RankingController の period と同じ方針）
        $input = $request->query('group');
        $requestedId = is_string($input) && ctype_digit($input) ? (int) $input : null;
        $group = ($requestedId !== null ? $groups->firstWhere('id', $requestedId) : null) ?? $groups->first();

        $row = null;
        $gap = null;
        if ($group) {
            $board = $ranking->board($group, RankingPeriod::Today);
            $row = $board->rowFor($user->id);
            $gap = $board->gapToNextAbove($user->id);
        }

        return view('home', [
            'groups' => $groups,
            'group' => $group,
            'todaySteps' => $todaySteps,
            'goal' => $goal,
            'remaining' => max(0, $goal - $todaySteps),
            'rank' => $row?->rank,
            'gap' => $gap,
            'days' => $days,
        ]);
    }
}
