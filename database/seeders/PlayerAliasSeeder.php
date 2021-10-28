<?php

namespace Database\Seeders;

use App\Models\PlayerAlias;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlayerAliasSeeder extends Seeder
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
            'Jazz',
            'Harker',
            'XBOCT',
            'RyOn',
            '0.0',
        ];
        foreach ($playerNames as $playerName) {
            PlayerAlias::updateOrCreate([
                'slug' => Str::slug($playerName)
            ], [
                'alias' => $playerName
            ]);
        }
    }
}
