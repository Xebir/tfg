<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Character extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'game_id',
        'name',
        'pasive_id',
        'hp',
        'max_hp',
        'physical_attack',
        'special_attack',
        'physical_defense',
        'special_defense',
        'speed',
        'exp',
        'level',
        'imagen',
        'recruited',
        'alive',
    ];

    protected $casts = [
        'recruited' => 'boolean',
        'alive'     => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function pasive(): BelongsTo
    {
        return $this->belongsTo(Pasive::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'character_skill')
            ->withPivot('cooldown');
    }

    public function getUsableSkills(): BelongsToMany
    {
        return $this->skills()->wherePivot('cooldown', 0);
    }
}
