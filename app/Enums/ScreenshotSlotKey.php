<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ANCHOR()
 * @method static static PLAYER_AND_CLAN()
 * @method static static KILLS()
 */
final class ScreenshotSlotKey extends Enum
{
    const ANCHOR = 'ANCHOR';
    const PLAYER_AND_CLAN = 'PLAYER_AND_CLAN';
    const KILLS = 'KILLS';
}
