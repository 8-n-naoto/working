<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    public function view(User $user, Group $group): bool
    {
        return $group->members()->whereKey($user->id)->exists();
    }

    public function viewMember(User $user, Group $group, User $member): Response
    {
        if (! $this->view($user, $group)) {
            return Response::deny();
        }

        if (! $group->members()->whereKey($member->id)->exists()) {
            return Response::denyAsNotFound();
        }

        return Response::allow();
    }

    public function regenerateInviteCode(User $user, Group $group): bool
    {
        return $user->getKey() === $group->owner_id;
    }

    /**
     * D-029：オーナーのみ。対象がメンバーでなければ 404、オーナー自身は外せない。
     */
    public function removeMember(User $user, Group $group, User $member): Response
    {
        if (! $this->regenerateInviteCode($user, $group)) {
            return Response::deny();
        }

        if (! $group->members()->whereKey($member->id)->exists()) {
            return Response::denyAsNotFound();
        }

        if ($member->getKey() === $group->owner_id) {
            return Response::deny('オーナーはグループから外せません。');
        }

        return Response::allow();
    }

    /**
     * D-029：メンバーのみ。オーナーは退会できない（グループ削除・オーナー移譲は MVP 外）。
     */
    public function leave(User $user, Group $group): Response
    {
        if (! $this->view($user, $group)) {
            return Response::deny();
        }

        if ($user->getKey() === $group->owner_id) {
            return Response::deny('オーナーはグループから退会できません。');
        }

        return Response::allow();
    }
}
