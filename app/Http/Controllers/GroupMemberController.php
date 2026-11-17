<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Services\StepHistoryService;
use Illuminate\View\View;

class GroupMemberController extends Controller
{
    public function __invoke(Group $group, User $user, StepHistoryService $history): View
    {
        return view('groups.members.show', [
            'group' => $group,
            'member' => $user,
            'days' => $history->lastSevenDays($user),
        ]);
    }
}
