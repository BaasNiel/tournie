<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScoreboardMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'anchor_text',
        'scoreboard_path',
        'height',
        'weight',
        'size_kb',
    ];

    public function slots(): HasMany
    {
        return $this->hasMany(ScoreboardMappingSlot::class, 'scoreboard_mapping_slots');
    }
}
