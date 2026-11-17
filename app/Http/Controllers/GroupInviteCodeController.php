<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Http\RedirectResponse;

class GroupInviteCodeController extends Controller
{
    public function __invoke(Group $group, GroupService $service): RedirectResponse
    {
        $service->regenerateInviteCode($group);

        return redirect()->route('groups.index')->with('status', '招待コードを再発行しました。');
    }
}
