<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GroupLeaveController extends Controller
{
    /**
     * D-029：メンバー自身がグループから退会する（認可は can:leave。オーナーは不可）。
     */
    public function __invoke(Request $request, Group $group, GroupService $service): RedirectResponse
    {
        $service->leave($group, $request->user());

        return redirect()->route('groups.index')->with('status', "「{$group->name}」から退会しました。");
    }
}
