<?php

namespace App\Http\Requests;

use App\Rules\StepCount;
use App\Rules\StepRecordRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreManualStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'date' => StepRecordRules::date(),
            'steps' => StepRecordRules::steps(),
        ];
    }

    /**
     * @return array{date: string, steps: int}
     */
    public function stepRecord(): array
    {
        $validated = $this->validated();

        return [
            'date' => (string) $validated['date'],
            'steps' => (int) StepCount::toInt($validated['steps']),
        ];
    }
}
