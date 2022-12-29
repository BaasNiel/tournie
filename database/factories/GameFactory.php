<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'radiant_win' => $this->faker->boolean(),
            'start_time' => $this->faker->dateTimeThisYear(),
            'duration' => $this->faker->numberBetween(1800, 3600),
            'radiant_score' => $this->faker->numberBetween(10, 50),
            'dire_score' => $this->faker->numberBetween(10, 50)
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Game $game) {
            $this->generatePlayersForGame($game);
        })->afterCreating(function (Game $game) {
            $this->generatePlayersForGame($game);
        });
    }

    private function generatePlayersForGame(Game $game)
    {
        // grab 10 random players
        $allPlayers = Player::all()->toArray();
        shuffle($allPlayers);
        $players = array_slice($allPlayers, 0, 10);

        foreach ($players as $player) {

        }

        $gamePlayer = new GamePlayer();
    }
}
