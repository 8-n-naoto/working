<?php

namespace App\Models;

use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    /** @use HasFactory<GroupFactory> */
    use HasFactory;

    /**
     * owner_id・invite_code はマスアサインメントさせない（Service が明示的に代入する）。
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * DB ドライバが owner_id を文字列で返しても整数として比較できるようにする。
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'owner_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('joined_at');
    }
}
