<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreboardMappingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'scoreboard_mapping_id',
        'key',
        'offset_x',
        'offset_y',
        'top',
        'right',
        'bottom',
        'left',
        'width',
        'height',
        'raw_text',
    ];

    public function scoreboardMapping(): BelongsTo
    {
        return $this->belongsTo(ScoreboardMapping::class);
    }
}
