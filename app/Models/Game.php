<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    public function path()
    {
        return route('games.show', $this->id);
    }

    public function players()
    {
        return $this
            ->belongsToMany(Player::class)
            ->withPivot('player_slot', 'hero_id', 'hero_level', 'denies', 'kills', 'deaths', 'assists', 'net_worth', 'gold_per_minute', 'xp_per_minute', 'last_hits')
            ->using(GamePlayer::class)
            ->withTimestamps();
    }

    public function getHumanReadableDurationAttribute()
    {
        return gmdate('i:s', $this->duration);
    }
}
