<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoreboardMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scoreboard_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('anchor_text')->unique()->index();
            $table->string('scoreboard_path');
            $table->unsignedInteger('height');
            $table->unsignedInteger('weight');
            $table->unsignedFloat('size_kb');
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
        Schema::dropIfExists('scoreboard_mappings');
    }
}
