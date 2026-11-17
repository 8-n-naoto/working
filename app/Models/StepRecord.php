<?php

namespace App\Models;

use App\Enums\StepSource;
use Database\Factories\StepRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepRecord extends Model
{
    /** @use HasFactory<StepRecordFactory> */
    use HasFactory;

    /**
     * 書き込みは Service の upsert とファクトリのみ。マスアサインメントは許可しない。
     *
     * @var list<string>
     */
    protected $fillable = [];

    /**
     * date は Y-m-d 文字列のまま扱う（キャストすると SQLite で日時文字列になり比較が壊れる）。
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'source' => StepSource::class,
            'received_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
