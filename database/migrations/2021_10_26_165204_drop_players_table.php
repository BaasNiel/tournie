<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropPlayersTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('players');
    }
}
