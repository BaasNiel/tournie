<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GamePlayer extends Pivot
{
    public $incrementing = true;
    protected $guarded = [];
}
