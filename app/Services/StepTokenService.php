<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use RuntimeException;

class StepTokenService
{
    public const PATTERN = '/\Astp_[A-Za-z0-9]{48}\z/';

    /**
     * トークンを発行（再発行）し、平文を返す。DB にはハッシュのみ保存する。
     */
    public function issue(User $user): string
    {
        $plain = 'stp_'.Str::random(48);

        try {
            $user->forceFill(['step_token_hash' => $this->hash($plain)])->save();
        } catch (QueryException) {
            // SQL・バインド値（ハッシュ）をログに残さないため previous を付けずに投げ直す
            throw new RuntimeException('連携トークンの保存に失敗しました。');
        }

        return $plain;
    }

    public function isWellFormed(#[\SensitiveParameter] ?string $plain): bool
    {
        return $plain !== null && preg_match(self::PATTERN, $plain) === 1;
    }

    public function hash(#[\SensitiveParameter] string $plain): string
    {
        return hash('sha256', $plain);
    }

    /**
     * 平文トークンの持ち主を返す。形式違い・不一致は null。
     */
    public function findUser(#[\SensitiveParameter] string $plain): ?User
    {
        if (! $this->isWellFormed($plain)) {
            return null;
        }

        try {
            return User::query()->where('step_token_hash', $this->hash($plain))->first();
        } catch (QueryException) {
            // SQL・バインド値（ハッシュ）をログに残さないため previous を付けずに投げ直す
            throw new RuntimeException('連携トークンの照合に失敗しました。');
        }
    }
}
