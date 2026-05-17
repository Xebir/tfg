<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = ['user_id', 'floor', 'active_character_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    public function activeCharacter(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'active_character_id');
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }
}
