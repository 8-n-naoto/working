<?php

namespace App\Services;

use App\Models\Group;
use Closure;
use RuntimeException;

class InviteCodeGenerator
{
    public const CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public const LENGTH = 6;

    public const MAX_ATTEMPTS = 10;

    /** @var Closure(int, int): int */
    private Closure $random;

    /**
     * @param  (callable(int, int): int)|null  $random  乱数源（min, max を受け取り整数を返す）。テストで差し替える
     */
    public function __construct(?callable $random = null)
    {
        $this->random = Closure::fromCallable($random ?? 'random_int');
    }

    public function generate(): string
    {
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = $this->candidate();

            if (! Group::where('invite_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('招待コードを生成できませんでした。');
    }

    private function candidate(): string
    {
        $code = '';
        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::CHARS[($this->random)(0, strlen(self::CHARS) - 1)];
        }

        return $code;
    }
}
