<?php

namespace App\Http\Requests\Api;

use App\Rules\StepCount;
use App\Rules\StepRecordRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreStepsRequest extends FormRequest
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
        if ($this->has('records')) {
            return [
                'records' => ['required', 'array', 'list', 'min:1', 'max:'.config('steps.max_records_per_request')],
                'records.*' => ['array'],
                'records.*.date' => [...StepRecordRules::date(), 'distinct'],
                'records.*.steps' => StepRecordRules::steps(),
                'date' => ['prohibited'],
                'steps' => ['prohibited'],
            ];
        }

        return [
            'date' => StepRecordRules::date(),
            'steps' => StepRecordRules::steps(),
        ];
    }

    /**
     * 検証済みの入力を [{date, steps(int)}] に正規化する。
     *
     * @return list<array{date: string, steps: int}>
     */
    public function stepRecords(): array
    {
        $validated = $this->validated();

        $items = array_key_exists('records', $validated)
            ? $validated['records']
            : [['date' => $validated['date'], 'steps' => $validated['steps']]];

        return array_values(array_map(fn (array $r) => [
            'date' => (string) $r['date'],
            'steps' => (int) StepCount::toInt($r['steps']),
        ], $items));
    }
}
