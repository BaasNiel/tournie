<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoreboardMappingSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scoreboard_mapping_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scoreboard_mapping_id');
            $table->string('key');
            $table->unsignedInteger('offset_x')->comment('Distance from the anchor on X-axis');
            $table->unsignedInteger('offset_y')->comment('Distance from the anchor on Y-axis');
            $table->unsignedInteger('top');
            $table->unsignedInteger('right');
            $table->unsignedInteger('bottom');
            $table->unsignedInteger('left');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->string('text')->comment('Original text when captured');
            $table->timestamps();
            $table->foreign('scoreboard_mapping_id')->references('id')->on('scoreboard_mappings');
            $table->unique(['scoreboard_mapping_id', 'key'], 'scoreboard_mapping_id_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scoreboard_mapping_slots');
    }
}
