<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamePlayerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_player', function (Blueprint $table) {
            $table->id();
            $table->integer('game_id');
            $table->integer('player_id');
            $table->integer('player_slot');
            $table->integer('hero_id');
            $table->integer('hero_level');
            $table->integer('kills');
            $table->integer('deaths');
            $table->integer('assists');
            $table->integer('net_worth');
            $table->integer('gold_per_minute');
            $table->integer('xp_per_minute');
            $table->integer('last_hits');
            $table->integer('denies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_player');
    }
}
