<?php

namespace App\Http\Controllers;

use App\Services\StepCalendar;
use App\Services\StepHistoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function show(Request $request, StepHistoryService $history): View
    {
        $user = $request->user();

        return view('link.show', [
            'plainToken' => session('step_token_plain'),
            'hasToken' => $user->step_token_hash !== null,
            'lastUsedAt' => $user->step_token_last_used_at,
            'days' => $history->lastSevenDays($user),
            'today' => StepCalendar::today()->toDateString(),
        ]);
    }
}
