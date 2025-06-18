<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $fillable = ["name"];

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function scopeByState($query, int $state)
    {
        return $query->where('state_id', $state);
    }

    public function withStateAndCountry(): string
    {
        $country = $this->state?->country?->name;
        $state = $this->state?->name;
        $city = $this->name;
        return " {$city}, {$state}, {$country}";
    }
}
