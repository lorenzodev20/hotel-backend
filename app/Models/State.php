<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use SoftDeletes;

    protected $fillable = ["name"];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeByCountry($query, int $country)
    {
        return $query->where('country_id', $country);
    }
}
