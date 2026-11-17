<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinGroupRequest;
use App\Services\GroupService;
use Illuminate\Http\RedirectResponse;

class GroupJoinController extends Controller
{
    public function __invoke(JoinGroupRequest $request, GroupService $service): RedirectResponse
    {
        $service->join($request->user(), $request->validated('invite_code'));

        return redirect()->route('groups.index')->with('status', 'グループに参加しました。');
    }
}
