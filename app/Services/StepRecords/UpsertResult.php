<?php

namespace App\Services\StepRecords;

readonly class UpsertResult
{
    public function __construct(
        public int $created,
        public int $updated,
    ) {}

    public function saved(): int
    {
        return $this->created + $this->updated;
    }
}
