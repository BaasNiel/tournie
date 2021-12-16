<?php

namespace Database\Factories;

use App\Models\Game;
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
        return $this->afterCreating(function (Game $game) {
            $this->createPlayersForGame($game);
        });
    }

    private function createPlayersForGame(Game $game)
    {
        // grab 10 random players
        $allPlayers = Player::all()->toArray();
        shuffle($allPlayers);
        $players = array_slice($allPlayers, 0, 10);

        foreach ($players as $index => $player) {
            $game->players()->syncWithoutDetaching([$player['id'] => [
                'player_slot' => $index,
                'hero_id' => $this->faker->numberBetween(1, 100),
                'hero_level' => $this->faker->numberBetween(12, 30),
                'denies' => $this->faker->numberBetween(0, 25),
                'kills' => $this->faker->numberBetween(0, 15),
                'deaths' => $this->faker->numberBetween(0, 15),
                'assists' => $this->faker->numberBetween(0, 15),
                'net_worth' => $this->faker->numberBetween(8000, 50000),
                'gold_per_minute' => $this->faker->numberBetween(200, 1000),
                'xp_per_minute' => $this->faker->numberBetween(300, 800),
                'last_hits' => $this->faker->numberBetween(40, 300),
                'last_hits' => $this->faker->numberBetween(0, 30)
            ]]);
        }
    }
}
