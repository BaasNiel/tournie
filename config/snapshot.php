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
    HeroField::gpm => 'goldPerMinute',
];

$columns = [
    // 9
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
    ],

    // 9
    [
        HeroField::player,
        HeroField::clan,
        HeroField::kills,
        HeroField::deaths,
        HeroField::assists,
        HeroField::net_worth,
        HeroField::last_hits,
        HeroField::denies,
        HeroField::level,
    ],

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
    [
        HeroField::player,
        HeroField::clan,
        HeroField::kills,
        HeroField::deaths,
        HeroField::assists,
        HeroField::net_worth,
        HeroField::last_hits,
        HeroField::denies,
        HeroField::gpm,
        HeroField::level,
    ],

    // 11
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
        HeroField::gpm,
        HeroField::level,
    ],
];

return [
    'columns' => $columns,
    'types' => $types
];
