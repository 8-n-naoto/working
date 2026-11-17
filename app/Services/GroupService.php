<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GroupService
{
    public function __construct(private readonly InviteCodeGenerator $generator) {}

    public function create(User $owner, string $name): Group
    {
        return DB::transaction(function () use ($owner, $name) {
            $group = new Group(['name' => $name]);
            $group->owner_id = $owner->id;
            $group->invite_code = $this->generator->generate();
            $group->save();

            $group->members()->attach($owner->id, ['joined_at' => now()]);

            return $group;
        });
    }

    /**
     * @throws ValidationException コード不一致・既に所属
     */
    public function join(User $user, string $inviteCode): Group
    {
        $group = Group::where('invite_code', $inviteCode)->first();

        if ($group === null) {
            throw ValidationException::withMessages(['invite_code' => '招待コードが見つかりません。']);
        }

        $already = ValidationException::withMessages(['invite_code' => 'すでに参加しています。']);

        if ($group->members()->whereKey($user->id)->exists()) {
            throw $already;
        }

        try {
            $group->members()->attach($user->id, ['joined_at' => now()]);
        } catch (UniqueConstraintViolationException) {
            throw $already;
        }

        return $group;
    }

    public function regenerateInviteCode(Group $group): Group
    {
        $group->invite_code = $this->generator->generate();
        $group->save();

        return $group;
    }

    /**
     * D-029：メンバーを外し、外した人が古い招待コードで戻れないよう同時に再発行する。
     * 歩数記録とユーザーは消さない（group_user の行のみ）。
     */
    public function removeMember(Group $group, User $member): Group
    {
        return DB::transaction(function () use ($group, $member) {
            $group->members()->detach($member->id);

            return $this->regenerateInviteCode($group);
        });
    }

    /**
     * D-029：メンバー自身の退会。招待コードは変えない（本人が抜けるだけのため）。
     */
    public function leave(Group $group, User $member): void
    {
        $group->members()->detach($member->id);
    }
}
