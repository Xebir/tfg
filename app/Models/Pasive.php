<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasive extends Model
{
    public $timestamps = false;

    protected $fillable = ['description'];

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }
}
