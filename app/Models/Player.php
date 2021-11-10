<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function path()
    {
        return route('players.show', $this->id);
    }

    public function name()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
