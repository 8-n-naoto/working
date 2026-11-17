<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * 歩数：整数、小数部 0 の有限な数値、数字だけの文字列を受け付け、0〜上限で判定する。
 */
class StepCount implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $number = self::toInt($value);

        if ($number === null) {
            $fail('歩数は整数で入力してください。');

            return;
        }

        $max = (int) config('steps.max_steps');

        if ($number < 0 || $number > $max) {
            $fail("歩数は 0 以上 {$max} 以下で入力してください。");
        }
    }

    /**
     * 受理形式なら整数に変換し、そうでなければ null。
     */
    public static function toInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            if (! is_finite($value) || floor($value) != $value || abs($value) > PHP_INT_MAX / 2) {
                return null;
            }

            return (int) $value;
        }

        if (is_string($value) && preg_match('/\A\d{1,18}\z/', $value) === 1) {
            return (int) $value;
        }

        return null;
    }
}
