<?php

namespace App\Services\StepRecords;

use App\Enums\StepSource;

readonly class DailySteps
{
    public function __construct(
        public string $date,
        public int $steps,
        public ?StepSource $source,
    ) {}
}
