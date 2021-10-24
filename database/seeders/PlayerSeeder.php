<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $playerNames = [
            'Hendrik',
            'Jaco',
            'KwaKwaBooi',
            'Niel',
            'Ryon',
            'Wesley',
            'ХВОСТ',
            'Sue_Hack',
            'Hugo',
            'Jazz'
        ];
        foreach ($playerNames as $playerName) {
            Player::updateOrCreate([
                'slug' => Str::slug($playerName)
            ], [
                'name' => $playerName
            ]);
        }
    }
}
