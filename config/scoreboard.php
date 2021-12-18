<?php

use App\Enums\HeroField;
use App\Enums\ScoreboardSlotKey;

return [
    'slots' => [
        ScoreboardSlotKey::ANCHOR,
    ],
    'fieldTypes' => [
        HeroField::player => 'playerNames',
        HeroField::clan => 'clanNames',
        HeroField::rank => 'rankCode',
        HeroField::kills => 'kills',
        HeroField::deaths => 'int',
        HeroField::assists => 'int',
        HeroField::net_worth => 'netWorth',
        HeroField::last_hits => 'int',
        HeroField::denies => 'int',
        HeroField::level => 'level',
        HeroField::hero_level => 'heroLevel',
        HeroField::gpm => 'goldPerMinute',
    ]
];
