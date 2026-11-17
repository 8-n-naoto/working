<?php

namespace App\Rules;

/**
 * 歩数レコード（date / steps）の共通検証ルール。API と手動入力で共有する。
 */
class StepRecordRules
{
    /**
     * @return list<mixed>
     */
    public static function date(): array
    {
        return ['bail', 'required', 'string', 'date_format:Y-m-d', new StepDate];
    }

    /**
     * @return list<mixed>
     */
    public static function steps(): array
    {
        return ['bail', 'required', new StepCount];
    }
}
