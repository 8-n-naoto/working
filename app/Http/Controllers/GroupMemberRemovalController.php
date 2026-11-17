<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Http\RedirectResponse;

class GroupMemberRemovalController extends Controller
{
    /**
     * D-029：オーナーがメンバーを外し、招待コードを再発行する（認可は can:removeMember）。
     */
    public function __invoke(Group $group, User $user, GroupService $service): RedirectResponse
    {
        $service->removeMember($group, $user);

        return redirect()->route('groups.ranking', $group)
            ->with('status', "{$user->name} さんをグループから外しました。招待コードを再発行しました。");
    }
}
