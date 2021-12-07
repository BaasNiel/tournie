<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ANCHOR()
 * @method static static RADIANT_WINNER()
 * @method static static RADIANT_SCORE()
 * @method static static DIRE_WINNER()
 * @method static static DIRE_SCORE()
 */
final class ScoreboardSlotKey extends Enum
{
    const ANCHOR            = 'ANCHOR';
    const RADIANT_WINNER    = 'RADIANT_WINNER';
    const RADIANT_SCORE     = 'RADIANT_SCORE';
    const DIRE_WINNER       = 'DIRE_WINNER';
    const DIRE_SCORE        = 'DIRE_SCORE';

    public static function getAll(): array
    {
        return [
            // Anchors
            [
                'key' => self::ANCHOR,
                'title' => 'Anchor (Top Left)',
            ],

            // Radiant
            [
                'key' => self::RADIANT_WINNER,
                'title' => 'Radiant winner label',
            ],
            [
                'key' => self::RADIANT_SCORE,
                'title' => 'Radiant score',
            ],

            // Dire
            [
                'key' => self::DIRE_WINNER,
                'title' => 'Dire winner label',
            ],
            [
                'key' => self::DIRE_SCORE,
                'title' => 'Dire score',
            ],
        ];
    }
}

// ALIAS_AND_CLAN
// HERO_LEVEL
// HERO_NAME
// KILLS
// DEATHS
// ASSISTS
// NET_WORTH
// LAST_HITS
// DENIES
// GPM
