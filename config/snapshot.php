<?php

use App\Enums\HeroField;

$ignore = [
    "OVERVIEW",
    "SCOREBOARD",
    "GRAPHS",
    "PERFORMANCE",
    "BREAKDOWNS",
    "NET",
    "ITEMS",
    "ВАСКРАСК",
    "RUN",
    "LH",
    "DN",
    "GPM",
    "BOUN",
    "MVP",
    "TT",
    "K",
    "D",
    "A",
    "|",
    "半",
    "。"
];

$replace = [
    "羊" => "6"
];

$types = [
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
];

$columns = [

    // 8
    [
        HeroField::player,
        HeroField::kills,
        HeroField::deaths,
        HeroField::assists,
        HeroField::net_worth,
        HeroField::last_hits,
        HeroField::denies,
        HeroField::gpm,
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

    // 11
    [
        HeroField::player, // Hugo
        HeroField::clan, // FTza
        HeroField::rank, // E
        HeroField::level, // 29
        HeroField::kills, // 6
        HeroField::deaths, // 8
        HeroField::assists, // 12
        HeroField::net_worth, // 16,572
        HeroField::last_hits, // 78
        HeroField::denies, // 10
        HeroField::gpm, // 712
    ],
];

return [
    'columns' => $columns,
    'types' => $types,
    'ignore' => $ignore,
    'replace' => $replace
];
