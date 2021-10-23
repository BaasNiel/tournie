<?php

use App\Enums\HeroField;

$types = [
    HeroField::player => 'playerNames',
    HeroField::clan => 'clanNames',
    HeroField::rank => 'rankCode',
    HeroField::kills => 'int',
    HeroField::deaths => 'int',
    HeroField::assists => 'int',
    HeroField::net_worth => 'netWorth',
    HeroField::last_hits => 'int',
    HeroField::denies => 'int',
    HeroField::level => 'level',
    HeroField::hero_level => 'heroLevel',
];

$columns = [
    // 10
    [
        HeroField::player,
        HeroField::clan,
        HeroField::rank,
        HeroField::kills,
        HeroField::deaths,
        HeroField::assists,
        HeroField::net_worth,
        HeroField::last_hits,
        HeroField::denies,
        HeroField::level,
    ],

    // 10
    // 0 => "Hugo"
    // 1 => "FTza"
    // 2 => "7"
    // 3 => "9"
    // 4 => "20"
    // 5 => "25,692"
    // 6 => "393"
    // 7 => "20"
    // 8 => "13"
    // 9 => "29"
    [
        HeroField::player,
        HeroField::clan,
        HeroField::kills,
        HeroField::deaths,
        HeroField::assists,
        HeroField::net_worth,
        HeroField::last_hits,
        HeroField::denies,
        HeroField::hero_level,
        HeroField::level,
    ],
];

return [
    'columns' => $columns,
    'types' => $types
];
