<?php

namespace App\Services;

use App\Enums\StepSource;
use App\Models\StepRecord;
use App\Models\User;
use App\Services\StepRecords\UpsertResult;
use Illuminate\Support\Facades\DB;

class StepRecordService
{
    /**
     * ショートカット（API）からの歩数を保存する。全件を 1 トランザクションで保存し、最終受信日時を更新する。
     *
     * @param  list<array{date: string, steps: int}>  $records
     */
    public function storeFromShortcut(User $user, array $records): UpsertResult
    {
        return DB::transaction(function () use ($user, $records) {
            $now = now();
            $result = $this->upsert($user, $records, StepSource::Shortcut);

            $user->forceFill(['step_token_last_used_at' => $now])->save();

            return $result;
        });
    }

    /**
     * 画面からの手動入力を保存する（同日は上書き。source=manual、received_at=保存時刻）。
     *
     * @param  array{date: string, steps: int}  $record
     */
    public function storeManual(User $user, array $record): UpsertResult
    {
        return DB::transaction(fn () => $this->upsert($user, [$record], StepSource::Manual));
    }

    /**
     * @param  list<array{date: string, steps: int}>  $records
     */
    private function upsert(User $user, array $records, StepSource $source): UpsertResult
    {
        $dates = array_column($records, 'date');
        $existing = StepRecord::query()
            ->where('user_id', $user->id)
            ->whereIn('date', $dates)
            ->count();
        $now = now();

        StepRecord::upsert(
            array_map(fn (array $r) => [
                'user_id' => $user->id,
                'date' => $r['date'],
                'steps' => $r['steps'],
                'source' => $source->value,
                'received_at' => $now,
            ], $records),
            ['user_id', 'date'],
            ['steps', 'source', 'received_at'],
        );

        return new UpsertResult(created: count($records) - $existing, updated: $existing);
    }
}
