<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * 文字列のバイト数（UTF-8）が上限以下であること。
 *
 * D-030：bcrypt は先頭 72 バイトしか照合に使わないため、パスワードを 72 バイトで止める。
 * 文字数（max:）では日本語（1 文字 3 バイト）を正しく制限できない。
 */
class MaxBytes implements ValidationRule
{
    public function __construct(private readonly int $max) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return; // 型の検証は他のルール（Password 等）に任せる
        }

        if (strlen($value) > $this->max) {
            $fail("パスワードは{$this->max}バイト以内で入力してください（全角は1文字3バイト）。");
        }
    }
}
