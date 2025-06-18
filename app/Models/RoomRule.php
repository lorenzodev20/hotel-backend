<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomRule extends Model
{
    /** @use HasFactory<\Database\Factories\RoomRuleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'enabled'
    ];

    protected $casts = [
        'enabled' => 'boolean'
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function accommodationType(): BelongsTo
    {
        return $this->belongsTo(AccommodationType::class);
    }
}
