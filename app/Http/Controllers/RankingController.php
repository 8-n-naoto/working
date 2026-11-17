<?php

namespace App\Http\Controllers;

use App\Enums\RankingPeriod;
use App\Models\Group;
use App\Services\RankingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function __invoke(Request $request, Group $group, RankingService $ranking): View
    {
        $input = $request->query('period');
        $period = (is_string($input) ? RankingPeriod::tryFrom($input) : null) ?? RankingPeriod::Today;

        return view('groups.ranking', [
            'group' => $group,
            'period' => $period,
            'board' => $ranking->board($group, $period),
            'groups' => $request->user()->groups()->get(),
        ]);
    }
}
