<?php

namespace App\Rules;

use App\Services\StepCalendar;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * 日付：今日 - max_past_days 日 〜 今日（アプリのタイムゾーンの暦日）。
 * 形式（Y-m-d）は date_format ルールで先に確認する前提。
 */
class StepDate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('日付の形式が正しくありません。');

            return;
        }

        $date = StepCalendar::today()->toDateString() >= $value
            && StepCalendar::earliestAcceptedDate()->toDateString() <= $value;

        if (! $date) {
            $days = (int) config('steps.max_past_days');
            $fail("日付は今日から {$days} 日前までの範囲で指定してください。");
        }
    }
}
